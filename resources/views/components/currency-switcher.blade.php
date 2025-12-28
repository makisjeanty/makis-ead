<!-- Currency Switcher Component -->
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $currentCurrency = $currencyService->getUserCurrency();
    $currencies = $currencyService->getSupportedCurrencies();
@endphp

<div class="relative inline-block text-left">
    <button type="button" onclick="toggleDropdown('currency-dropdown')" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium">{{ $currencies[$currentCurrency]['symbol'] }} {{ $currentCurrency }}</span>
    </button>

    <div id="currency-dropdown" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
            @foreach($currencies as $code => $currency)
                <form action="{{ route('currency.set') }}" method="POST" class="inline w-full">
                    @csrf
                    <input type="hidden" name="currency" value="{{ $code }}">
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentCurrency === $code ? 'bg-gray-50 font-semibold' : '' }}">
                        <span class="font-mono">{{ $currency['symbol'] }}</span> {{ $currency['name'] }} ({{ $code }})
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('hidden');
    
    // Close when clicking outside
    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest(`#${id}`) && !e.target.closest('button')) {
            dropdown.classList.add('hidden');
            document.removeEventListener('click', closeDropdown);
        }
    });
}
</script>
