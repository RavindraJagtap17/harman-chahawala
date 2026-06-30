<?php
/**
 * Thank You page — only reachable right after a successful enquiry submit.
 * enquiry.php sets $_SESSION['form_submitted'] = true on success.
 * This page checks that flag, then immediately clears it so the page
 * cannot be revisited via refresh, back button, or direct URL.
 */
session_start();

if (empty($_SESSION['form_submitted'])) {
    header('Location: contact.html');
    exit;
}

// One-time use — clear immediately so refresh/back/direct URL won't work again
unset($_SESSION['form_submitted']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Thank You | Harman Chahawala</title>
  <link rel="icon" type="image/png" href="images/favicon.png">
  <link rel="apple-touch-icon" href="images/favicon.png">
  <meta name="robots" content="noindex, nofollow">

  <link rel="icon" type="image/png" href="images/logo.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <a href="#main" class="skip-link">Skip to main content</a>

  <div class="topbar">
    <div class="container">
      <div class="left">
        <span>📍 Sangli, Maharashtra</span>
        <a href="mailto:info@harmanchahawala.com">✉ info@harmanchahawala.com</a>
      </div>
      <div class="right">
        <a href="tel:+919117151715"><strong>📞 +91 91171 51715</strong></a>
      </div>
    </div>
  </div>

  <header class="header">
    <div class="container">
      <nav class="nav" aria-label="Primary">
        <a class="brand" href="index.html"><img src="images/512x512_logo harmanpng.png" class="logo"
            alt="Harman Chahawala logo" width="56" height="56">
        </a>
        <ul class="menu" id="primaryMenu">
          <li><a href="index.html">Home</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="products.html">Products</a></li>
          <li><a href="franchise.html">Franchise</a></li>
          <li><a href="contact.html">Contact</a></li>
          <li><a href="contact.html" class="btn btn-primary">Apply Now</a></li>
        </ul>
        <button class="menu-toggle" aria-label="Toggle menu" aria-expanded="false"
          aria-controls="primaryMenu"><span></span><span></span><span></span></button>
      </nav>
    </div>
  </header>

  <main id="main">
    <section class="section" style="min-height:60vh; display:flex; align-items:center;">
      <div class="container narrow" style="text-align:center;">

        <div style="width:96px;height:96px;border-radius:50%;background:var(--cream);border:3px solid var(--saffron);
                    display:flex;align-items:center;justify-content:center;margin:0 auto 28px;">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--maroon-dark)" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6L9 17l-5-5"></path>
          </svg>
        </div>

        <span class="section-eyebrow">Enquiry Received</span>
        <h1 style="color:var(--maroon-dark); margin:10px 0 18px;">Thank You!</h1>
        <p style="color:var(--soft-ink); font-size:1.05rem; max-width:520px; margin:0 auto 32px;">
          Your franchise enquiry has been received successfully. Our team will get
          in touch with you within 24 hours with a complete proposal.
        </p>

        <a href="index.html" class="btn btn-primary">Back to Home</a>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="footer-brand"><img src="images/512x512_logo harmanpng.png" class="footer-logo" alt="Harman
              Chahawala logo">
            <div class="footer-name">Harman Chahawala</div>
          </div>
          <p style="font-size:.92rem; line-height:1.6;">India's most loved tea franchise — building entrepreneurs and
            transforming lives, one outlet at a time.</p>
          <div class="social-links">
            <a href="https://www.facebook.com/harmanchahawala" aria-label="Facebook" rel="noopener">f</a>
            <a href="https://www.instagram.com/harmanchahawala" aria-label="Instagram" rel="noopener">◉</a>
            <a href="https://www.youtube.com/@harmanchahawala" aria-label="YouTube" rel="noopener">▶</a>
          </div>
        </div>
        <div>
          <h4>Quick Links</h4>
          <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About Us</a></li>
            <li><a href="products.html">Products</a></li>
            <li><a href="franchise.html">Franchise</a></li>
            <li><a href="contact.html">Contact</a></li>
          </ul>
        </div>
        <div>
          <h4>Franchise In</h4>
          <ul>
            <li><a href="franchise.html">Maharashtra</a></li>
            <li><a href="franchise.html">Gujarat</a></li>
            <li><a href="franchise.html">Goa</a></li>
            <li><a href="franchise.html">Madhya Pradesh</a></li>
            <li><a href="contact.html">All Other States</a></li>
          </ul>
        </div>
        <div>
          <h4>Contact</h4>
          <ul>
            <li><a href="tel:+919117151715">📞 +91 91171 51715</a></li>
            <li><a href="mailto:info@harmanchahawala.com">✉ info@harmanchahawala.com</a></li>
            <li>📍 Vishrambag, Sangli — 416415</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <div>© <span id="year"></span> Harman Chahawala. All rights reserved.</div>
        <div><a href="sitemap.xml">Sitemap</a> · <a href="privacy.html">Privacy</a> · <a href="terms.html">Terms</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="js/main.js"></script>
</body>

</html>