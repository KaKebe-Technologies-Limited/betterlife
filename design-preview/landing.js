// Story cards: arrow buttons scroll one card at a time; disabled at either end.
document.addEventListener('DOMContentLoaded', function () {
  var row = document.getElementById('lpCards');
  var arrows = document.querySelectorAll('.lp-arrow');
  if (!row || !arrows.length) return;

  function step() {
    var card = row.querySelector('.lp-card');
    var gap = parseFloat(getComputedStyle(row).columnGap) || 0;
    return card ? card.getBoundingClientRect().width + gap : row.clientWidth;
  }
  function update() {
    var max = row.scrollWidth - row.clientWidth - 1;
    arrows[0].disabled = row.scrollLeft <= 0;
    arrows[1].disabled = row.scrollLeft >= max;
  }
  arrows.forEach(function (btn) {
    btn.addEventListener('click', function () {
      row.scrollBy({ left: step() * parseInt(btn.dataset.dir, 10), behavior: 'smooth' });
    });
  });
  row.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);
  update();
});
