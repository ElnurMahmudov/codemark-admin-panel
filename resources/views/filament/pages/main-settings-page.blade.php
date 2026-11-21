<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div style="padding-top: 1rem;">
            <x-filament::button type="submit" size="lg">
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>