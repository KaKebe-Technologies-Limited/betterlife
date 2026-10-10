// Programmes pages: photo strip controls. Reveals, the photo viewer and
// "Read more" toggles come from about.js, which these pages also load.
(function () {
  'use strict';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-strip]').forEach(initStrip);
    document.querySelectorAll('[data-atlas]').forEach(initAtlas);
    initHeroFilm();
    initFilmDialog();
  });

  /* Programme areas: the large photograph follows the area pointed at, focused or linked to (#area) */
  function initAtlas(root) {
    var items = root.querySelectorAll('.pg-atlas-item');
    var slides = root.querySelectorAll('.pg-atlas-slide');
    if (!items.length || items.length !== slides.length) return;
    var current = 0;
    function show(i) {
      if (i === current) return;
      current = i;
      items.forEach(function (li, k) { li.classList.toggle('is-on', k === i); });
      slides.forEach(function (s, k) { s.classList.toggle('is-on', k === i); });
    }
    items.forEach(function (li, k) {
      li.addEventListener('mouseenter', function () { show(k); });
      li.addEventListener('focusin', function () { show(k); });
    });
    function fromHash() {
      var id = decodeURIComponent(location.hash.slice(1));
      items.forEach(function (li, k) { if (id && li.id === id) show(k); });
    }
    window.addEventListener('hashchange', fromHash);
    fromHash();
  }

  /* The Yumbe film opens in a player dialog; nothing downloads until play is pressed */
  function initFilmDialog() {
    var dlg = document.getElementById('pgFilmDialog');
    var buttons = document.querySelectorAll('[data-film]');
    if (!dlg || !buttons.length || typeof dlg.showModal !== 'function') return;
    var video = dlg.querySelector('video'), close = dlg.querySelector('.pg-film-close'), opener = null;
    var heroFilm = document.querySelector('.pg-hero-video, .hm-window-video'), heroWasPlaying = false;
    // Phones, data saving and slow connections get the lighter copy of a film when there is one
    var conn = navigator.connection || {};
    var light = conn.saveData || /(^|-)(2g|3g)$/.test(conn.effectiveType || '') || window.matchMedia('(max-width: 720px)').matches;
    buttons.forEach(function (b) {
      b.addEventListener('click', function () {
        opener = b;
        var src = (light && b.getAttribute('data-film-small')) || b.getAttribute('data-film');
        if (video.getAttribute('src') !== src) video.src = src;
        video.setAttribute('aria-label', b.getAttribute('data-film-title') || 'Film');
        if (heroFilm) { heroWasPlaying = !heroFilm.paused; heroFilm.pause(); }
        dlg.showModal();
        video.play().catch(function () {});
        close.focus();
      });
    });
    close.addEventListener('click', function () { dlg.close(); });
    dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
    dlg.addEventListener('close', function () {
      video.pause();
      if (heroFilm && heroWasPlaying && !document.documentElement.classList.contains('ab-motion-paused')) heroFilm.play().catch(function () {});
      if (opener) opener.focus();
    });
  }

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
