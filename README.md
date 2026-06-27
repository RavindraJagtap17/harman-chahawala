# Harman Chahawala — Website

Multi-page, mobile-friendly, SEO-ready website for the Harman Chahawala tea franchise brand.

## File structure
```
harman-website/
├── index.html         Home
├── about.html         About / founder story
├── products.html      Product catalogue
├── franchise.html     Franchise plans + earnings + FAQ
├── contact.html       Enquiry form (CAPTCHA + honeypot)
├── privacy.html       Privacy policy
├── terms.html         Terms & conditions
├── 404.html           Branded error page
├── sitemap.xml        XML sitemap (7 URLs)
├── robots.txt         Allows all crawlers + AI bots
├── .htaccess          HTTPS, www, gzip, caching, security headers
├── css/style.css      Single stylesheet
├── js/main.js         Mobile menu, captcha, form, scroll reveal
├── php/enquiry.php    Server-side form handler → info@harmanchahawala.com
└── images/            logo, mascot, hero & founder photos
```

## Deployment

### 1. Upload
Upload the entire `harman-website/` folder contents to your web host's public root (often `public_html/` or `www/`). Make sure `.htaccess` is uploaded — it's a hidden file.

### 2. PHP requirement
The enquiry form uses PHP (`php/enquiry.php`). Your host must support PHP 7.4+ with the `mail()` function enabled. Most shared hosts (Hostinger, Bluehost, GoDaddy, BigRock, HostGator) support this by default.

### 3. Form delivery — recommended upgrade
The current `enquiry.php` uses PHP's built-in `mail()` which can land in spam. For reliable delivery to **info@harmanchahawala.com**, replace it with one of these:

- **PHPMailer + SMTP** (free, recommended). Use SMTP credentials from your hosting provider, Gmail, or a service like Brevo/Mailgun.
- **Formspree** or **Web3Forms** (no backend). Change the form `action` in `contact.html` and remove the PHP file. Both have free tiers.

### 4. CAPTCHA — recommended upgrade
The current CAPTCHA is a custom client-side 6-character challenge plus a honeypot trap. For production, replacing it with **Google reCAPTCHA v3** or **hCaptcha** is recommended. Drop their `<script>` and site key into `contact.html` and validate the token in `enquiry.php`.

### 5. Search Console
After going live:
1. Add `https://www.harmanchahawala.com` as a property in [Google Search Console](https://search.google.com/search-console).
2. Submit `https://www.harmanchahawala.com/sitemap.xml`.
3. Repeat in [Bing Webmaster Tools](https://www.bing.com/webmasters).

### 6. Analytics (optional)
Add Google Analytics 4 / Plausible / Microsoft Clarity by inserting their snippet just before `</head>` in each HTML file.

## SEO highlights
- **Targeted keywords** baked into titles/meta/H1: tea franchise cost, chai franchise cost, tea franchise in Maharashtra / Gujarat / Goa / Madhya Pradesh.
- **Schema markup** on every page: Organization, Service (FranchiseOpportunity), WebSite, AboutPage, Menu, FAQPage, ContactPage, BreadcrumbList.
- **All AI/LLM crawlers explicitly allowed** in `robots.txt` (GPTBot, ClaudeBot, PerplexityBot, Google-Extended, etc.).
- Geo meta tags pointing to Sangli, Maharashtra.
- Open Graph + Twitter Card tags on every page.

## Form fields delivered to your inbox
Every enquiry email to `info@harmanchahawala.com` includes: full name, phone, email, city, state, investment range, model preference (Plug N Play / DIY), message, IP, user-agent, timestamp. The enquirer also gets an automatic acknowledgement reply.

## Browser support
Tested patterns work on all modern browsers including iOS Safari and Android Chrome. Mobile menu kicks in below 980px.

---
**Brand contact:** Harman Chahawala · V' Heights Apt 2nd Floor, Vishrambag, Sangli — 416415, Maharashtra · +91 91171 51715 · info@harmanchahawala.com
