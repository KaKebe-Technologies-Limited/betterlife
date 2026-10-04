/* BetterLife Farm: the connected-farm ring. The six parts are ordinary tabs (initTabs in about.js); this keeps the
   photograph in the centre in step with the chosen part, and turns the ring on its own while it is on screen
   until someone chooses a part themselves. Never turns with reduced motion. */
(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    var list = document.querySelector('.fm-ring-nodes');
    if (!list) return;
    var photos = Array.prototype.slice.call(document.querySelectorAll('.fm-ring-photo'));
    var keys = Array.prototype.slice.call(list.querySelectorAll('[role="tab"]')).map(function (t) { return t.getAttribute('data-key'); });
    var current = keys[0];
    list.addEventListener('ab:select', function (e) {
      current = e.detail.key;
      photos.forEach(function (p) { p.classList.toggle('is-on', p.getAttribute('data-key') === current); });
    });

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce || !('IntersectionObserver' in window)) return;
    var stopped = false, visible = false, timer = null;
    function stop() { stopped = true; clearInterval(timer); }
    ['pointerdown', 'keydown'].forEach(function (ev) { list.addEventListener(ev, stop); });
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
  });
})();
