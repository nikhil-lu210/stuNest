/**
 * Select2 for all administration `<select class="form-select">` except opt-outs.
 * - `.no-select2` — native select
 * - `.geo-managed` — country/city/area cascades; page script owns destroy/init
 *
 * Search box appears when there are at least `data-min-search-options` options (default 12).
 * Set `data-allow-clear="true"` for clearable selects.
 *
 * Dropdown parent: modal → Bootstrap column → card → body. Using the column keeps the
 * dropdown aligned to the field width on responsive layouts (avoids full-card-wide menus).
 */
import '../css/admin-select2.css';

function resolveDropdownParent($el) {
    const $ = window.jQuery;
    const $modal = $el.closest('.modal');
    if ($modal.length) {
        return $modal;
    }
    const $col = $el.closest('[class*="col-"]');
    if ($col.length) {
        return $col;
    }
    const $card = $el.closest('.card');
    if ($card.length) {
        return $card;
    }
    return $(document.body);
}

function buildOptions($el) {
    const $ = window.jQuery;
    const dropdownParent = resolveDropdownParent($el);
    $el.data('adminSelect2DropdownParent', dropdownParent);

    const allowClear = $el.attr('data-allow-clear') === 'true';
    const placeholder = $el.attr('data-placeholder') || '';
    const rawMin = $el.attr('data-min-search-options');
    let minSearch = 12;
    if (rawMin !== undefined && rawMin !== '') {
        const n = parseInt(rawMin, 10);
        if (Number.isFinite(n)) {
            minSearch = n;
        }
    }

    return {
        width: '100%',
        allowClear,
        placeholder: placeholder || undefined,
        dropdownParent,
        dropdownCssClass: 'admin-select2-dropdown',
        minimumResultsForSearch: minSearch,
    };
}

function syncOpenDropdownWidth($el) {
    const $ = window.jQuery;
    const $container = $el.next('.select2-container');
    if (!$container.length) {
        return;
    }
    const w = $container.outerWidth();
    if (!w) {
        return;
    }
    const $dropdownParent = $el.data('adminSelect2DropdownParent');
    let $dropdown = $();
    if ($dropdownParent && $dropdownParent.length) {
        $dropdown = $dropdownParent.find('.select2-dropdown').last();
    }
    if (!$dropdown.length) {
        $dropdown = $container.find('.select2-dropdown');
    }
    if (!$dropdown.length) {
        $dropdown = $('.select2-container--open').last().find('.select2-dropdown');
    }
    if ($dropdown.length) {
        $dropdown.css({
            width: w,
            maxWidth: '100%',
            minWidth: 0,
            boxSizing: 'border-box',
        });
    }
}

function bindWidthSync($el) {
    const $ = window.jQuery;
    $el.off('select2:open.adminSelect2').on('select2:open.adminSelect2', function () {
        const self = $(this);
        window.requestAnimationFrame(function () {
            syncOpenDropdownWidth(self);
        });
    });
}

function initAdminSelect2() {
    const $ = window.jQuery;
    if (!$ || !$.fn.select2) {
        return;
    }

    $('select.form-select:not(.no-select2):not(.geo-managed)').each(function () {
        const $el = $(this);
        if ($el.hasClass('select2-hidden-accessible')) {
            return;
        }
        $el.select2(buildOptions($el));
        bindWidthSync($el);
    });
}

document.addEventListener('DOMContentLoaded', initAdminSelect2);

window.initAdminSelect2 = initAdminSelect2;
