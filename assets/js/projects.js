/* Projects directory: filter in place. The chips are ordinary links, so the page also works without this script. */
(function () {
  'use strict';
  var dir = document.getElementById('directory');
  if (!dir) return;

  var cards = [].slice.call(dir.querySelectorAll('.pg-dcard'));
  var groups = [].slice.call(dir.querySelectorAll('.pg-group'));
  var chips = [].slice.call(dir.querySelectorAll('.pg-fchip'));
  var status = dir.querySelector('.pg-count');
  var empty = dir.querySelector('.pg-empty');
  var state = { area: dir.getAttribute('data-area') || '', country: dir.getAttribute('data-country') || '' };

  function matches(card, area, country) {
    return (!area || (' ' + card.getAttribute('data-areas') + ' ').indexOf(' ' + area + ' ') > -1)
      && (!country || card.getAttribute('data-country') === country);
  }
  function count(area, country) {
    return cards.filter(function (c) { return matches(c, area, country); }).length;
  }
  function label(kind, value) {
    var chip = chips.filter(function (c) { return c.getAttribute('data-kind') === kind && c.getAttribute('data-value') === value; })[0];
    return chip ? chip.getAttribute('data-label') : '';
  }
  function plural(n) { return n + (n === 1 ? ' project' : ' projects'); }

  function apply() {
    var shown = 0;
    cards.forEach(function (c) {
      var on = matches(c, state.area, state.country);
      c.hidden = !on;
      if (on) shown++;
    });
    groups.forEach(function (g) {
      var n = g.querySelectorAll('.pg-dcard:not([hidden])').length;
      g.hidden = !n;
      g.classList.toggle('is-one', n === 1);
      g.classList.toggle('is-two', n === 2);
      var out = g.querySelector('.pg-group-n');
      if (out) out.textContent = plural(n);
    });
    chips.forEach(function (chip) {
      var kind = chip.getAttribute('data-kind'), value = chip.getAttribute('data-value');
      if (state[kind] === value) chip.setAttribute('aria-current', 'true'); else chip.removeAttribute('aria-current');
      var n = kind === 'area' ? count(value, state.country) : count(state.area, value);
      var small = chip.querySelector('small');
      if (small) small.textContent = '(' + n + ')';
    });

    // The live status line, rebuilt from text so nothing is parsed as markup
    status.textContent = 'Showing ' + plural(shown)
      + (state.area ? ' in ' + label('area', state.area) : '')
      + (state.country ? ' · ' + label('country', state.country) : '');
    if (state.area || state.country) {
      var clear = document.createElement('a');
      clear.href = 'projects.php#directory';
      clear.setAttribute('data-clear', '');
      clear.textContent = 'Clear filters';
      status.appendChild(document.createTextNode(' · '));
      status.appendChild(clear);
    }
    if (empty) empty.hidden = shown > 0;

    // Keep the address shareable
    var q = [];
    if (state.area) q.push('area=' + encodeURIComponent(state.area));
    if (state.country) q.push('country=' + encodeURIComponent(state.country));
    try { history.replaceState(null, '', location.pathname + (q.length ? '?' + q.join('&') : '') + '#directory'); } catch (e) {}
  }

  dir.addEventListener('click', function (e) {
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    var link = e.target.closest ? e.target.closest('.pg-fchip, [data-clear]') : null;
    if (!link || !dir.contains(link)) return;
    e.preventDefault();
    if (link.hasAttribute('data-clear')) { state.area = ''; state.country = ''; }
    else state[link.getAttribute('data-kind')] = link.getAttribute('data-value');
    apply();
  });
})();
