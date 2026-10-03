// Home page: the film inside the map, the five area panels and the country highlights.
// Reveals, counters and the film player come from about.js and programmes.js.
(function () {
  'use strict';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', function () {
    initHeroFilm();
    initPanels();
    initMap();
  });

  /* Silent loop inside the outline of Africa: loads after the page on wider screens, never with
     reduced motion or data saving; can be paused (remembered with the other pages' films). */
  function initHeroFilm() {
    var art = document.querySelector('.hm-art');
    var video = art && art.querySelector('.hm-window-video');
    var btn = art && art.querySelector('.hm-motion');
    if (!video || !btn) return;
    var saveData = navigator.connection && navigator.connection.saveData;
    if (reduceMotion || saveData || !window.matchMedia('(min-width: 720px)').matches) return;

    var root = document.documentElement, offscreen = false;
    var paused = function () { return root.classList.contains('ab-motion-paused'); };
    function sync() {
      if (!video.getAttribute('src')) return;
      if (paused() || offscreen) video.pause(); else video.play().catch(function () {});
    }
    function setPaused(p) {
      root.classList.toggle('ab-motion-paused', p);
      btn.setAttribute('aria-pressed', p ? 'true' : 'false');
      btn.setAttribute('aria-label', p ? 'Play the film' : 'Pause the film');
      try { localStorage.setItem('abMotionPaused', p ? '1' : '0'); } catch (e) {}
      sync();
    }
    try { if (localStorage.getItem('abMotionPaused') === '1') setPaused(true); } catch (e) {}
    btn.addEventListener('click', function () { setPaused(!paused()); });
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { offscreen = !en.isIntersecting; sync(); });
      }).observe(art);
    }
    function start() {
      video.addEventListener('playing', function () { art.classList.add('has-video'); btn.hidden = false; }, { once: true });
      video.src = video.getAttribute('data-src');
      sync();
    }
    if (document.readyState === 'complete') setTimeout(start, 300); else window.addEventListener('load', function () { setTimeout(start, 300); });
  }

  /* Five areas: the panel pointed at or focused opens (wide screens with a mouse; see home.css) */
  function initPanels() {
    var root = document.querySelector('[data-panels]');
    if (!root) return;
    var items = root.querySelectorAll('.hm-panel'), current = 0;
    function show(i) {
      if (i === current) return;
      current = i;
      items.forEach(function (li, k) { li.classList.toggle('is-on', k === i); });
    }
    items.forEach(function (li, k) {
      li.addEventListener('mouseenter', function () { show(k); });
      li.addEventListener('focusin', function () { show(k); });
    });
  }

  /* Where we work: pointing at a country in the list or on the map lights up both */
  function initMap() {
    var map = document.querySelector('[data-map]');
    var list = document.querySelector('.hm-countries-list');
    if (!map || !list) return;
    var rows = list.querySelectorAll('li[data-key]');
    function highlight(key) {
      map.querySelectorAll('[data-key]').forEach(function (el) { el.classList.toggle('is-hl', el.getAttribute('data-key') === key); });
      rows.forEach(function (li) { li.classList.toggle('is-hl', li.getAttribute('data-key') === key); });
    }
    rows.forEach(function (li) { li.addEventListener('mouseenter', function () { highlight(li.getAttribute('data-key')); }); });
    list.addEventListener('mouseleave', function () { highlight(null); });
    map.querySelectorAll('.hm-map-countries path').forEach(function (p) {
      p.addEventListener('mouseenter', function () { highlight(p.getAttribute('data-key')); });
      p.addEventListener('mouseleave', function () { highlight(null); });
    });
  }
})();
