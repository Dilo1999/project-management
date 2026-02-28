@props([
    'label',
    'value',
    'iconWrapperClass' => 'bg-slate-100',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-5']) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ $label }}</p>
            <p class="text-2xl font-semibold text-gray-800 mt-1">{{ $value }}</p>
        </div>
        <div class="h-11 w-11 rounded-lg flex items-center justify-center {{ $iconWrapperClass }}">
            {{ $slot }}
        </div>
    </div>
</div>
