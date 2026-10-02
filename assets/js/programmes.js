// Programmes pages: photo strip controls. Reveals, the photo viewer and
// "Read more" toggles come from about.js, which these pages also load.
(function () {
  'use strict';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-strip]').forEach(initStrip);
    initHeroFilm();
  });

  /* Opening film: one shot per programme area; the index along the hero follows the shot on screen.
     Loads after the page on wider screens, never with reduced motion or data saving; can be paused. */
  function initHeroFilm() {
    var hero = document.querySelector('.pg-hero.has-index');
    var video = hero && hero.querySelector('.pg-hero-video');
    var btn = hero && hero.querySelector('.pg-film-toggle');
    if (!video || !btn) return;
    var saveData = navigator.connection && navigator.connection.saveData;
    if (reduceMotion || saveData || !window.matchMedia('(min-width: 720px)').matches) return;

    var root = document.documentElement;
    var links = hero.querySelectorAll('.pg-hero-index a');
    var cues = (video.getAttribute('data-cues') || '').split(',').map(Number);
    var offscreen = false, current = -1;
    var paused = function () { return root.classList.contains('ab-motion-paused'); };

    function mark() {
      var t = video.currentTime, i = 0;
      for (var k = 0; k < cues.length; k++) if (t >= cues[k]) i = k;
      if (i === current) return;
      current = i;
      links.forEach(function (a) { a.classList.toggle('is-active', Number(a.getAttribute('data-i')) === i); });
    }
    function sync() {
      if (!video.getAttribute('src')) return;
      if (paused() || offscreen) video.pause(); else video.play().catch(function () {});
    }
    function setPaused(p) {
      root.classList.toggle('ab-motion-paused', p);
      btn.setAttribute('aria-pressed', p ? 'true' : 'false');
      btn.setAttribute('aria-label', p ? 'Play background film' : 'Pause background film');
      try { localStorage.setItem('abMotionPaused', p ? '1' : '0'); } catch (e) {}
      sync();
    }
    try { if (localStorage.getItem('abMotionPaused') === '1') setPaused(true); } catch (e) {}
    btn.addEventListener('click', function () { setPaused(!paused()); });
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { offscreen = !en.isIntersecting; sync(); });
      }).observe(hero);
    }
    video.addEventListener('timeupdate', mark);
    video.addEventListener('seeked', function () { current = -1; mark(); });

    function start() {
      video.addEventListener('playing', function () { hero.classList.add('has-video'); btn.hidden = false; mark(); }, { once: true });
      var hd = video.getAttribute('data-src-hd');
      video.src = hd && hero.offsetWidth * (window.devicePixelRatio || 1) > 1400 ? hd : video.getAttribute('data-src');
      sync();
    }
    if (document.readyState === 'complete') setTimeout(start, 300); else window.addEventListener('load', function () { setTimeout(start, 300); });
  }

  /* Previous / next buttons for a sideways photo strip, disabled at either end */
  function initStrip(root) {
    var row = root.querySelector('.pg-strip-row');
    var nav = root.querySelector('.pg-strip-nav');
    if (!row || !nav) return;
    var btns = nav.querySelectorAll('.pg-round');
    function update() {
      var max = row.scrollWidth - row.clientWidth - 2;
      nav.hidden = max <= 0;
      btns[0].disabled = row.scrollLeft <= 2;
      btns[1].disabled = row.scrollLeft >= max;
    }
    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        row.scrollBy({ left: row.clientWidth * 0.75 * Number(b.getAttribute('data-dir')), behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    });
    row.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  }
})();
