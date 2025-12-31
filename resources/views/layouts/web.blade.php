<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Étude Rapide') - Étude Rapide</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/luxury-premium.css') }}">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #6B21A8 0%, #9333EA 50%, #0D9488 100%);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md shadow-sm" x-data="{ open: false }">
        <div class="container-premium">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="relative w-10 h-10 overflow-hidden rounded-xl shadow-lg group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-teal-400 opacity-20"></div>
                        <img src="{{ asset('images/brand/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <span class="text-2xl font-bold logo-text text-gradient-royal">Étude Rapide</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="/" class="nav-link font-medium hover:text-purple-600 transition">Accueil</a>
                    <a href="{{ route('courses.index') }}" class="nav-link font-medium hover:text-purple-600 transition">Cours</a>
                    <a href="{{ route('blog.index') }}" class="nav-link font-medium hover:text-purple-600 transition">Blog</a>
                    <a href="/contact" class="nav-link font-medium hover:text-purple-600 transition">Contact</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-premium px-6 py-2.5 rounded-full text-sm">Mon Tableau de Bord</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-gray-700 hover:text-purple-600 transition">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-premium px-6 py-2.5 rounded-full text-sm">S'inscrire</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden bg-white border-t">
            <div class="container-premium py-4 space-y-4">
                <a href="/" class="block font-medium hover:text-purple-600">Accueil</a>
                <a href="{{ route('courses.index') }}" class="block font-medium hover:text-purple-600">Cours</a>
                <a href="{{ route('blog.index') }}" class="block font-medium hover:text-purple-600">Blog</a>
                <a href="/contact" class="block font-medium hover:text-purple-600">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block btn-premium text-center">Mon Tableau de Bord</a>
                @else
                    <div class="flex flex-col gap-3 pt-4 border-t">
                        <a href="{{ route('login') }}" class="block text-center font-bold text-gray-700">Connexion</a>
                        <a href="{{ route('register') }}" class="block btn-premium text-center">S'inscrire</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="pt-20 min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 mt-12">
        <div class="container-premium">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4 logo-text text-gradient-royal">Étude Rapide</h3>
                    <p class="text-gray-400">Plateforme d'apprentissage premium pour la francophonie.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Liens Rapides</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/" class="hover:text-white transition">Accueil</a></li>
                        <li><a href="{{ route('courses.index') }}" class="hover:text-white transition">Cours</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                        <li><a href="/contact" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Légal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Conditions d'utilisation</a></li>
                        <li><a href="#" class="hover:text-white transition">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>support@etuderapide.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500">
                &copy; {{ date('Y') }} Étude Rapide. Tous droits réservés.
            </div>
        </div>
    </footer>

</body>
</html>
