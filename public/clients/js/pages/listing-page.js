/**
 * Listing detail: save (favourite) + lucide refresh.
 */
function listingRefreshLucide() {
  if (window.lucide && typeof lucide.createIcons === 'function') {
    lucide.createIcons();
  }
}

function listingSetupFavorites() {
  var token =
    document.querySelector('meta[name="csrf-token"]') &&
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  var holder = document.querySelector('[data-explore-favorite-prefix]');
  var prefix = holder && holder.getAttribute('data-explore-favorite-prefix');
  if (!prefix) {
    return;
  }

  document.querySelectorAll('.explore-heart-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var id = btn.getAttribute('data-property-id');
      if (!id) {
        return;
      }
      var url = prefix + id;
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token || '',
        },
        credentials: 'same-origin',
        body: JSON.stringify({}),
      })
        .then(function (res) {
          if (res.status === 401) {
            return res.json().then(function (data) {
              var login = (data && data.login_url) || '/login';
              window.location.href = login;
            });
          }
          if (res.status === 503) {
            return res.json().then(function (data) {
              var msg = data && data.message;
              if (msg && typeof console !== 'undefined' && console.warn) {
                console.warn('[listing favorites]', msg);
              }
              return null;
            }).catch(function () {
              return null;
            });
          }
          if (!res.ok) {
            return res.text().then(function (body) {
              if (typeof console !== 'undefined' && console.warn) {
                console.warn('[listing favorites]', res.status, body || '');
              }
              throw new Error('Request failed');
            });
          }
          return res.json();
        })
        .then(function (data) {
          if (!data) {
            return;
          }
          var saved = !!data.saved;
          btn.setAttribute('data-saved', saved ? '1' : '0');
          var icon = btn.querySelector('svg') || btn.querySelector('i');
          btn.classList.toggle('text-red-500', saved);
          btn.classList.toggle('text-gray-900', !saved);
          if (icon) {
            icon.classList.toggle('text-red-500', saved);
            icon.classList.toggle('fill-red-500', saved);
            icon.classList.toggle('text-gray-900', !saved);
          }
        })
        .catch(function (err) {
          if (typeof console !== 'undefined' && console.warn) {
            console.warn('[listing favorites]', err && err.message ? err.message : err);
          }
        });
    });
  });
}

document.addEventListener('DOMContentLoaded', function () {
  listingSetupFavorites();
  listingRefreshLucide();
});
