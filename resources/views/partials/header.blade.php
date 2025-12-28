{{-- Header Navigation --}}
<header class="bg-white shadow-sm sticky top-0 z-50">
    <nav class="container-premium py-4">
        <div class="flex justify-between items-center">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/brand/logo.png') }}" alt="Étude Rapide" class="h-16">
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="/cursos" class="text-gray-700 hover:text-purple-600 font-medium transition">Cours</a>
                <a href="/#features" class="text-gray-700 hover:text-purple-600 font-medium transition">Fonctionnalités</a>
                <a href="/pricing" class="text-gray-700 hover:text-purple-600 font-medium transition">Tarifs</a>
            </div>

            {{-- Auth Buttons --}}
            <div class="hidden md:flex items-center gap-4">
                @auth
                    <a href="{{ route('student.dashboard') }}" class="text-purple-600 font-semibold hover:text-purple-800">Dashboard</a>
                    
                    {{-- Logout Button --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 font-medium hover:text-red-600 transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-purple-600">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-premium">
                        Commencer Gratuitement
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <button class="md:hidden" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4">
            <div class="flex flex-col gap-4">
                <a href="/cursos" class="text-gray-700 font-medium">Cours</a>
                <a href="/#features" class="text-gray-700 font-medium">Fonctionnalités</a>
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 font-medium">Connexion</a>
                    <a href="{{ route('register') }}" class="btn-premium justify-center">Commencer</a>
                @endguest
            </div>
        </div>
    </nav>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }
</script>
