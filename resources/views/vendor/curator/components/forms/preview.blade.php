@php
    $record = $getRecord();
    $src = $record->largeUrl ?? $record->url;
@endphp

<div class="flex items-center justify-center p-4 bg-gray-50/50 dark:bg-gray-900/50 rounded-xl overflow-hidden min-h-[300px]">
    <x-curator::display
        :item="$record"
        :src="$src"
        :constrained="true"
        icon-classes="h-24"
        class="max-h-[550px] w-auto max-w-full object-contain mx-auto rounded-lg shadow-sm"
    />
</div>
