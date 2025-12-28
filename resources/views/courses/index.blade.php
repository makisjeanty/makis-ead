<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Cours - Étude Rapide</title>
    <meta name="description" content="Découvrez notre catalogue complet de cours en ligne premium. Programmation, Marketing, Design et plus encore.">
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/luxury-premium.css') }}">
</head>
<body class="bg-gray-50">
    {{-- Subscription Banner --}}
    <x-subscription-banner />
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container-premium py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/brand/logo.png') }}" alt="Étude Rapide" class="h-16">
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="/" class="text-gray-700 hover:text-purple-600 font-medium transition">Accueil</a>
                    <a href="/cursos" class="text-purple-600 font-semibold">Cours</a>
                    <a href="#" class="text-gray-700 hover:text-purple-600 font-medium transition">À Propos</a>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="text-purple-600 font-semibold">Dashboard</a>
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 font-medium transition">
                                Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-purple-600">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-premium">S'inscrire</a>
                    @endauth
                </div>

                <button class="md:hidden" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-purple-700 to-teal-600 text-white py-16">
        <div class="container-premium text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 logo-text">Catalogue de Cours Premium</h1>
            <p class="text-xl opacity-90 mb-8">Découvrez {{ $courses->total() }} cours pour transformer votre carrière</p>
            
            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('courses.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher un cours..." 
                           class="w-full px-6 py-4 rounded-full text-gray-900 text-lg focus:outline-none focus:ring-4 focus:ring-yellow-300">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-purple-600 to-yellow-500 text-white px-8 py-3 rounded-full font-semibold hover:opacity-90 transition">
                        Rechercher
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Filters & Courses -->
    <section class="py-12">
        <div class="container-premium">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="lg:w-1/4">
                    <div class="card-premium sticky top-24">
                        <h3 class="text-xl font-bold mb-6 text-purple-900">Filtres</h3>
                        
                        <form action="{{ route('courses.index') }}" method="GET" id="filterForm">
                            <!-- Keep search query -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Category Filter -->
                            <div class="mb-6">
                                <label class="block font-semibold mb-3 text-gray-700">Catégorie</label>
                                <select name="category" onchange="document.getElementById('filterForm').submit()" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->category }}" {{ request('category') == $cat->category ? 'selected' : '' }}>
                                            {{ $cat->category }} ({{ $cat->courses_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Level Filter -->
                            <div class="mb-6">
                                <label class="block font-semibold mb-3 text-gray-700">Niveau</label>
                                <select name="level" onchange="document.getElementById('filterForm').submit()" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Tous les niveaux</option>
                                    <option value="Iniciante" {{ request('level') == 'Iniciante' ? 'selected' : '' }}>Débutant</option>
                                    <option value="Intermediário" {{ request('level') == 'Intermediário' ? 'selected' : '' }}>Intermédiaire</option>
                                    <option value="Avançado" {{ request('level') == 'Avançado' ? 'selected' : '' }}>Avancé</option>
                                </select>
                            </div>

                            <!-- Price Filter -->
                            <div class="mb-6">
                                <label class="block font-semibold mb-3 text-gray-700">Prix</label>
                                <select name="price" onchange="document.getElementById('filterForm').submit()" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Tous les prix</option>
                                    <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>Gratuit</option>
                                    <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>Payant</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="mb-6">
                                <label class="block font-semibold mb-3 text-gray-700">Trier par</label>
                                <select name="sort" onchange="document.getElementById('filterForm').submit()" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Plus populaires</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Mieux notés</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Prix croissant</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Prix décroissant</option>
                                </select>
                            </div>

                            <!-- Clear Filters -->
                            @if(request()->hasAny(['category', 'level', 'price', 'sort', 'search']))
                                <a href="{{ route('courses.index') }}" class="block text-center px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                                    Réinitialiser les filtres
                                </a>
                            @endif
                        </form>

                        <!-- Stats -->
                        <div class="mt-8 p-4 bg-purple-50 rounded-lg">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ $courses->total() }}</div>
                                <div class="text-sm text-gray-600">Cours disponibles</div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Courses Grid -->
                <main class="lg:w-3/4">
                    <!-- Results Info -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="text-gray-600">
                            Affichage de <strong>{{ $courses->firstItem() ?? 0 }}</strong> à <strong>{{ $courses->lastItem() ?? 0 }}</strong> sur <strong>{{ $courses->total() }}</strong> cours
                        </div>
                    </div>

                    <!-- Courses Grid -->
                    @if($courses->count() > 0)
                        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                            @foreach($courses as $course)
                                <div class="card-premium group cursor-pointer" onclick="window.location.href='{{ route('courses.show', $course->slug) }}'">
                                    <!-- Course Image -->
                                    <div class="h-48 bg-gradient-to-br from-purple-400 to-yellow-400 rounded-xl mb-4 relative overflow-hidden">
                                        @if($course->image)
                                            <img src="{{ asset($course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                        @endif
                                        
                                        <!-- Pricing Tier Badge -->
                                        <div class="absolute top-3 left-3">
                                            <x-price-badge 
                                                :tier="$course->price_tier ?? 'pratico'" 
                                                :subscriptionOnly="$course->subscription_only ?? false" 
                                            />
                                        </div>

                                        <!-- Category Badge -->
                                        <span class="absolute top-3 right-3 px-3 py-1 bg-white bg-opacity-90 backdrop-blur-sm rounded-full text-xs font-bold text-purple-600">
                                            {{ $course->category }}
                                        </span>
                                    </div>

                                    <!-- Course Content -->
                                    <div>
                                        <h3 class="text-lg font-bold mb-2 group-hover:text-purple-600 transition line-clamp-2">
                                            {{ $course->title }}
                                        </h3>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                            {{ $course->description }}
                                        </p>

                                        <!-- Value Highlights -->
                                        <div class="flex flex-wrap gap-3 mb-3">
                                            <x-value-highlight type="immediate" />
                                            @if($course->price > 0 && $course->price < 50)
                                                <x-value-highlight type="affordable" />
                                            @endif
                                        </div>

                                        <!-- Meta Info -->
                                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
                                            <span class="flex items-center gap-1">
                                                <span>⭐</span>
                                                <span class="font-semibold">{{ number_format($course->rating, 1) }}</span>
                                            </span>
                                            <span>•</span>
                                            <span>{{ number_format($course->students_count) }} étudiants</span>
                                            <span>•</span>
                                            <span>{{ $course->level }}</span>
                                        </div>

                                        <!-- Footer -->
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                            <div class="text-2xl font-bold text-purple-600">
                                                @if($course->subscription_only)
                                                    <span class="text-sm text-gray-500">Assinatura</span>
                                                @elseif($course->price > 0)
                                                    R$ {{ number_format($course->price, 2, ',', '.') }}
                                                @else
                                                    <span class="text-green-600 font-bold">GRÁTIS</span>
                                                @endif
                                            </div>
                                            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition text-sm">
                                                @if($course->price == 0)
                                                    Começar Grátis
                                                @else
                                                    Ver Curso →
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $courses->links() }}
                        </div>
                    @else
                        <!-- No Results -->
                        <div class="text-center py-16">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-2xl font-bold mb-2 text-gray-900">Aucun cours trouvé</h3>
                            <p class="text-gray-600 mb-6">Essayez de modifier vos filtres ou votre recherche</p>
                            <a href="{{ route('courses.index') }}" class="btn-premium inline-flex">
                                Voir tous les cours
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-purple-700 to-teal-600 text-white">
        <div class="container-premium text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Comece Sua Jornada Hoje!</h2>
            <p class="text-xl opacity-90 mb-6">Cursos a partir de <span class="text-yellow-300 font-bold text-3xl">R$ 19,90</span></p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="bg-white text-purple-700 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition text-lg shadow-xl">
                    🎁 Começar Grátis
                </a>
                <a href="{{ route('pricing') }}" class="border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white hover:text-purple-700 transition text-lg">
                    💎 Ver Assinatura R$ 49,90/mês
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container-premium">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 logo-text text-gradient-royal">Étude Rapide</h3>
                    <p class="text-gray-400">Plateforme d'apprentissage premium</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Cours</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/cursos" class="hover:text-white">Tous les cours</a></li>
                        <li><a href="#" class="hover:text-white">Catégories</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Légal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Conditions</a></li>
                        <li><a href="#" class="hover:text-white">Confidentialité</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Étude Rapide. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>

    <style>
        /* Custom Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #E5E7EB;
            border-radius: 0.5rem;
            text-decoration: none;
            color: #6B21A8;
            font-weight: 500;
            transition: all 0.3s;
        }
        .pagination a:hover {
            background: #6B21A8;
            color: white;
            border-color: #6B21A8;
        }
        .pagination .active span {
            background: linear-gradient(135deg, #6B21A8, #F59E0B);
            color: white;
            border-color: transparent;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>
</html>
