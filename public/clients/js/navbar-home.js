/**
 * Marketing homepage navbar: solid background on scroll.
 */
document.addEventListener('DOMContentLoaded', function () {
  var navbar = document.getElementById('navbar');
  if (!navbar) return;

  function onScroll() {
    if (window.scrollY > 20) {
      navbar.classList.add('bg-white/80', 'backdrop-blur-md', 'border-b', 'border-gray-100', 'py-4');
      navbar.classList.remove('bg-transparent', 'py-6');
    } else {
      navbar.classList.remove('bg-white/80', 'backdrop-blur-md', 'border-b', 'border-gray-100', 'py-4');
      navbar.classList.add('bg-transparent', 'py-6');
    }
  }

  window.addEventListener('scroll', onScroll);
  onScroll();
});
