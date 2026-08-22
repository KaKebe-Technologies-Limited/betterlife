document.addEventListener('DOMContentLoaded', function () {
  // Mobile nav toggle
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { nav.classList.remove('open'); });
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

  // Sticky header shadow
  var header = document.querySelector('.site-header');
  window.addEventListener('scroll', function () {
    if (!header) return;
    header.style.boxShadow = window.scrollY > 10 ? '0 6px 20px rgba(11,61,46,.08)' : 'none';

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

  // Simple client-side validation feedback (native HTML5 required already handles most)
});
