/* Impact and reports: the report covers follow the year chosen above them, and choosing a cover chooses its year.
   The year buttons are ordinary tabs (initTabs in about.js, which runs first). */
(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
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
  });
})();
