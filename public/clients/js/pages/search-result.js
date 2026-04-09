/**
 * Search results: filter pills + heart toggle.
 */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.filter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      this.classList.toggle('bg-gray-900');
      this.classList.toggle('text-white');
      this.classList.toggle('border-gray-900');
      if (this.classList.contains('bg-gray-900')) {
        this.classList.remove('border-gray-200', 'hover:border-black');
      } else {
        this.classList.add('border-gray-200', 'hover:border-black');
      }
    });
  });

  document.querySelectorAll('.heart-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var icon = this.querySelector('i');
      if (!icon) return;
      if (icon.classList.contains('text-red-500')) {
        icon.classList.remove('text-red-500', 'fill-red-500');
      } else {
        icon.classList.add('text-red-500', 'fill-red-500');
        this.style.transform = 'scale(1.2)';
        setTimeout(function () {
          btn.style.transform = 'scale(1)';
        }, 150);
      }
    });
  });
});
