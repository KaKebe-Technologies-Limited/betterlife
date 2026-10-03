// Home page: the film inside the map, the five area panels and the country highlights.
// Reveals, counters and the film player come from about.js and programmes.js.
(function () {
  'use strict';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', function () {
    initHeroFilm();
    initPanels();
    initLoop();
    initTileFilm();
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

  /* The farm model: pointing at a step lights it in the loop and the list; when nobody is
     interacting and the loop is on screen, the highlight moves round slowly (not with reduced motion) */
  function initLoop() {
    var loop = document.querySelector('[data-loop]');
    var steps = document.querySelectorAll('.hm-steps li[data-i]');
    if (!loop || !steps.length) return;
    var nodes = loop.querySelectorAll('.hm-node'), current = 0, hold = false, visible = false;
    function show(i) {
      current = i;
      nodes.forEach(function (n, k) { n.classList.toggle('is-on', k === i); });
      steps.forEach(function (s, k) { s.classList.toggle('is-on', k === i); });
    }
    var chosen = false;
    document.querySelector('.hm-steps').classList.add('is-js');
    [nodes, steps].forEach(function (list) {
      list.forEach(function (el, k) {
        el.addEventListener('mouseenter', function () { hold = true; show(k); });
        el.addEventListener('mouseleave', function () { hold = chosen; });
      });
    });
    // Tapping or pressing a photo shows its step and stops the automatic tour
    nodes.forEach(function (n, k) { n.addEventListener('click', function () { chosen = hold = true; show(k); }); });
    if (reduceMotion || !('IntersectionObserver' in window)) return;
    new IntersectionObserver(function (entries) { entries.forEach(function (en) { visible = en.isIntersecting; }); }, { threshold: 0.4 }).observe(loop);
    setInterval(function () {
      if (hold || !visible || document.documentElement.classList.contains('ab-motion-paused')) return;
      show((current + 1) % nodes.length);
    }, 3200);
  }

  /* School kitchen: a short silent film in one mosaic tile, loaded when it comes near the screen
     (wider screens only, never with reduced motion or data saving); pauses off screen and with the page's pause */
  function initTileFilm() {
    var tile = document.querySelector('[data-tile-film]');
    var video = tile && tile.querySelector('video');
    if (!video || !('IntersectionObserver' in window)) return;
    var saveData = navigator.connection && navigator.connection.saveData;
    if (reduceMotion || saveData || !window.matchMedia('(min-width: 720px)').matches) return;
    var visible = false;
    function sync() {
      if (!video.getAttribute('src')) return;
      if (!visible || document.documentElement.classList.contains('ab-motion-paused')) video.pause(); else video.play().catch(function () {});
    }
    new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        visible = en.isIntersecting;
        if (visible && !video.getAttribute('src')) {
          video.addEventListener('playing', function () { tile.classList.add('has-video'); }, { once: true });
          video.src = video.getAttribute('data-src');
        }
        sync();
      });
    }, { rootMargin: '150px 0px' }).observe(tile);
    new MutationObserver(sync).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
  }

  /* Where we work: pointing at a country in the list or on the map lights up both */
  function initMap() {
    var map = document.querySelector('[data-reach-map]');
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
