<?php
/**
 * Harman Chahawala — Franchise Enquiry Handler
 * --------------------------------------------
 * Receives the enquiry form POST, validates, sanitises, applies anti-spam
 * checks (honeypot + simple rate-limiting via session) and emails the lead
 * to info@harmanchahawala.com.
 *
 * IMPORTANT: This script requires the server's PHP to have a working `mail()`
 * function configured (sendmail/SMTP). On most cPanel/VPS hosts this works
 * out of the box. For better deliverability, replace the mail() call below
 * with PHPMailer + SMTP credentials of your domain provider.
 */

declare(strict_types=1);

// ------ CONFIG ------
$RECIPIENT_EMAIL = 'info@harmanchahawala.com';
$SITE_NAME       = 'Harman Chahawala';
$ALLOWED_ORIGIN  = 'https://www.harmanchahawala.com'; // change for staging if needed

header('Content-Type: application/json; charset=utf-8');

// CORS / origin check (loose — accepts same-origin browser POSTs too)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && strpos($origin, 'harmanchahawala') === false && strpos($origin, 'localhost') === false) {
    http_response_code(403);
    echo json_encode(['error' => 'Origin not allowed']);
    exit;
}
header('Access-Control-Allow-Origin: ' . ($origin ?: $ALLOWED_ORIGIN));
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ------ Anti-spam: rate limit by IP ------
session_start();
$ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key  = 'last_submit_' . md5($ip);
$now  = time();
$last = $_SESSION[$key] ?? 0;
if ($now - $last < 30) {
    http_response_code(429);
    echo json_encode(['error' => 'Please wait a moment before submitting again.']);
    exit;
}

// ------ Honeypot ------
if (!empty($_POST['website'] ?? '')) {
    // pretend success for bots
    echo json_encode(['ok' => true]);
    exit;
}

// ------ Required fields ------
$required = ['name', 'email', 'phone', 'state', 'city', 'captchaInput'];
foreach ($required as $f) {
    if (empty(trim((string)($_POST[$f] ?? '')))) {
        http_response_code(400);
        echo json_encode(['error' => 'Please fill all required fields.']);
        exit;
    }
}

// ------ Sanitise ------
function clean(string $s): string {
    $s = trim($s);
    $s = strip_tags($s);
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$name       = clean($_POST['name']);
$email      = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$phone      = clean($_POST['phone']);
$state      = clean($_POST['state']);
$city       = clean($_POST['city']);
$investment = clean($_POST['investment'] ?? '');
$model      = clean($_POST['model'] ?? '');
$message    = clean($_POST['message'] ?? '');
$captcha    = strtoupper(trim((string)$_POST['captchaInput']));

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Please provide a valid email address.']);
    exit;
}

// Phone basic check
if (!preg_match('/^[0-9+\s\-()]{7,20}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please provide a valid phone number.']);
    exit;
}

// CAPTCHA basic length check (the JS captcha is client-side; for hardened
// security, replace with Google reCAPTCHA / hCaptcha — see notes below)
if (strlen($captcha) < 4 || strlen($captcha) > 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Verification code is invalid.']);
    exit;
}

// ------ Build email ------
$subject = "New Franchise Enquiry from {$name} ({$city}, {$state}) — {$SITE_NAME}";

$bodyHtml = <<<HTML
<!doctype html>
<html><body style="font-family:Arial,sans-serif; color:#222; line-height:1.55;">
  <div style="max-width:640px;margin:0 auto;padding:20px;border:1px solid #eee;border-radius:8px;">
    <h2 style="color:#6b1410;margin:0 0 6px;">New Franchise Enquiry</h2>
    <p style="color:#888;margin:0 0 18px;">Received via harmanchahawala.com</p>
    <table cellpadding="8" cellspacing="0" style="border-collapse:collapse; width:100%; font-size:14px;">
      <tr><td style="background:#f7efe1;width:160px;"><strong>Name</strong></td><td>{$name}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>Email</strong></td><td>{$email}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>Phone</strong></td><td>{$phone}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>City</strong></td><td>{$city}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>State</strong></td><td>{$state}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>Investment Range</strong></td><td>{$investment}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>Preferred Model</strong></td><td>{$model}</td></tr>
      <tr><td style="background:#f7efe1; vertical-align:top;"><strong>Message</strong></td>
          <td>{$message}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>IP</strong></td><td>{$ip}</td></tr>
      <tr><td style="background:#f7efe1;"><strong>Time</strong></td><td>{$lateStamp}</td></tr>
    </table>
    <p style="margin-top:24px; font-size:12px; color:#888;">Reply directly to this email to respond to the lead.</p>
  </div>
</body></html>
HTML;

// We need to inject server time after HEREDOC builds — simpler: recompute & str_replace
$bodyHtml = str_replace('{$lateStamp}', date('Y-m-d H:i:s'), $bodyHtml);

// Plain text fallback
$bodyText = "NEW FRANCHISE ENQUIRY\n----------------------\n"
          . "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n"
          . "City: {$city}\nState: {$state}\n"
          . "Investment: {$investment}\nModel: {$model}\n"
          . "Message:\n{$message}\n\n"
          . "IP: {$ip}\nTime: " . date('Y-m-d H:i:s') . "\n";

// Build multi-part mail headers
$boundary = md5((string)random_int(0, PHP_INT_MAX));
$headers  = [];
$headers[] = "From: {$SITE_NAME} <noreply@harmanchahawala.com>";
$headers[] = "Reply-To: {$name} <{$email}>";
$headers[] = "X-Mailer: PHP/" . PHP_VERSION;
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

$emailBody  = "--{$boundary}\r\n";
$emailBody .= "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n";
$emailBody .= $bodyText . "\r\n\r\n";
$emailBody .= "--{$boundary}\r\n";
$emailBody .= "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n";
$emailBody .= $bodyHtml . "\r\n\r\n";
$emailBody .= "--{$boundary}--";

// Send
$sent = @mail($RECIPIENT_EMAIL, $subject, $emailBody, implode("\r\n", $headers));

if (!$sent) {
    // fallback: log to a file the server admin can monitor
    $log = __DIR__ . '/enquiries.log';
    @file_put_contents($log, "[" . date('c') . "] " . $bodyText . "\n----\n", FILE_APPEND | LOCK_EX);
    http_response_code(500);
    echo json_encode(['error' => 'Could not send email. Please call us at +91 91171 51715.']);
    exit;
}

$_SESSION[$key] = $now;

// Auto-acknowledgement to the enquirer (optional but nice)
$ackSubject = "We received your franchise enquiry — Harman Chahawala";
$ackBody = "Hi {$name},\n\nThank you for your interest in the Harman Chahawala tea franchise.\n\n"
         . "Our franchise team will review your enquiry and get back to you within 24 hours.\n\n"
         . "For anything urgent, please call +91 91171 51715.\n\n"
         . "Warm regards,\nHarman Chahawala — Franchise Team\nwww.harmanchahawala.com";
$ackHeaders = "From: {$SITE_NAME} <info@harmanchahawala.com>\r\nReply-To: info@harmanchahawala.com\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8";
@mail($email, $ackSubject, $ackBody, $ackHeaders);

echo json_encode(['ok' => true, 'message' => 'Enquiry received successfully.']);
exit;
