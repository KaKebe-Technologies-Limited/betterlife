// About page interactions. Everything here enhances content that is already
// fully readable without JavaScript.
(function () {
  'use strict';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasIO = 'IntersectionObserver' in window;

  document.addEventListener('DOMContentLoaded', function () {
    initHeroMotion();
    initReveal();
    initCounters();
    document.querySelectorAll('[role="tablist"]').forEach(initTabs);
    initToggles();
    initPrinciples();
    initRail();
    initMap();
    initLightbox();
    document.querySelectorAll('.ab-glide').forEach(initGlide);
  });

  /* ---------- Opening photographs: pause control (remembered) and pause when off screen ---------- */
  function initHeroMotion() {
    var hero = document.querySelector('.ab-hero');
    var btn = document.querySelector('.ab-motion-toggle');
    var root = document.documentElement;
    if (!hero || !btn) return;
    var video = hero.querySelector('.ab-hero-video');
    var userPaused = function () { return root.classList.contains('ab-motion-paused'); };
    var offscreen = false;
    function syncVideo() {
      if (!video || !video.getAttribute('src')) return;
      if (userPaused() || offscreen) video.pause(); else video.play().catch(function () {});
    }
    function setPaused(paused) {
      root.classList.toggle('ab-motion-paused', paused);
      btn.setAttribute('aria-pressed', paused ? 'true' : 'false');
      btn.setAttribute('aria-label', paused ? 'Play background motion' : 'Pause background motion');
      try { localStorage.setItem('abMotionPaused', paused ? '1' : '0'); } catch (e) {}
      syncVideo();
    }
    try { if (localStorage.getItem('abMotionPaused') === '1') setPaused(true); } catch (e) {}
    btn.addEventListener('click', function () { setPaused(!userPaused()); });
    if (hasIO) {
      new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { offscreen = !en.isIntersecting; hero.classList.toggle('is-offscreen', offscreen); syncVideo(); });
      }).observe(hero);
    }
    // The film loads only after the page has finished loading, on wider screens, without
    // reduced motion or data saving; it fades in over the photographs once it is playing.
    var saveData = navigator.connection && navigator.connection.saveData;
    if (video && !reduceMotion && !saveData && window.matchMedia('(min-width: 720px)').matches) {
      var start = function () {
        video.addEventListener('playing', function () { hero.classList.add('has-video'); }, { once: true });
        // Full HD where the opening is drawn with more than ~1400 device pixels across, 720p otherwise
        var hd = video.getAttribute('data-src-hd');
        video.src = hd && hero.offsetWidth * (window.devicePixelRatio || 1) > 1400 ? hd : video.getAttribute('data-src');
        syncVideo();
      };
      if (document.readyState === 'complete') setTimeout(start, 300); else window.addEventListener('load', function () { setTimeout(start, 300); });
    }
  }

  /* ---------- Gliding photo gallery: each row moves sideways as the gallery passes through the screen, rows in
     opposite directions. A row someone swipes, scrolls or tabs through stays where they put it. ---------- */
  function initGlide(box) {
    var rows = Array.prototype.slice.call(box.querySelectorAll('.ab-glide-row[data-glide]'));
    if (!rows.length || reduceMotion) return;
    var state = rows.map(function (row) {
      var s = { row: row, dir: Number(row.getAttribute('data-glide')), set: -1, free: false };
      row.addEventListener('scroll', function () { if (s.set >= 0 && Math.abs(row.scrollLeft - s.set) > 3) s.free = true; }, { passive: true });
      row.addEventListener('focusin', function () { s.free = true; });
      return s;
    });
    var ticking = false;
    function update() {
      ticking = false;
      var r = box.getBoundingClientRect(), vh = window.innerHeight;
      if (r.bottom < -200 || r.top > vh + 200) return;
      var p = Math.max(0, Math.min(1, (vh - r.top) / (vh + r.height)));
      state.forEach(function (s) {
        if (s.free) return;
        var max = s.row.scrollWidth - s.row.clientWidth;
        if (max <= 0) return;
        s.set = Math.round((s.dir > 0 ? p : 1 - p) * max);
        s.row.scrollLeft = s.set;
      });
    }
    function request() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request);
    window.addEventListener('load', request);
    update();
  }

  /* ---------- Reveal on scroll (one time) ---------- */
  function initReveal() {
    var items = document.querySelectorAll('.ab-reveal');
    if (reduceMotion || !hasIO) { items.forEach(function (el) { el.classList.add('is-in'); }); return; }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    items.forEach(function (el) { io.observe(el); });
  }

  /* ---------- Figures: brief one-time count-up; final values already in the HTML ---------- */
  function initCounters() {
    var counters = document.querySelectorAll('.ab-count[data-count]');
    if (reduceMotion || !hasIO || !counters.length) return;
    var fmt = function (n) { return Math.round(n).toLocaleString('en-US'); };
    var run = function (el) {
      var target = parseInt(el.getAttribute('data-count'), 10), finalText = el.textContent, start = null, dur = 1300;
      function step(t) {
        if (start === null) start = t;
        var p = Math.min(1, (t - start) / dur), eased = 1 - Math.pow(1 - p, 3);
        el.textContent = p < 1 ? fmt(target * eased) : finalText;
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    };
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { run(en.target); io.unobserve(en.target); }
      });
    }, { threshold: 0.6 });
    counters.forEach(function (el) { io.observe(el); });
  }

  /* ---------- Accessible tabs (arrow keys, Home/End, automatic activation) ---------- */
  function initTabs(list) {
    var tabs = Array.prototype.slice.call(list.querySelectorAll('[role="tab"]'));
    if (!tabs.length) return;
    function select(tab, focus) {
      tabs.forEach(function (t) {
        var on = t === tab;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
        var panel = document.getElementById(t.getAttribute('aria-controls'));
        if (panel) panel.classList.toggle('is-active', on);
      });
      if (focus) tab.focus();
      list.dispatchEvent(new CustomEvent('ab:select', { detail: { key: tab.getAttribute('data-key') } }));
    }
    tabs.forEach(function (tab, i) {
      tab.addEventListener('click', function () { select(tab, false); });
      tab.addEventListener('keydown', function (e) {
        var next = null;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next = tabs[(i + 1) % tabs.length];
        else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') next = tabs[(i - 1 + tabs.length) % tabs.length];
        else if (e.key === 'Home') next = tabs[0];
        else if (e.key === 'End') next = tabs[tabs.length - 1];
        if (next) { e.preventDefault(); select(next, true); }
      });
    });
    list.abSelect = function (key) {
      var t = tabs.filter(function (x) { return x.getAttribute('data-key') === key; })[0];
      if (t) select(t, false);
    };
    var current = tabs.filter(function (t) { return t.getAttribute('aria-selected') === 'true'; })[0] || tabs[0];
    select(current, false);
  }

  /* ---------- Read more toggles (extra text visible without JS) ---------- */
  function initToggles() {
    document.querySelectorAll('.ab-who-toggle').forEach(function (btn) {
      var target = document.getElementById(btn.getAttribute('aria-controls'));
      if (!target) return;
      target.hidden = true;
      btn.hidden = false;
      var label = btn.querySelector('span');
      btn.addEventListener('click', function () {
        var open = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
        target.hidden = open;
        if (label) label.textContent = open ? 'Read more' : 'Show less';
      });
    });
  }

  /* ---------- How we work: one principle open at a time; the photograph follows it ---------- */
  function initPrinciples() {
    var steps = Array.prototype.slice.call(document.querySelectorAll('.ab-how-step'));
    var imgs = document.querySelectorAll('.ab-how-img');
    var count = document.querySelector('.ab-how-count b');
    if (!steps.length) return;
    function activate(i) {
      imgs.forEach(function (img) { img.classList.toggle('is-active', img.getAttribute('data-step') === String(i)); });
      if (count) count.textContent = (i + 1 < 10 ? '0' : '') + (i + 1);
    }
    steps.forEach(function (d, i) {
      d.addEventListener('toggle', function () {
        if (!d.open) return;
        // Browsers without exclusive <details name> still close the others
        steps.forEach(function (o) { if (o !== d && o.open) o.open = false; });
        activate(i);
      });
    });
  }

  /* ---------- Journey rail: previous / next buttons, disabled at either end ---------- */
  function initRail() {
    var rail = document.querySelector('.ab-timeline');
    var nav = document.querySelector('.ab-rail-nav');
    if (!rail || !nav) return;
    var btns = nav.querySelectorAll('.ab-rail-btn');
    function update() {
      var max = rail.scrollWidth - rail.clientWidth - 2;
      nav.hidden = max <= 0;
      btns[0].disabled = rail.scrollLeft <= 2;
      btns[1].disabled = rail.scrollLeft >= max;
    }
    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        var card = rail.querySelector('.ab-milestone');
        var step = card ? card.getBoundingClientRect().width + 22 : rail.clientWidth * 0.8;
        rail.scrollBy({ left: step * Number(b.getAttribute('data-dir')), behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    });
    rail.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  }

  /* ---------- Interactive map: tabs and countries stay in sync; zoom preserves proportions ---------- */
  function initMap() {
    var root = document.querySelector('[data-map]');
    var web = document.querySelector('.ab-web [role="tablist"]');
    if (web) {
      web.addEventListener('ab:select', function (e) {
        document.querySelectorAll('.ab-web-lines line').forEach(function (l) {
          l.classList.toggle('is-active', l.getAttribute('data-for') === e.detail.key);
        });
      });
      var sel = web.querySelector('[aria-selected="true"]');
      if (sel) document.querySelectorAll('.ab-web-lines line[data-for="' + sel.getAttribute('data-key') + '"]').forEach(function (l) { l.classList.add('is-active'); });
    }
    if (!root) return;
    var svg = root.querySelector('.ab-map');
    var tabs = root.querySelector('.ab-country-tabs');
    var countries = svg.querySelectorAll('.ab-map-country');
    var places = svg.querySelectorAll('.ab-map-place');
    var base = svg.getAttribute('data-base').split(' ').map(Number);
    var aspect = base[2] / base[3];
    var current = base.slice();
    var anim = null;

    function setView(v) {
      current = v;
      svg.setAttribute('viewBox', v.map(function (n) { return n.toFixed(2); }).join(' '));
      // Keep markers and labels the same on-screen size at any zoom and any map width
      var k = v[2] / (svg.getBoundingClientRect().width || base[2]);
      places.forEach(function (p) {
        p.setAttribute('transform', 'translate(' + p.getAttribute('data-x') + ' ' + p.getAttribute('data-y') + ') scale(' + k.toFixed(4) + ')');
      });
      // On the whole-continent view the East African places sit too close to label
      svg.classList.toggle('is-zoomed', v[2] < base[2] * 0.6);
    }
    window.addEventListener('resize', function () { setView(current); });
    function viewFor(key) {
      var path = svg.querySelector('.ab-map-country[data-key="' + key + '"]');
      if (!path) return base.slice();
      var b = path.getBBox();
      // Pad generously so neighbouring countries stay visible, then match the map's aspect ratio
      var w = Math.max(b.width * 2.4, 170), h = Math.max(b.height * 2.4, 170 / aspect);
      if (w / h > aspect) h = w / aspect; else w = h * aspect;
      var cx = b.x + b.width / 2, cy = b.y + b.height / 2;
      return [cx - w / 2, cy - h / 2, w, h];
    }
    function zoomTo(target) {
      if (anim) cancelAnimationFrame(anim);
      if (reduceMotion) { setView(target); return; }
      var from = current.slice(), start = null, dur = 750;
      function step(t) {
        if (start === null) start = t;
        var p = Math.min(1, (t - start) / dur), e = p < .5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2;
        setView(from.map(function (f, i) { return f + (target[i] - f) * e; }));
        if (p < 1) anim = requestAnimationFrame(step);
      }
      anim = requestAnimationFrame(step);
    }
    function highlight(key) {
      countries.forEach(function (c) { c.classList.toggle('is-selected', c.getAttribute('data-key') === key); });
    }
    // The page opens on the whole continent; choosing a country zooms in
    tabs.addEventListener('ab:select', function (e) {
      highlight(e.detail.key);
      zoomTo(viewFor(e.detail.key));
    });
    countries.forEach(function (c) {
      c.addEventListener('click', function () { if (tabs.abSelect) tabs.abSelect(c.getAttribute('data-key')); });
    });
    // "Whole continent" reset control
    var reset = document.createElement('button');
    reset.type = 'button';
    reset.className = 'ab-map-reset';
    reset.textContent = 'Show all of Africa';
    reset.addEventListener('click', function () { zoomTo(base.slice()); });
    root.querySelector('.ab-map-wrap').appendChild(reset);
    var sel = tabs.querySelector('[aria-selected="true"]');
    if (sel) highlight(sel.getAttribute('data-key'));
    setView(base.slice());
  }

  /* ---------- Lightbox with previous / next / close ---------- */
  function initLightbox() {
    var dlg = document.getElementById('abLightbox');
    if (!dlg || typeof dlg.showModal !== 'function') return;   // links still open the image directly
    var img = dlg.querySelector('img'), cap = dlg.querySelector('.ab-lb-caption'), cnt = dlg.querySelector('.ab-lb-count');
    var items = [], index = 0, opener = null;

    function show(i) {
      index = (i + items.length) % items.length;
      var a = items[index], thumb = a.querySelector('img');
      img.src = a.getAttribute('href');
      img.alt = thumb ? thumb.alt : '';
      cap.textContent = a.getAttribute('data-caption') || '';
      cnt.textContent = (index + 1) + ' of ' + items.length;
    }
    document.querySelectorAll('.ab-lb').forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        opener = a;
        var g = a.getAttribute('data-gallery');
        items = Array.prototype.slice.call(document.querySelectorAll('.ab-lb[data-gallery="' + g + '"]'));
        dlg.classList.toggle('is-single', items.length < 2);
        show(items.indexOf(a));
        dlg.showModal();
        dlg.querySelector('.ab-lb-close').focus();
      });
    });
    dlg.querySelector('.ab-lb-close').addEventListener('click', function () { dlg.close(); });
    dlg.querySelector('.ab-lb-prev').addEventListener('click', function () { show(index - 1); });
    dlg.querySelector('.ab-lb-next').addEventListener('click', function () { show(index + 1); });
    dlg.addEventListener('keydown', function (e) {
      if (items.length < 2) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
    });
    dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });   // click outside the photo
    dlg.addEventListener('close', function () { img.src = ''; if (opener) opener.focus(); });
  }
})();
