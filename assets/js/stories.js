/* Stories page.
   1. After subscribing, the newsletter form returns to this page; bring its message into view.
   2. The wall of press cuttings: filter by topic, and show the first few until asked for the rest.
      Without this script every cutting shows and the filters stay hidden. */
(function () {
  'use strict';

  var flash = document.querySelector('.st-flash');
  if (flash && flash.scrollIntoView) flash.scrollIntoView({ block: 'center' });

  var wall = document.querySelector('.st-wall');
  if (!wall) return;
  var cuttings = [].slice.call(wall.querySelectorAll('.st-cutting'));
  var bar = document.querySelector('.st-topics');
  var more = document.querySelector('.st-wall-more');
  var buttons = bar ? [].slice.call(bar.querySelectorAll('button')) : [];
  // Fewer on phones, where the cuttings stack one above another
  var small = window.matchMedia && window.matchMedia('(max-width: 720px)').matches;
  var limit = parseInt(wall.getAttribute(small ? 'data-show-small' : 'data-show'), 10) || cuttings.length;
  var topic = '';
  var expanded = false;

  function render() {
    var matched = 0;
    cuttings.forEach(function (c) {
      var match = !topic || (' ' + c.getAttribute('data-topics') + ' ').indexOf(' ' + topic + ' ') > -1;
      // A topic shows all its cuttings; the full wall shows the first few until expanded
      c.hidden = !match || (!topic && !expanded && matched >= limit);
      if (match) matched++;
    });
    if (more) more.hidden = !!topic || expanded || cuttings.length <= limit;
  }

  buttons.forEach(function (b) {
    b.addEventListener('click', function () {
      topic = b.getAttribute('data-topic') || '';
      buttons.forEach(function (o) { o.setAttribute('aria-pressed', o === b ? 'true' : 'false'); });
      render();
    });
  });

  if (more) {
    more.querySelector('button').addEventListener('click', function () {
      var first = cuttings.filter(function (c) { return c.hidden; })[0];
      expanded = true;
      render();
      // Keyboard users continue from the first newly shown article
      var link = first && first.querySelector('a');
      if (link) link.focus();
    });
  }

  if (bar) bar.hidden = false;
  render();
})();
