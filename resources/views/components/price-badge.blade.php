@props(['tier' => 'pratico', 'subscriptionOnly' => false])

@php
$badges = [
    'gratuito' => [
        'text' => 'GRATUITO',
        'bg' => 'bg-green-500',
        'icon' => '🎁'
    ],
    'iniciante' => [
        'text' => 'INICIANTE',
        'bg' => 'bg-blue-400',
        'icon' => '🟢'
    ],
    'pratico' => [
        'text' => 'PRÁTICO',
        'bg' => 'bg-blue-600',
        'icon' => '🔵'
    ],
    'profissional' => [
        'text' => 'PROFISSIONAL',
        'bg' => 'bg-purple-600',
        'icon' => '🟣'
    ],
    'assinatura' => [
        'text' => 'ASSINATURA',
        'bg' => 'bg-gradient-to-r from-yellow-400 to-orange-500',
        'icon' => '💎'
    ]
];

$badge = $badges[$tier] ?? $badges['pratico'];
@endphp

<div class="inline-flex items-center gap-1.5 {{ $badge['bg'] }} text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
    <span>{{ $badge['icon'] }}</span>
    <span>{{ $badge['text'] }}</span>
    @if($subscriptionOnly)
        <span class="ml-1 text-xs opacity-90">ONLY</span>
    @endif
</div>
