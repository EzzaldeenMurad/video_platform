<x-form-section submit="updateTeamName">
    <x-slot name="title">
        {{ __('site.teams_name') }}
    </x-slot>

    <x-slot name="description">
        {{ __('site.team_and_members') }}
    </x-slot>

    <x-slot name="form">
        <!-- Team Owner Information -->
        <div class="col-span-6">
            <x-label value="{{ __('site.teams_own') }}" />

            <div class="flex items-center mt-2">
                <img class="w-12 h-12 rounded-full object-cover" src="{{ $team->owner->profile_photo_url }}"
                    alt="{{ $team->owner->name }}">

                <div class="ms-4 leading-tight">
                    <div class="text-gray-900">{{ $team->owner->name }}</div>
                    <div class="text-gray-700 text-sm">{{ $team->owner->email }}</div>
                </div>
            </div>
        </div>

        <!-- Team Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('site.teams_name') }}" />

            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name"
                :disabled="!Gate::check('update', $team)" />

            <x-input-error for="name" class="mt-2" />
        </div>
    </x-slot>

    @if (Gate::check('update', $team))
        <x-slot name="actions">
            <x-action-message class="me-3" on="saved">
                {{ __('site.teams_saved') }}
            </x-action-message>

            <x-button>
                {{ __('site.teams_save') }}
            </x-button>
        </x-slot>
    @endif
</x-form-section>
