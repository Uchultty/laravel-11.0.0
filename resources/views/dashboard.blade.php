<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <p class="text-sm text-ink-700">{{ __("You're logged in!") }}</p>
        </x-ui.card>
    </div>
</x-app-layout>
