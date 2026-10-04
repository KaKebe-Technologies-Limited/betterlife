/* BetterLife Farm.
   1. The connected-farm ring. The six parts are ordinary tabs (initTabs in about.js); this keeps the photograph in
      the centre in step with the chosen part, and turns the ring on its own while it is on screen until someone
      chooses a part themselves. The film index in the hero opens the matching part. Never turns with reduced motion.
   2. The value-addition diagram. On wide screens, lines run from what comes in, through the farm, to what goes out;
      pointing at any item lights up its own route. */
(function () {
  'use strict';
  var SVG = 'http://www.w3.org/2000/svg';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function initRing() {
    var list = document.querySelector('.fm-ring-nodes');
    if (!list) return;
    var photos = Array.prototype.slice.call(document.querySelectorAll('.fm-ring-photo'));
    var keys = Array.prototype.slice.call(list.querySelectorAll('[role="tab"]')).map(function (t) { return t.getAttribute('data-key'); });
    var current = keys[0], stopped = false, visible = false, timer = null;
    list.addEventListener('ab:select', function (e) {
      current = e.detail.key;
      photos.forEach(function (p) { p.classList.toggle('is-on', p.getAttribute('data-key') === current); });
    });
    function stop() { stopped = true; clearInterval(timer); }
    ['pointerdown', 'keydown'].forEach(function (ev) { list.addEventListener(ev, stop); });

    // The hero's film index: each item opens its part of the ring as the page moves down to it
    document.querySelectorAll('.fm-hero .pg-hero-index a[data-part]').forEach(function (a) {
      a.addEventListener('click', function () {
        stop();
        if (list.abSelect) list.abSelect(a.getAttribute('data-part'));
      });
    });

    if (reduce || !('IntersectionObserver' in window)) return;
    function tick() {
      if (stopped || !visible || !list.abSelect || document.hidden) return;
      list.abSelect(keys[(keys.indexOf(current) + 1) % keys.length]);
    }
    new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        visible = en.isIntersecting;
        clearInterval(timer);
        if (visible && !stopped) timer = setInterval(tick, 4200);
      });
    }, { threshold: 0.35 }).observe(list.closest('.fm-ring-stage'));
  }

  function initFlow() {
    var flow = document.querySelector('.fm-flow');
    if (!flow) return;
    var svg = flow.querySelector('.fm-flow-lines');
    var hub = flow.querySelector('.fm-hub-photo');
    var ins = Array.prototype.slice.call(flow.querySelectorAll('.fm-in'));
    var outs = Array.prototype.slice.call(flow.querySelectorAll('.fm-out'));

    function curve(x1, y1, x2, y2) {
      var c = (x2 - x1) * 0.5;
      return 'M' + x1 + ',' + y1 + ' C' + (x1 + c) + ',' + y1 + ' ' + (x2 - c) + ',' + y2 + ' ' + x2 + ',' + y2;
    }
    function add(d, key) {
      ['fm-line', 'fm-dots'].forEach(function (cls) {
        var p = document.createElementNS(SVG, 'path');
        p.setAttribute('d', d); p.setAttribute('class', cls); p.setAttribute('data-in', key);
        svg.appendChild(p);
      });
    }
    function draw() {
      while (svg.firstChild) svg.removeChild(svg.firstChild);
      if (getComputedStyle(svg).display === 'none') return;
      var box = flow.getBoundingClientRect(), h = hub.getBoundingClientRect();
      var hy = h.top + h.height / 2 - box.top, gap = 10;
      svg.setAttribute('viewBox', '0 0 ' + box.width + ' ' + box.height);
      ins.forEach(function (li) {
        var r = li.querySelector('.fm-in-ico').getBoundingClientRect();
        add(curve(r.right - box.left + gap, r.top + r.height / 2 - box.top, h.left - box.left - gap, hy), li.getAttribute('data-in'));
      });
      outs.forEach(function (li) {
        var r = li.querySelector('.fm-out-img').getBoundingClientRect();
        add(curve(h.right - box.left + gap, hy, r.left - box.left - gap, r.top + r.height / 2 - box.top), li.getAttribute('data-in'));
      });
    }

    // Point at an input or a product to light up its route
    function light(key) {
      flow.classList.toggle('is-dim', !!key);
      flow.querySelectorAll('[data-in]').forEach(function (el) { el.classList.toggle('is-on', !!key && el.getAttribute('data-in') === key); });
    }
    // Only where the lines are drawn, and only for a mouse (a tap on a phone would leave the diagram dimmed)
    function lines() { return getComputedStyle(svg).display !== 'none'; }
    flow.addEventListener('pointerover', function (e) {
      if (e.pointerType !== 'mouse' || !lines()) return;
      var item = e.target.closest('.fm-in, .fm-out');
      light(item ? item.getAttribute('data-in') : null);
    });
    flow.addEventListener('pointerleave', function () { light(null); });
    flow.addEventListener('focusin', function (e) {
      if (!lines()) return;
      var item = e.target.closest('.fm-out');
      light(item ? item.getAttribute('data-in') : null);
    });
    flow.addEventListener('focusout', function () { light(null); });

    draw();
    if ('ResizeObserver' in window) new ResizeObserver(draw).observe(flow);
    else window.addEventListener('resize', draw);
    window.addEventListener('load', draw);
    // Redraw once the diagram has finished rising into place
    flow.addEventListener('transitionend', function (e) { if (e.target === flow) draw(); });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initRing();
    initFlow();
  });
})();
