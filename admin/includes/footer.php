    </div>
  </div>
</div>
<script>
  var t = document.getElementById('adminToggle');
  var s = document.getElementById('adminSidebar');
  if (t && s) t.addEventListener('click', function () { s.classList.toggle('open'); });

  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
</script>
</body>
</html>
