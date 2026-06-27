/* Harman Chahawala — Site JS */

// Mobile menu toggle
(function () {
  const toggle = document.querySelector('.menu-toggle');
  const menu = document.querySelector('.menu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => {
      toggle.classList.toggle('open');
      menu.classList.toggle('open');
      const expanded = toggle.classList.contains('open');
      toggle.setAttribute('aria-expanded', expanded);
    });
    // Close on link click (mobile)
    menu.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        toggle.classList.remove('open');
        menu.classList.remove('open');
      });
    });
  }
})();

// Reveal on scroll
(function () {
  const els = document.querySelectorAll('.reveal');
  if (!('IntersectionObserver' in window) || els.length === 0) {
    els.forEach(e => e.classList.add('in'));
    return;
  }
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('in');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  els.forEach(el => io.observe(el));
})();

// Sticky header shadow on scroll
(function () {
  const header = document.querySelector('.header');
  if (!header) return;
  const onScroll = () => {
    if (window.scrollY > 12) header.style.boxShadow = '0 8px 28px rgba(74,14,10,.10)';
    else header.style.boxShadow = '';
  };
  window.addEventListener('scroll', onScroll, { passive: true });
})();

// Active nav link based on current page
(function () {
  const path = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.menu a').forEach(a => {
    const href = a.getAttribute('href');
    if (href === path || (path === '' && href === 'index.html')) {
      a.classList.add('active');
    }
  });
})();

// CAPTCHA — simple math/text captcha (client-side)
(function () {
  const captchaDisplay = document.getElementById('captchaDisplay');
  const captchaInput = document.getElementById('captchaInput');
  const captchaRefresh = document.getElementById('captchaRefresh');
  const form = document.getElementById('enquiryForm');
  if (!captchaDisplay || !form) return;

  function generateCaptcha() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    let code = '';
    for (let i = 0; i < 6; i++) {
      code += chars[Math.floor(Math.random() * chars.length)];
    }
    captchaDisplay.textContent = code;
    captchaDisplay.dataset.code = code;
    if (captchaInput) captchaInput.value = '';
  }

  generateCaptcha();
  if (captchaRefresh) captchaRefresh.addEventListener('click', generateCaptcha);

  // Hidden honeypot — bots tend to fill all fields
  // The form has a `website` field that should be empty (display:none)

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('formMessage');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Honeypot check
    const hp = form.querySelector('input[name="website"]');
    if (hp && hp.value.trim() !== '') {
      // Silently "succeed" for bots
      msg.className = 'form-message success';
      msg.textContent = 'Thank you. Your enquiry has been received.';
      form.reset();
      return;
    }

    // Captcha check
    const expected = captchaDisplay.dataset.code;
    const provided = (captchaInput.value || '').trim().toUpperCase();
    if (provided !== expected) {
      msg.className = 'form-message error';
      msg.textContent = 'Verification code is incorrect. Please try again.';
      generateCaptcha();
      return;
    }

    // Validate required fields manually for nicer UX
    const required = ['name', 'email', 'phone', 'state', 'city'];
    for (const fieldName of required) {
      const el = form.elements[fieldName];
      if (!el || !el.value.trim()) {
        msg.className = 'form-message error';
        msg.textContent = 'Please fill all required fields marked with *';
        if (el) el.focus();
        return;
      }
    }

    // Email format basic check
    const email = form.elements['email'].value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      msg.className = 'form-message error';
      msg.textContent = 'Please enter a valid email address.';
      form.elements['email'].focus();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    try {
      const formData = new FormData(form);
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
      });

      if (response.ok) {
        msg.className = 'form-message success';
        msg.textContent = 'Thank you! Your franchise enquiry has been received. Our team will reach out within 24 hours.';
        form.reset();
        generateCaptcha();
        // Scroll into view
        msg.scrollIntoView({ behavior: 'smooth', block: 'center' });
      } else {
        let errorText = 'Something went wrong. Please call us at 91171 51715.';
        try {
          const data = await response.json();
          if (data && data.error) errorText = data.error;
        } catch (_) {}
        msg.className = 'form-message error';
        msg.textContent = errorText;
      }
    } catch (err) {
      msg.className = 'form-message error';
      msg.textContent = 'Network error. Please call us at 91171 51715 or email info@harmanchahawala.com.';
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit Enquiry';
    }
  });
})();

// Year in footer
(function () {
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();
