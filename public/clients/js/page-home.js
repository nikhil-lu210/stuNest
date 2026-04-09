/**
 * Home page: search bar focus ring.
 */
document.addEventListener('DOMContentLoaded', function () {
  var searchInput = document.getElementById('location-input');
  var searchContainer = document.getElementById('search-container');
  if (!searchInput || !searchContainer) return;

  searchInput.addEventListener('focus', function () {
    searchContainer.classList.remove('border-gray-200');
    searchContainer.classList.add('border-gray-400', 'shadow-lg');
  });

  searchInput.addEventListener('blur', function () {
    searchContainer.classList.remove('border-gray-400', 'shadow-lg');
    searchContainer.classList.add('border-gray-200');
  });
});
