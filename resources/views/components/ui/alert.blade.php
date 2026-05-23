@props(['type' => 'success', 'message' => null])

@php
    $colors = [
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'error' => 'bg-rose-50 text-rose-700 border-rose-100',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-100',
        'info' => 'bg-blue-50 text-blue-700 border-blue-100',
    ];
    
    $icons = [
        'success' => 'check_circle',
        'error' => 'error',
        'warning' => 'warning',
        'info' => 'info',
    ];

    $colorClass = $colors[$type] ?? $colors['success'];
    $icon = $icons[$type] ?? $icons['success'];
@endphp

@if($message || $slot->isNotEmpty())
    <div {{ $attributes->merge(['class' => $colorClass . ' p-4 rounded-2xl border flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500']) }} role="alert">
        <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        <div class="text-sm font-bold">
            {{ $message ?? $slot }}
        </div>
        <button type="button" @click="open = false" class="ml-auto text-current opacity-50 hover:opacity-100 transition-opacity">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
@endif
