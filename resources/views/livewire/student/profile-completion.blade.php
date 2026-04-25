<div class="w-full max-w-2xl space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">{{ __('Complete your student profile') }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('Add a few details so you can use the StuNest student portal.') }}</p>
    </div>

    <form wire:submit="saveProfile" class="space-y-5">
        <div>
            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Student ID') }} <span class="text-red-500">*</span></label>
            <input
                type="text"
                id="student_id"
                wire:model.blur="student_id"
                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('student_id') border-red-300 @enderror"
            />
            @error('student_id')
                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="country_of_citizen" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Country of citizenship') }} <span class="text-red-500">*</span></label>
            <select
                id="country_of_citizen"
                wire:model="country_of_citizen"
                class="block w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('country_of_citizen') border-red-300 @enderror"
            >
                <option value="">{{ __('Select a country') }}</option>
                @foreach ($countries as $c)
                    <option value="{{ $c['code'] }}">{{ $c['name'] }}</option>
                @endforeach
            </select>
            @error('country_of_citizen')
                <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"
            >
                {{ __('Save and continue') }}
            </button>
        </div>
    </form>
</div>
