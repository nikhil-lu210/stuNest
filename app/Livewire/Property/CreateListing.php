<?php

namespace App\Livewire\Property;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Property\Property;
use App\Services\GoogleMapsUrlNormalizer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class CreateListing extends Component
{
    use WithFileUploads;

    /** Combined size limit for all property photos (bytes). */
    private const PHOTOS_MAX_TOTAL_BYTES = 10 * 1024 * 1024;

    public int $currentStep = 1;

    public string $listing_category = '';

    public ?int $country_id = null;

    public ?int $city_id = null;

    public ?int $area_id = null;

    public string $map_link = '';

    public ?string $latitude = null;

    public ?string $longitude = null;

    public ?string $distance_university_km = null;

    public ?string $distance_transit_km = null;

    public string $property_type = '';

    public ?string $bed_type = null;

    public int $bedrooms = 1;

    public int $bathrooms = 1;

    public string $bathroom_type = '';

    public bool $is_furnished = false;

    public string $rent_duration = '';

    public ?int $rent_amount = null;

    public string $bills_included = '';

    /** @var array<int, string> */
    public array $included_bills = [];

    public string $min_contract_length = '';

    public bool $provides_agreement = false;

    public string $deposit_required = '';

    public string $rent_for = '';

    /** @var array<int, string> */
    public array $suitable_for = [];

    public ?string $flatmate_vibe = null;

    /** @var array<int, string> */
    public array $house_rules = [];

    /** @var array<int, string> */
    public array $amenities = [];

    /** @var array<int, \Illuminate\Http\UploadedFile> */
    public array $photos = [];

    public int $photosCount = 0;

    /** User choice on the final step: save without publishing, or publish live. */
    public string $save_as = 'draft';

    public function mount(): void
    {
        if (request()->routeIs('client.student.create-listing')) {
            abort_unless($this->isStudent(), 403);
        }

        if ($this->isStudent()) {
            $this->listing_category = 'shared_room';
        }
    }

    public function hydrate(): void
    {
        $this->photosCount = count($this->photos);
    }

    public function updatedPhotos(): void
    {
        $this->photosCount = count($this->photos);
        $this->resetValidation('photos');
        if ($this->getPhotosTotalBytes() > self::PHOTOS_MAX_TOTAL_BYTES) {
            $this->addError('photos', __('The total size of all photos must not exceed 10 MB.'));
        }
    }

    private function getPhotosTotalBytes(): int
    {
        $total = 0;
        foreach ($this->photos as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $total += $file->getSize();
            }
        }

        return $total;
    }

    public function updatedCountryId(mixed $value): void
    {
        $this->country_id = $value === '' || $value === null ? null : (int) $value;
        $this->city_id = null;
        $this->area_id = null;
    }

    public function updatedCityId(mixed $value): void
    {
        $this->city_id = $value === '' || $value === null ? null : (int) $value;
        $this->area_id = null;
    }

    public function updatedListingCategory(string $value): void
    {
        if ($this->isStudent() && $value !== 'shared_room') {
            $this->listing_category = 'shared_room';

            return;
        }

        if ($value !== 'shared_room') {
            $this->flatmate_vibe = null;
            $this->bed_type = null;
        }
    }

    public function updatedBillsIncluded(string $value): void
    {
        if ($value !== 'some') {
            $this->included_bills = [];
        }
    }

    /**
     * Student listers: spare seat / shared room only (column slug + Spatie role).
     */
    public function isStudent(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        if (($user->role ?? null) === 'student') {
            return true;
        }

        return $user->hasStudentRole();
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->currentStep));
        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rulesForStep(int $step): array
    {
        $listingCategories = $this->isStudent()
            ? ['shared_room']
            : ['entire_place', 'shared_room'];

        $includedBillsRules = $this->bills_included === 'some'
            ? ['required', 'array', 'min:1']
            : ['nullable', 'array'];

        return match ($step) {
            1 => [
                'listing_category' => ['required', Rule::in($listingCategories)],
                'country_id' => ['required', 'integer', 'exists:countries,id'],
                'city_id' => [
                    'required',
                    'integer',
                    Rule::exists('cities', 'id')->where(fn ($q) => $q->where('country_id', $this->country_id)),
                ],
                'area_id' => [
                    'required',
                    'integer',
                    Rule::exists('areas', 'id')->where(fn ($q) => $q->where('city_id', $this->city_id)),
                ],
                'map_link' => ['required', 'string', 'max:2048', 'url'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'distance_university_km' => ['required', 'numeric', 'min:0', 'max:999.99'],
                'distance_transit_km' => ['required', 'numeric', 'min:0', 'max:999.99'],
            ],
            2 => [
                'property_type' => ['required', Rule::in(['studio', 'apartment', 'house', 'student_seat'])],
                'bed_type' => [
                    Rule::requiredIf(fn () => $this->listing_category === 'shared_room'),
                    'nullable',
                    Rule::in(['single', 'shared_double']),
                ],
                'bedrooms' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6])],
                'bathrooms' => ['required', 'integer', Rule::in([1, 2, 3])],
                'bathroom_type' => ['required', Rule::in(['private_ensuite', 'shared'])],
                'is_furnished' => ['boolean'],
            ],
            3 => [
                'rent_duration' => ['required', Rule::in(['day', 'week', 'month'])],
                'rent_amount' => ['required', 'integer', 'min:1'],
                'bills_included' => ['required', Rule::in(['all', 'some', 'none'])],
                'included_bills' => $includedBillsRules,
                'included_bills.*' => [Rule::in(['wifi', 'water', 'electricity', 'gas'])],
                'min_contract_length' => ['required', Rule::in(['1_month', '3_months', '6_months', '1_year', 'flexible'])],
                'provides_agreement' => ['boolean'],
                'deposit_required' => ['required', Rule::in(['none', '1_month', '5_weeks'])],
                'rent_for' => ['required', Rule::in(['only_boys', 'only_girls', 'couples', 'anyone'])],
            ],
            4 => [
                'suitable_for' => ['required', 'array', 'min:1'],
                'suitable_for.*' => [Rule::in(['undergraduates', 'postgraduates', 'couples'])],
                'flatmate_vibe' => [
                    Rule::requiredIf(fn () => $this->listing_category === 'shared_room'),
                    'nullable',
                    Rule::in(['all_male', 'all_female', 'mixed']),
                ],
                'house_rules' => ['array'],
                'house_rules.*' => [Rule::in(['pet_friendly', 'smoking_allowed', 'quiet_house'])],
            ],
            5 => [
                'amenities' => ['required', 'array', 'min:1'],
                'amenities.*' => [Rule::in(['wifi', 'washing_machine', 'tumble_dryer', 'dishwasher', 'balcony_garden', 'desk_in_room', 'building_gym', 'bike_storage'])],
                'save_as' => ['required', Rule::in(['draft', 'published'])],
            ],
            default => [],
        };
    }

    public function submitDraft(): void
    {
        $this->submitListing('draft');
    }

    public function submitPublished(): void
    {
        $this->submitListing('published');
    }

    protected function submitListing(string $saveAs = 'draft'): void
    {
        $this->save_as = in_array($saveAs, ['draft', 'published'], true) ? $saveAs : 'draft';

        for ($s = 1; $s <= 5; $s++) {
            $this->validate($this->rulesForStep($s));
        }

        $this->validate([
            'photos' => [
                'required',
                'array',
                'min:3',
                'max:10',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_array($value)) {
                        return;
                    }
                    $total = 0;
                    foreach ($value as $file) {
                        if ($file instanceof TemporaryUploadedFile) {
                            $total += $file->getSize();
                        }
                    }
                    if ($total > self::PHOTOS_MAX_TOTAL_BYTES) {
                        $fail(__('The total size of all photos must not exceed 10 MB.'));
                    }
                },
            ],
            'photos.*' => ['image'],
        ]);

        if ($this->bills_included !== 'some') {
            $this->included_bills = [];
        }

        if ($this->isStudent()) {
            $this->listing_category = 'shared_room';
        }

        $maps = app(GoogleMapsUrlNormalizer::class)->normalize($this->map_link);
        $this->map_link = $maps['url'];
        $latitude = ($maps['latitude'] !== null && $maps['longitude'] !== null)
            ? $maps['latitude']
            : $this->normalizeOptionalDecimal($this->latitude);
        $longitude = ($maps['latitude'] !== null && $maps['longitude'] !== null)
            ? $maps['longitude']
            : $this->normalizeOptionalDecimal($this->longitude);

        DB::transaction(function () use ($latitude, $longitude) {
            $property = Property::create([
                'user_id' => Auth::id(),
                'country_id' => $this->country_id,
                'city_id' => $this->city_id,
                'area_id' => $this->area_id,
                'map_link' => $this->map_link,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance_university_km' => $this->distance_university_km,
                'distance_transit_km' => $this->distance_transit_km,
                'bed_type' => $this->listing_category === 'shared_room' ? $this->bed_type : null,
                'listing_category' => $this->listing_category,
                'property_type' => $this->property_type,
                'bedrooms' => $this->bedrooms,
                'bathrooms' => $this->bathrooms,
                'bathroom_type' => $this->bathroom_type,
                'is_furnished' => $this->is_furnished,
                'rent_duration' => $this->rent_duration,
                'rent_amount' => (int) $this->rent_amount,
                'bills_included' => $this->bills_included,
                'included_bills' => $this->bills_included === 'some' ? $this->included_bills : [],
                'min_contract_length' => $this->min_contract_length,
                'provides_agreement' => $this->provides_agreement,
                'deposit_required' => $this->deposit_required,
                'rent_for' => $this->rent_for,
                'suitable_for' => $this->suitable_for,
                'flatmate_vibe' => $this->listing_category === 'shared_room' ? $this->flatmate_vibe : null,
                'house_rules' => $this->house_rules,
                'amenities' => $this->amenities,
                'capacity' => max(1, (int) $this->bedrooms),
                'available_beds' => max(1, (int) $this->bedrooms),
                'status' => $this->save_as === 'published'
                    ? Property::STATUS_PUBLISHED
                    : Property::STATUS_DRAFT,
            ]);

            foreach ($this->photos as $photo) {
                $property->addMedia($photo->getRealPath())->toMediaCollection('property_gallery');
                if ($photo instanceof TemporaryUploadedFile) {
                    $photo->delete();
                }
            }
        });

        $this->photos = [];
        $this->photosCount = 0;

        session()->flash(
            'success',
            $this->save_as === 'published'
                ? __('Your property listing has been published.')
                : __('Your property listing has been saved as a draft.')
        );

        $this->redirect(
            $this->isStudent()
                ? route('client.student.dashboard')
                : route('administration.dashboard.index')
        );
    }

    protected function normalizeOptionalDecimal(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value;
    }

    public function render(): View
    {
        return view('livewire.property.create-listing', [
            'countries' => Country::query()->active()->orderBy('name')->get(),
            'cities' => $this->country_id
                ? City::query()->active()->where('country_id', $this->country_id)->orderBy('name')->get()
                : collect(),
            'areas' => $this->city_id
                ? Area::query()->active()->where('city_id', $this->city_id)->orderBy('name')->get()
                : collect(),
        ])->layout('layouts.property-wizard', [
            'title' => $this->isStudent()
                ? __('List a Room/Seat')
                : __('Create property listing'),
        ]);
    }
}
