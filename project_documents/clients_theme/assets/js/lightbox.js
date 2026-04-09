/**
 * StuNest lightbox — plain HTML/CSS/JS.
 *
 * Markup:
 *   <div data-lightbox-gallery="unique-id">
 *     <img src="..." alt="...">
 *   </div>
 *   Optional: open same gallery from elsewhere
 *   <img data-lightbox-open="unique-id" data-lightbox-index="0" ...>
 *
 * Any element with data-lightbox-open triggers open at data-lightbox-index (default 0).
 */
(function () {
  function buildOverlay() {
    var root = document.createElement('div');
    root.className = 'stunest-lightbox';
    root.setAttribute('role', 'dialog');
    root.setAttribute('aria-modal', 'true');
    root.setAttribute('aria-label', 'Image gallery');
    root.innerHTML =
      '<div class="stunest-lightbox__inner">' +
      '<span class="stunest-lightbox__counter" aria-hidden="true"></span>' +
      '<button type="button" class="stunest-lightbox__close" aria-label="Close">&times;</button>' +
      '<button type="button" class="stunest-lightbox__nav stunest-lightbox__nav--prev" aria-label="Previous photo">&lsaquo;</button>' +
      '<button type="button" class="stunest-lightbox__nav stunest-lightbox__nav--next" aria-label="Next photo">&rsaquo;</button>' +
      '<img class="stunest-lightbox__img" alt="">' +
      '<p class="stunest-lightbox__caption"></p>' +
      '</div>';
    return root;
  }

  function parseItemsFromGallery(container) {
    return Array.prototype.map.call(container.querySelectorAll('img'), function (img) {
      return {
        src: img.currentSrc || img.src,
        alt: img.getAttribute('alt') || '',
      };
    });
  }

  function initLightbox(overlay, state) {
    var imgEl = overlay.querySelector('.stunest-lightbox__img');
    var capEl = overlay.querySelector('.stunest-lightbox__caption');
    var counterEl = overlay.querySelector('.stunest-lightbox__counter');
    var btnClose = overlay.querySelector('.stunest-lightbox__close');
    var btnPrev = overlay.querySelector('.stunest-lightbox__nav--prev');
    var btnNext = overlay.querySelector('.stunest-lightbox__nav--next');

    function render() {
      var items = state.items;
      var i = state.index;
      if (!items.length) return;
      if (i < 0) i = items.length - 1;
      if (i >= items.length) i = 0;
      state.index = i;
      var item = items[i];
      imgEl.src = item.src;
      imgEl.alt = item.alt;
      capEl.textContent = item.alt || '';
      capEl.style.display = item.alt ? 'block' : 'none';
      counterEl.textContent = items.length > 1 ? i + 1 + ' / ' + items.length : '';
      btnPrev.style.visibility = items.length > 1 ? 'visible' : 'hidden';
      btnNext.style.visibility = items.length > 1 ? 'visible' : 'hidden';
    }

    function open(items, index) {
      state.items = items;
      state.index = typeof index === 'number' ? index : 0;
      render();
      overlay.classList.add('is-open');
      document.body.classList.add('stunest-lightbox-open');
    }

    function close() {
      overlay.classList.remove('is-open');
      document.body.classList.remove('stunest-lightbox-open');
      imgEl.removeAttribute('src');
    }

    function onKeydown(e) {
      if (!overlay.classList.contains('is-open')) return;
      if (e.key === 'Escape') {
        e.preventDefault();
        close();
      } else if (e.key === 'ArrowLeft') {
        e.preventDefault();
        state.index -= 1;
        render();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        state.index += 1;
        render();
      }
    }

    btnClose.addEventListener('click', close);
    btnPrev.addEventListener('click', function () {
      state.index -= 1;
      render();
    });
    btnNext.addEventListener('click', function () {
      state.index += 1;
      render();
    });
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });

    document.addEventListener('keydown', onKeydown);

    return { open: open, close: close };
  }

  document.addEventListener('DOMContentLoaded', function () {
    var overlay = buildOverlay();
    document.body.appendChild(overlay);

    var state = { items: [], index: 0 };
    var api = initLightbox(overlay, state);

    var galleries = {};
    document.querySelectorAll('[data-lightbox-gallery]').forEach(function (container) {
      var id = container.getAttribute('data-lightbox-gallery');
      if (!id) return;
      galleries[id] = parseItemsFromGallery(container);

      container.querySelectorAll('img').forEach(function (img, idx) {
        img.style.cursor = 'zoom-in';
        img.addEventListener(
          'click',
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            api.open(galleries[id], idx);
          },
          true
        );
      });
    });

    document.querySelectorAll('[data-lightbox-open]').forEach(function (el) {
      el.addEventListener(
        'click',
        function (e) {
          var id = el.getAttribute('data-lightbox-open');
          if (!id || !galleries[id]) return;
          e.preventDefault();
          e.stopPropagation();
          var idx = parseInt(el.getAttribute('data-lightbox-index') || '0', 10);
          api.open(galleries[id], idx);
        },
        true
      );
    });
  });
})();
