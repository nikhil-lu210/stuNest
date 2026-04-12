import '../css/property-wizard.css';
import jQuery from 'jquery';
import 'select2';
import 'select2/dist/css/select2.min.css';

window.jQuery = window.$ = jQuery;

const LOCATION_SELECT_IDS = ['location-country', 'location-city', 'location-area'];

function destroySelect2InSubtree(el) {
    if (!el || el.nodeType !== Node.ELEMENT_NODE) {
        return;
    }

    LOCATION_SELECT_IDS.forEach((id) => {
        const node = el.id === id ? el : el.querySelector('#' + id);
        if (!node) {
            return;
        }
        const $node = jQuery(node);
        if ($node.hasClass('select2-hidden-accessible')) {
            $node.select2('destroy');
        }
    });
}

function initLocationSelects() {
    const country = document.getElementById('location-country');
    if (!country) {
        return;
    }

    LOCATION_SELECT_IDS.forEach((id) => {
        const el = document.getElementById(id);
        if (!el) {
            return;
        }
        const $el = jQuery(el);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        const placeholder = el.dataset.placeholder || '';

        $el.select2({
            width: '100%',
            placeholder,
            allowClear: false,
            disabled: el.disabled,
            dropdownParent: jQuery(document.body),
            minimumResultsForSearch: 12,
        });
    });
}

let initScheduled = null;

function scheduleInitLocationSelects() {
    if (initScheduled !== null) {
        cancelAnimationFrame(initScheduled);
    }
    initScheduled = requestAnimationFrame(() => {
        initScheduled = null;
        initLocationSelects();
    });
}

let livewireSelect2HooksRegistered = false;

function registerLivewireSelect2Hooks() {
    if (!window.Livewire || livewireSelect2HooksRegistered) {
        return;
    }

    livewireSelect2HooksRegistered = true;

    Livewire.hook('morph.removing', ({ el }) => {
        destroySelect2InSubtree(el);
    });

    Livewire.hook('morph.updated', () => {
        scheduleInitLocationSelects();
    });

    scheduleInitLocationSelects();
}

function bootPropertyWizardSelect2() {
    if (window.Livewire) {
        registerLivewireSelect2Hooks();
    }
}

document.addEventListener('livewire:init', bootPropertyWizardSelect2);

// Livewire loads before this module (script is after @livewireScripts).
bootPropertyWizardSelect2();

// First paint / hydration edge cases: retry until step-1 selects exist.
function initLocationSelectsWhenReady(attempt = 0) {
    if (document.getElementById('location-country')) {
        scheduleInitLocationSelects();

        return;
    }

    if (attempt < 60) {
        requestAnimationFrame(() => initLocationSelectsWhenReady(attempt + 1));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initLocationSelectsWhenReady();
});
