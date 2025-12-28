<!-- Language Switcher Component -->
<div class="relative inline-block text-left">
    <button type="button" onclick="toggleDropdown('lang-dropdown')" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
        </svg>
        <span class="text-sm font-medium">
            @switch(app()->getLocale())
                @case('fr') Français @break
                @case('ht') Kreyòl @break
                @case('en') English @break
                @case('pt') Português @break
                @default Français
            @endswitch
        </span>
    </button>

    <div id="lang-dropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
            <a href="?lang=fr" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'fr' ? 'bg-gray-50 font-semibold' : '' }}">
                🇫🇷 Français
            </a>
            <a href="?lang=ht" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'ht' ? 'bg-gray-50 font-semibold' : '' }}">
                🇭🇹 Kreyòl Ayisyen
            </a>
            <a href="?lang=en" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-50 font-semibold' : '' }}">
                🇬🇧 English
            </a>
            <a href="?lang=pt" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'pt' ? 'bg-gray-50 font-semibold' : '' }}">
                🇧🇷 Português
            </a>
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
