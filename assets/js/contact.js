/* Contact page: the message box asks a question that fits the chosen topic; the email address can be
   copied; required fields are checked before sending; links that name a topic choose it in place. */
(function () {
  'use strict';
  var form = document.querySelector('.ct-form');

  // 1. The question above the message box follows the topic
  var ask = document.querySelector('.ct-ask');
  var radios = form ? [].slice.call(form.querySelectorAll('input[name="subject"]')) : [];
  function setAsk(r) {
    if (!ask || !r) return;
    var star = ask.querySelector('span');
    ask.textContent = r.getAttribute('data-ask') + ' ';
    if (star) ask.appendChild(star);
  }
  radios.forEach(function (r) { r.addEventListener('change', function () { setAsk(r); }); });
  setAsk(radios.filter(function (r) { return r.checked; })[0]);

  // 2. Copy the email address
  var copy = document.querySelector('.ct-copy');
  if (copy && navigator.clipboard) {
    copy.hidden = false;
    var done = document.querySelector('.ct-copy-done');
    copy.addEventListener('click', function () {
      navigator.clipboard.writeText(copy.getAttribute('data-copy')).then(function () {
        if (done) { done.textContent = 'Email address copied'; setTimeout(function () { done.textContent = ''; }, 2600); }
      });
    });
  }

  // 3. Check the required fields here first, with a message beside each one
  if (form) {
    var messages = { name: 'Please tell us your name.', email: 'Please give an email address we can reply to.', message: 'Please write a message.' };
    form.addEventListener('submit', function (e) {
      var first = null;
      ['name', 'email', 'message'].forEach(function (n) {
        var input = form.elements[n];
        var field = input.closest('.ct-field');
        var old = field.querySelector('.ct-field-error');
        if (old) old.remove();
        input.removeAttribute('aria-invalid');
        field.classList.remove('is-invalid');
        var bad = !input.value.trim() || (n === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim()));
        if (bad) {
          var p = document.createElement('p');
          p.className = 'ct-field-error';
          p.id = input.id + 'Error';
          p.textContent = n === 'email' && input.value.trim() ? 'That email address does not look quite right.' : messages[n];
          field.appendChild(p);
          input.setAttribute('aria-invalid', 'true');
          input.setAttribute('aria-describedby', p.id);
          field.classList.add('is-invalid');
          if (!first) first = input;
        }
      });
      if (first) { e.preventDefault(); first.focus(); }
    });
  }

  // 4. "Plan a visit" and similar links choose their topic without reloading the page
  [].slice.call(document.querySelectorAll('a[data-topic]')).forEach(function (a) {
    a.addEventListener('click', function (e) {
      var r = radios.filter(function (x) { return x.value === a.getAttribute('data-topic'); })[0];
      if (!r) return;
      e.preventDefault();
      r.checked = true;
      setAsk(r);
      document.getElementById('write').scrollIntoView({ behavior: 'smooth', block: 'start' });
      setTimeout(function () { var m = form.elements.message; if (m) m.focus({ preventScroll: true }); }, 600);
    });
  });
})();
