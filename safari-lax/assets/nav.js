(function () {
  var b = document.querySelector('.nav-toggle'), n = document.querySelector('.site-nav');
  if (!b || !n) return;
  function close() { n.classList.remove('open'); b.setAttribute('aria-expanded', 'false'); }
  b.addEventListener('click', function () { var o = n.classList.toggle('open'); b.setAttribute('aria-expanded', o ? 'true' : 'false'); });
  n.addEventListener('click', function (e) { if (e.target.tagName === 'A') close(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();
