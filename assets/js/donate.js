/* Donate: show the "other amount" box when it is chosen, the amount in US dollars as a guide,
   and the amount on the button. The form works without this. */
(function () {
  'use strict';
  var form = document.querySelector('[data-donate]');
  if (!form) return;
  var other = form.querySelector('[data-other]');
  var otherInput = other && other.querySelector('input');
  var usd = form.querySelector('[data-usd]');
  var give = form.querySelector('[data-give] span');
  var rate = parseFloat(form.getAttribute('data-usd-rate')) || 0;

  function amount() {
    var picked = form.querySelector('input[name="amount"]:checked');
    if (!picked) return 0;
    if (picked.value === 'other') return parseInt((otherInput.value || '').replace(/[^0-9]/g, ''), 10) || 0;
    return parseInt(picked.value, 10) || 0;
  }
  function update() {
    var picked = form.querySelector('input[name="amount"]:checked');
    var isOther = picked && picked.value === 'other';
    other.hidden = !isOther;
    var a = amount();
    give.textContent = a ? 'Give UGX ' + a.toLocaleString('en-US') : 'Give now';
    usd.textContent = a && rate ? 'About USD ' + (a / rate).toFixed(2) + '. Payment is in Uganda shillings.' : '';
  }
  form.addEventListener('change', function (e) {
    update();
    if (e.target.name === 'amount' && e.target.value === 'other') otherInput.focus();
  });
  otherInput.addEventListener('input', update);
  update();
})();
