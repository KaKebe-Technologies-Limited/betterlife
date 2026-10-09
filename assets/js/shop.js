/* The farm shop. Everything here improves on plain forms and links that already work without it:
   quantity buttons, adding to the basket without leaving the page, filtering by product type,
   and updating basket quantities as they change. */
(function () {
  'use strict';

  // 1. Quantity: minus and plus beside the number box
  [].slice.call(document.querySelectorAll('[data-qty]')).forEach(function (box) {
    var input = box.querySelector('input');
    [].slice.call(box.querySelectorAll('button[data-step]')).forEach(function (b) {
      b.hidden = false;
      b.addEventListener('click', function () {
        var v = Math.min(99, Math.max(1, (parseInt(input.value, 10) || 1) + parseInt(b.getAttribute('data-step'), 10)));
        input.value = v;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
  });

  // 2. Adding to the basket in place, then showing the basket bar
  var bar = document.querySelector('[data-basket-bar]');
  function toast(text) {
    var old = document.querySelector('.sh-toast');
    if (old) old.remove();
    var t = document.createElement('div');
    t.className = 'sh-toast';
    t.setAttribute('role', 'status');
    t.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>';
    t.appendChild(document.createTextNode(text));
    document.body.appendChild(t);
    setTimeout(function () { t.remove(); }, 2700);
  }
  if (window.fetch && window.FormData) {
    [].slice.call(document.querySelectorAll('form.sh-add')).forEach(function (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('.sh-add-btn');
        var label = btn.querySelector('span');
        btn.disabled = true;
        fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (d) {
            if (!d.ok) throw new Error(d.message || 'not added');
            if (bar) {
              bar.querySelector('[data-basket-count]').textContent = d.count + (d.count === 1 ? ' item' : ' items');
              bar.querySelector('[data-basket-total]').textContent = d.total;
              bar.hidden = false;
              bar.classList.remove('is-bump'); void bar.offsetWidth; bar.classList.add('is-bump');
            }
            toast(d.name + ' is in your basket');
            btn.classList.add('is-added');
            label.textContent = 'Added';
            setTimeout(function () { btn.classList.remove('is-added'); label.textContent = 'Add to basket'; }, 1800);
          })
          .catch(function () { form.submit(); })   // fall back to the plain form
          .then(function () { btn.disabled = false; });
      });
    });
  }

  // 3. Product types: filter in place, keeping the address shareable
  var cats = document.querySelector('.sh-cats');
  if (cats) {
    var cards = [].slice.call(document.querySelectorAll('.sh-grid .sh-card'));
    cats.addEventListener('click', function (e) {
      var a = e.target.closest ? e.target.closest('a[data-cat]') : null;
      if (!a || e.metaKey || e.ctrlKey || e.shiftKey) return;
      e.preventDefault();
      var cat = a.getAttribute('data-cat');
      [].slice.call(cats.querySelectorAll('a')).forEach(function (x) { if (x === a) x.setAttribute('aria-current', 'true'); else x.removeAttribute('aria-current'); });
      cards.forEach(function (c) { c.hidden = !!cat && c.getAttribute('data-category') !== cat; });
      try { history.replaceState(null, '', location.pathname + (cat ? '?category=' + encodeURIComponent(cat) : '') + '#products'); } catch (err) {}
    });
  }

  // 4. Basket: a changed quantity updates on its own
  [].slice.call(document.querySelectorAll('form[data-autosubmit]')).forEach(function (form) {
    var update = form.querySelector('.sh-line-update');
    if (update) update.hidden = true;
    var timer;
    form.addEventListener('change', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { form.submit(); }, 450);
    });
  });
})();
