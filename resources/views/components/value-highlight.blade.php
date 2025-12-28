@props(['type' => 'immediate'])

@php
$highlights = [
    'immediate' => [
        'icon' => '⚡',
        'text' => 'Acesso Imediato',
        'color' => 'text-green-600'
    ],
    'affordable' => [
        'icon' => '💰',
        'text' => 'Preço Acessível',
        'color' => 'text-blue-600'
    ],
    'certificate' => [
        'icon' => '🏆',
        'text' => 'Certificado Incluído',
        'color' => 'text-purple-600'
    ],
    'lifetime' => [
        'icon' => '♾️',
        'text' => 'Acesso Vitalício',
        'color' => 'text-orange-600'
    ]
];

$highlight = $highlights[$type] ?? $highlights['immediate'];
@endphp

<div class="inline-flex items-center gap-1.5 {{ $highlight['color'] }} text-sm font-semibold">
    <span class="text-base">{{ $highlight['icon'] }}</span>
    <span>{{ $highlight['text'] }}</span>
</div>
