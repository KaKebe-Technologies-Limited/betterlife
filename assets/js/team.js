/* Our Team.
   1. Reading panel: "Read bio" opens the person's full bio in a dialog; Previous and Next (or the arrow keys) move
      through everyone with a bio, in page order.
   2. Country map: pointing at a country manager lights up their country and its line. On narrow screens the map is
      framed on Africa alone, with the cards listed beneath it. */
(function () {
  'use strict';

  function initBios() {
    var bios = window.__teamBios || [];
    var dialog = document.getElementById('tmDialog');
    if (!dialog || !bios.length || typeof dialog.showModal !== 'function') return;
    var photo = dialog.querySelector('.tm-dialog-photo');
    var role = document.getElementById('tmDialogRole');
    var name = document.getElementById('tmDialogName');
    var bio = document.getElementById('tmDialogBio');
    var count = dialog.querySelector('.tm-dialog-count');
    var index = 0;

    function show(i) {
      index = (i + bios.length) % bios.length;
      var b = bios[index];
      photo.innerHTML = '';
      if (b.photo) {
        var img = new Image();
        img.src = b.photo; img.alt = '';
        photo.appendChild(img);
      } else {
        var mono = document.createElement('span');
        mono.className = 'tm-mono tone-' + b.tone;
        var ini = document.createElement('span');
        ini.textContent = b.ini;
        mono.appendChild(ini);
        photo.appendChild(mono);
      }
      role.textContent = b.role;
      name.textContent = b.name;
      bio.textContent = b.bio;
      count.textContent = (index + 1) + ' of ' + bios.length;
      dialog.querySelector('.tm-dialog-body').scrollTop = 0;
    }

    document.addEventListener('click', function (e) {
      // A "Read bio" button, or a portrait on the map
      var btn = e.target.closest('[data-member]');
      if (!btn) return;
      var id = Number(btn.getAttribute('data-member'));
      for (var i = 0; i < bios.length; i++) if (bios[i].id === id) { show(i); break; }
      dialog.showModal();
    });
    dialog.querySelectorAll('[data-step]').forEach(function (b) {
      b.addEventListener('click', function () { show(index + Number(b.getAttribute('data-step'))); });
    });
    dialog.querySelector('.tm-dialog-close').addEventListener('click', function () { dialog.close(); });
    // A click on the dimmed backdrop (outside the card) closes the panel
    dialog.addEventListener('click', function (e) { if (e.target === dialog) dialog.close(); });
    dialog.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { e.preventDefault(); show(index + 1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); show(index - 1); }
    });
  }

  function initMap() {
    var map = document.querySelector('.tm-map');
    if (!map) return;
    var svg = map.querySelector('.tm-map-svg');
    var full = svg.getAttribute('viewBox');
    var narrow = window.matchMedia('(max-width: 980px)');
    function frame() { svg.setAttribute('viewBox', narrow.matches ? '30 43 572 640' : full); }
    frame();
    if (narrow.addEventListener) narrow.addEventListener('change', frame); else narrow.addListener(frame);

    function light(key) {
      map.querySelectorAll('[data-key]').forEach(function (el) {
        el.classList.toggle('is-on', !!key && el.getAttribute('data-key') === key);
      });
    }
    map.querySelectorAll('.tm-pin').forEach(function (pin) {
      var key = pin.getAttribute('data-key');
      pin.addEventListener('mouseenter', function () { light(key); });
      pin.addEventListener('mouseleave', function () { light(null); });
      pin.addEventListener('focusin', function () { light(key); });
      pin.addEventListener('focusout', function () { light(null); });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initBios();
    initMap();
  });
})();
