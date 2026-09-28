document.addEventListener('DOMContentLoaded', function () {
  // Mobile nav: off-canvas panel with backdrop, close button and Escape
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  var backdrop = document.getElementById('navBackdrop');
  function setNavOpen(open) {
    nav.classList.toggle('open', open);
    document.body.classList.toggle('nav-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (backdrop) backdrop.hidden = !open;
  }
  if (toggle && nav) {
    toggle.addEventListener('click', function () { setNavOpen(!nav.classList.contains('open')); });
    var closeBtn = nav.querySelector('.nav-close');
    if (closeBtn) closeBtn.addEventListener('click', function () { setNavOpen(false); });
    if (backdrop) backdrop.addEventListener('click', function () { setNavOpen(false); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('open')) setNavOpen(false);
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { setNavOpen(false); });
    });
  }

  // Quantity steppers (+/-)
  document.querySelectorAll('.qty-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = btn.parentElement.querySelector('input[type=number]');
      var step = parseInt(btn.getAttribute('data-step'), 10);
      var next = Math.max(1, (parseInt(input.value, 10) || 1) + step);
      input.value = next;
    });
  });

  // "Our Work" nav dropdown
  document.querySelectorAll('.nav-dropdown-toggle').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var dd = btn.closest('.nav-dropdown');
      var wasOpen = dd.classList.contains('open');
      document.querySelectorAll('.nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
      if (!wasOpen) dd.classList.add('open');
    });
  });
  document.addEventListener('click', function () {
    document.querySelectorAll('.nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
  });

  // Sticky header shadow (menu band on desktop, masthead on mobile — CSS picks)
  var stickyBars = document.querySelectorAll('.site-header, .nav-bar');
  window.addEventListener('scroll', function () {
    stickyBars.forEach(function (bar) { bar.classList.toggle('scrolled', window.scrollY > 10); });

    var btt = document.querySelector('.back-to-top');
    if (btt) btt.classList.toggle('show', window.scrollY > 500);
  });

  // Back to top
  var btt = document.querySelector('.back-to-top');
  if (btt) {
    btt.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Animated counters
  var counters = document.querySelectorAll('[data-count]');
  if (counters.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        var raw = el.getAttribute('data-count');
        var match = raw.match(/([\d,.]+)/);
        if (!match) return;
        var target = parseFloat(match[1].replace(/,/g, ''));
        var suffix = raw.replace(match[1], '');
        var current = 0;
        var step = Math.max(target / 60, 1);
        var isInt = Number.isInteger(target);
        var timer = setInterval(function () {
          current += step;
          if (current >= target) {
            current = target;
            clearInterval(timer);
          }
          el.textContent = (isInt ? Math.floor(current).toLocaleString() : current.toFixed(1)) + suffix;
        }, 20);
        observer.unobserve(el);
      });
    }, { threshold: 0.4 });
    counters.forEach(function (c) { observer.observe(c); });
  }

  // Fade-up reveal on scroll
  var reveals = document.querySelectorAll('.fade-up');
  if (reveals.length) {
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    reveals.forEach(function (el) { revealObserver.observe(el); });
  }

  // Team/board member bio modal
  var memberModal = document.getElementById('memberModal');
  if (memberModal && window.__teamData) {
    var openBioCard = function (card) {
      var m = window.__teamData[card.getAttribute('data-member-id')];
      if (!m) return;
      document.getElementById('modalPhoto').src = m.photo;
      document.getElementById('modalPhoto').alt = m.name;
      document.getElementById('modalName').textContent = m.name;
      document.getElementById('modalRole').textContent = m.role;
      document.getElementById('modalBio').textContent = m.bio;
      memberModal.classList.add('open');
      memberModal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };
    var closeBioModal = function () {
      memberModal.classList.remove('open');
      memberModal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };
    document.querySelectorAll('.js-open-bio').forEach(function (card) {
      card.addEventListener('click', function () { openBioCard(card); });
      card.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openBioCard(card); }
      });
    });
    memberModal.querySelectorAll('[data-close-modal]').forEach(function (el) {
      el.addEventListener('click', closeBioModal);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeBioModal();
    });
  }

  // Homepage hero: fetch the admin-managed photo gallery over AJAX and
  // rotate the photo that breaks over the Africa artwork's edge. It fades
  // to 0, swaps its src once fully hidden, then fades back in — so a slow
  // connection just shows the static server-rendered photo for longer,
  // never a broken image.
  var heroPhoto = document.getElementById('heroOverlapPhoto');

  if (heroPhoto && window.fetch) {
    fetch('hero-gallery.php')
      .then(function (res) { return res.ok ? res.json() : []; })
      .then(function (photos) {
        photos = (photos || []).filter(function (p) { return p && p.src; });
        if (photos.length < 2) return; // not enough variety to rotate; keep the default

        photos.forEach(function (p) { var pre = new Image(); pre.src = p.src; });

        var index = 0;
        setInterval(function () {
          index = (index + 1) % photos.length;
          var next = photos[index];
          heroPhoto.style.opacity = '0';
          setTimeout(function () {
            heroPhoto.setAttribute('src', next.src);
            heroPhoto.alt = next.alt || heroPhoto.alt;
            heroPhoto.style.opacity = '1';
          }, 500);
        }, 5000);
      })
      .catch(function () { /* keep the three default portraits already rendered */ });
  }
});
