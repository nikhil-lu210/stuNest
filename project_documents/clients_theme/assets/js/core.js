/**
 * StuNest — shared bootstrap: HTML partials + Lucide refresh.
 * Requires: Tailwind (optional), Lucide loaded before this script.
 */
(function () {
  async function loadIncludes() {
    var nodes = document.querySelectorAll('[data-include]');
    for (var i = 0; i < nodes.length; i++) {
      var el = nodes[i];
      var path = el.getAttribute('data-include');
      if (!path) continue;
      try {
        var res = await fetch(path, { credentials: 'same-origin' });
        if (res.ok) el.innerHTML = await res.text();
      } catch (e) {
        console.warn('[StuNest] Include failed:', path, e);
      }
    }
  }

  function refreshIcons() {
    if (window.lucide && typeof lucide.createIcons === 'function') {
      lucide.createIcons();
    }
  }

  document.addEventListener('DOMContentLoaded', async function () {
    await loadIncludes();
    refreshIcons();
    document.dispatchEvent(new CustomEvent('stunest:ready'));
  });
})();
