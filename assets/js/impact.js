/* Impact and reports.
   1. The report covers follow the year chosen above them, and choosing a cover chooses its year.
      The year buttons are ordinary tabs (initTabs in about.js, which runs first).
   2. Field notes: the photograph beside the lines changes to match the line being read. */
(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    initShelf();
    initNotes();
  });

  function initShelf() {
    var list = document.querySelector('.im-year-tabs');
    if (!list) return;
    var books = Array.prototype.slice.call(document.querySelectorAll('.im-book[data-key]'));
    function mark(key) {
      books.forEach(function (b) { b.classList.toggle('is-on', b.getAttribute('data-key') === key); });
    }
    list.addEventListener('ab:select', function (e) { mark(e.detail.key); });
    books.forEach(function (b) {
      b.addEventListener('click', function () { if (list.abSelect) list.abSelect(b.getAttribute('data-key')); });
    });
    var current = list.querySelector('[aria-selected="true"]');
    if (current) mark(current.getAttribute('data-key'));
  }

  function initNotes() {
    var box = document.querySelector('.im-essay');
    if (!box || !('IntersectionObserver' in window)) return;
    var steps = Array.prototype.slice.call(box.querySelectorAll('.im-step'));
    var frames = Array.prototype.slice.call(box.querySelectorAll('.im-frame-img'));
    var count = box.querySelector('.im-frame-count span');
    function show(i) {
      steps.forEach(function (s, k) { s.classList.toggle('is-on', k === i); });
      frames.forEach(function (f, k) { f.classList.toggle('is-on', k === i); });
      if (count) count.textContent = (i < 9 ? '0' : '') + (i + 1);
    }
    // A line counts as being read once it crosses the middle of the screen
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) show(steps.indexOf(en.target)); });
    }, { rootMargin: '-45% 0px -45% 0px' });
    steps.forEach(function (s) { io.observe(s); });
  }
})();
