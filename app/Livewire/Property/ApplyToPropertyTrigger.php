<?php

namespace App\Livewire\Property;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Dispatches openApplicationModal so the Apply slide-over can live outside the button in the DOM.
 */
class ApplyToPropertyTrigger extends Component
{
    public string $buttonClass = '';

    public string $label = '';

    public function open(): void
    {
        // Target the slide-over explicitly (multiple Livewire islands on the page).
        $this->dispatch('openApplicationModal')->to(ApplyToProperty::class);
    }

    public function render(): View
    {
        return view('livewire.property.apply-to-property-trigger');
    }
}
