// Nav menu (below 1400px): toggle, close on link click, Escape or outside click.
document.addEventListener('DOMContentLoaded', function () {
  var nav = document.querySelector('.lp-nav');
  var toggle = document.querySelector('.lp-menu-toggle');
  if (!nav || !toggle) return;
  function setOpen(open) {
    nav.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    setOpen(!nav.classList.contains('is-open'));
  });
  nav.querySelectorAll('.lp-links a').forEach(function (a) {
    a.addEventListener('click', function () { setOpen(false); });
  });
  document.addEventListener('click', function (e) {
    if (!nav.contains(e.target)) setOpen(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && nav.classList.contains('is-open')) { setOpen(false); toggle.focus(); }
  });
});

// Story cards: arrow buttons scroll one card at a time; disabled at either end.
document.addEventListener('DOMContentLoaded', function () {
  var row = document.getElementById('lpCards');
  var arrows = document.querySelectorAll('.lp-arrow');
  if (!row || !arrows.length) return;

  function step() {
    var card = row.querySelector('.lp-card');
    var gap = parseFloat(getComputedStyle(row).columnGap) || 0;
    return card ? card.getBoundingClientRect().width + gap : row.clientWidth;
  }
  function update() {
    var max = row.scrollWidth - row.clientWidth - 1;
    arrows[0].disabled = row.scrollLeft <= 0;
    arrows[1].disabled = row.scrollLeft >= max;
    // All cards visible (desktop): the arrows have nothing to do, so hide them
    arrows[0].parentElement.classList.toggle('is-idle', max <= 0);
  }
  arrows.forEach(function (btn) {
    btn.addEventListener('click', function () {
      row.scrollBy({ left: step() * parseInt(btn.dataset.dir, 10), behavior: 'smooth' });
    });
  });
  row.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);
  update();
});

// Motion: pause/play button for all photo animation (remembered per visitor),
// plus automatic pausing of each animated section while it is off screen.
document.addEventListener('DOMContentLoaded', function () {
  var btn = document.querySelector('.lp-motion-toggle');
  var root = document.documentElement;
  function setPaused(paused) {
    root.classList.toggle('motion-paused', paused);
    if (btn) {
      btn.setAttribute('aria-pressed', paused ? 'true' : 'false');
      btn.setAttribute('aria-label', paused ? 'Play photo animation' : 'Pause photo animation');
    }
    try { localStorage.setItem('lpMotionPaused', paused ? '1' : '0'); } catch (e) {}
  }
  var saved = null;
  try { saved = localStorage.getItem('lpMotionPaused'); } catch (e) {}
  if (saved === '1') setPaused(true);
  if (btn) btn.addEventListener('click', function () { setPaused(!root.classList.contains('motion-paused')); });

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { en.target.classList.toggle('is-offscreen', !en.isIntersecting); });
    }, { rootMargin: '80px' });
    document.querySelectorAll('.lp-hero, .lp-impact').forEach(function (el) { io.observe(el); });
  }
});