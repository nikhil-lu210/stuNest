/**
 * Select2 for all administration `<select class="form-select">` except opt-outs.
 * - `.no-select2` — native select
 * - `.geo-managed` — country/city/area cascades; page script owns destroy/init
 *
 * Search box appears when there are at least `data-min-search-options` options (default 12).
 * Set `data-allow-clear="true"` for clearable selects.
 */
import '../css/admin-select2.css';

function buildOptions($el) {
    const $ = window.jQuery;
    const dropdownParent = $el.closest('.modal').length ? $el.closest('.modal') : $(document.body);
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
        minimumResultsForSearch: minSearch,
    };
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
    });
}

document.addEventListener('DOMContentLoaded', initAdminSelect2);

window.initAdminSelect2 = initAdminSelect2;
