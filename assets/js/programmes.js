// Programmes pages: photo strip controls. Reveals, the photo viewer and
// "Read more" toggles come from about.js, which these pages also load.
(function () {
  'use strict';
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-strip]').forEach(initStrip);
  });

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
