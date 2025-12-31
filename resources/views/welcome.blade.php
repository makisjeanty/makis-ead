<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étude Rapide - Apprentissage Premium</title>
    <meta name="description" content="Plateforme d'apprentissage en ligne premium pour développer vos compétences rapidement.">
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/luxury-premium.css') }}">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #6B21A8 0%, #9333EA 50%, #0D9488 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-25px); }
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #6B21A8, #F59E0B, #0D9488);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card-hover:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .text-gradient {
            background: linear-gradient(135deg, #6B21A8, #0D9488);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, rgba(107, 33, 168, 0.1), rgba(13, 148, 136, 0.1));
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container-premium py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/brand/logo.png') }}" alt="Étude Rapide" class="h-16">
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="/cursos" class="text-gray-700 hover:text-purple-600 font-medium transition relative group">
                        Cours
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#features" class="text-gray-700 hover:text-purple-600 font-medium transition relative group">
                        Fonctionnalités
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="/pricing" class="text-gray-700 hover:text-purple-600 font-medium transition relative group">
                        Tarifs
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition relative group">
                        Blog
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="/contact" class="text-gray-700 hover:text-purple-600 font-medium transition relative group">
                        Contact
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-purple-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="text-purple-600 font-semibold hover:text-purple-800 transition">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 font-medium hover:text-red-600 transition ml-2">
                                Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-purple-600 transition">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-premium relative overflow-hidden group">
                            <span class="relative z-10">Commencer Gratuitement</span>
                            <span class="absolute inset-0 bg-gradient-to-r from-purple-700 to-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4">
                <div class="flex flex-col gap-4">
                    <a href="/cursos" class="text-gray-700 font-medium hover:text-purple-600">Cours</a>
                    <a href="#features" class="text-gray-700 font-medium hover:text-purple-600">Fonctionnalités</a>
                    <a href="/pricing" class="text-gray-700 font-medium hover:text-purple-600">Tarifs</a>
                    <a href="{{ route('blog.index') }}" class="text-gray-700 font-medium hover:text-purple-600">Blog</a>
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="text-purple-600 font-medium hover:text-purple-800">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 font-medium w-full text-left">
                                Déconnexion
                            </button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 font-medium hover:text-purple-600">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-premium justify-center">Commencer</a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-16 md:py-28 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse animation-delay-4000"></div>
        </div>

        <div class="container-premium relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-3 bg-white bg-opacity-20 backdrop-blur-sm px-6 py-3 rounded-full mb-6">
                        <span class="text-2xl">✨</span>
                        <span class="text-base font-semibold">Plateforme #1 en Haïti</span>
                    </div>

                    <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                        Apprenez <span class="text-gradient-royal">Rapidement</span>,
                        <br />
                        <span class="text-yellow-300">Réussissez Brillamment</span>
                    </h1>

                    <p class="text-lg md:text-xl opacity-90 leading-relaxed max-w-lg">
                        Transformez votre avenir avec des cours en ligne de qualité premium.
                        Rejoignez 10,000+ étudiants qui réussissent.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mt-10">
                        <a href="{{ route('register') }}" class="btn-premium text-lg px-8 py-4 justify-center relative overflow-hidden group">
                            <span class="relative z-10 flex items-center gap-2">
                                <span>Commencer Gratuitement</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                            <span class="absolute inset-0 bg-gradient-to-r from-purple-700 to-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        </a>

                        <a href="/cursos" class="px-8 py-4 bg-white text-purple-600 font-bold rounded-lg hover:bg-gray-100 transition text-center">
                            Explorer les Cours
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex items-center gap-8 mt-12 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="w-10 h-10 rounded-full bg-purple-300 border-2 border-white"></div>
                                <div class="w-10 h-10 rounded-full bg-yellow-300 border-2 border-white"></div>
                                <div class="w-10 h-10 rounded-full bg-teal-300 border-2 border-white"></div>
                            </div>
                            <span class="text-sm font-medium">10,000+ étudiants actifs</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-300 text-xl">⭐⭐⭐⭐⭐</span>
                            <span class="text-sm font-medium">4.9/5 (2,500 avis)</span>
                        </div>
                    </div>
                </div>

                <!-- Right Illustration -->
                <div class="hidden md:block floating">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-yellow-300 to-purple-600 rounded-3xl blur-2xl opacity-30 transform rotate-6"></div>
                        <div class="relative bg-white bg-opacity-10 backdrop-blur-lg rounded-3xl p-8 border border-white border-opacity-20 transform transition-transform hover:scale-105">
                            <div class="text-center mb-6">
                                <div class="text-7xl mb-4">🎓</div>
                                <h3 class="text-2xl font-bold mb-2">Votre Succès Commence Ici</h3>
                                <p class="text-white opacity-80">Accédez à des formations de qualité dès maintenant</p>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 bg-white bg-opacity-20 rounded-xl p-4">
                                    <div class="text-3xl">✓</div>
                                    <div>
                                        <span class="font-bold text-lg">500+ Cours Premium</span>
                                        <p class="text-sm opacity-80">De tous niveaux et domaines</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-white bg-opacity-20 rounded-xl p-4">
                                    <div class="text-3xl">✓</div>
                                    <div>
                                        <span class="font-bold text-lg">Certificats Reconnus</span>
                                        <p class="text-sm opacity-80">Reconnus par les employeurs</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-white bg-opacity-20 rounded-xl p-4">
                                    <div class="text-3xl">✓</div>
                                    <div>
                                        <span class="font-bold text-lg">Support 24/7</span>
                                        <p class="text-sm opacity-80">Assistance continue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gradient-to-b from-white to-gray-50">
        <div class="container-premium">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <div class="stat-number">10K+</div>
                    <div class="text-gray-700 font-bold mt-2 text-lg">Étudiants</div>
                </div>
                <div class="p-6">
                    <div class="stat-number">500+</div>
                    <div class="text-gray-700 font-bold mt-2 text-lg">Cours</div>
                </div>
                <div class="p-6">
                    <div class="stat-number">95%</div>
                    <div class="text-gray-700 font-bold mt-2 text-lg">Satisfaction</div>
                </div>
                <div class="p-6">
                    <div class="stat-number">50+</div>
                    <div class="text-gray-700 font-bold mt-2 text-lg">Instructeurs</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container-premium">
            <div class="text-center mb-16">
                <h2 class="section-title text-4xl font-bold mb-4">Pourquoi Choisir Étude Rapide?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Une plateforme conçue pour votre réussite avec des fonctionnalités premium</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="card-hover bg-gradient-to-br from-purple-50 to-white p-8 rounded-2xl shadow-lg border border-purple-100">
                    <div class="feature-icon">
                        <div class="text-4xl">🎯</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-purple-900 text-center">Apprentissage Personnalisé</h3>
                    <p class="text-gray-600 text-center">Des parcours adaptés à votre niveau et vos objectifs pour une progression optimale</p>
                </div>

                <!-- Feature 2 -->
                <div class="card-hover bg-gradient-to-br from-teal-50 to-white p-8 rounded-2xl shadow-lg border border-teal-100">
                    <div class="feature-icon">
                        <div class="text-4xl">👨‍🏫</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-teal-900 text-center">Instructeurs Experts</h3>
                    <p class="text-gray-600 text-center">Apprenez avec des professionnels reconnus dans leur domaine</p>
                </div>

                <!-- Feature 3 -->
                <div class="card-hover bg-gradient-to-br from-yellow-50 to-white p-8 rounded-2xl shadow-lg border border-yellow-100">
                    <div class="feature-icon">
                        <div class="text-4xl">🏆</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-yellow-900 text-center">Certificats Reconnus</h3>
                    <p class="text-gray-600 text-center">Valorisez votre CV avec des certifications professionnelles</p>
                </div>

                <!-- Feature 4 -->
                <div class="card-hover bg-gradient-to-br from-blue-50 to-white p-8 rounded-2xl shadow-lg border border-blue-100">
                    <div class="feature-icon">
                        <div class="text-4xl">📱</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-blue-900 text-center">Accès Multi-Appareils</h3>
                    <p class="text-gray-600 text-center">Étudiez où vous voulez, quand vous voulez, sur tous vos appareils</p>
                </div>

                <!-- Feature 5 -->
                <div class="card-hover bg-gradient-to-br from-green-50 to-white p-8 rounded-2xl shadow-lg border border-green-100">
                    <div class="feature-icon">
                        <div class="text-4xl">💬</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-green-900 text-center">Communauté Active</h3>
                    <p class="text-gray-600 text-center">Échangez avec des milliers d'étudiants motivés comme vous</p>
                </div>

                <!-- Feature 6 -->
                <div class="card-hover bg-gradient-to-br from-indigo-50 to-white p-8 rounded-2xl shadow-lg border border-indigo-100">
                    <div class="feature-icon">
                        <div class="text-4xl">⚡</div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-indigo-900 text-center">Mises à Jour Continues</h3>
                    <p class="text-gray-600 text-center">Contenu régulièrement mis à jour pour rester à la pointe</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Courses -->
    <section class="py-20 bg-gray-50">
        <div class="container-premium">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
                <div>
                    <h2 class="section-title text-4xl font-bold mb-4">Cours Populaires</h2>
                    <p class="text-xl text-gray-600">Découvrez nos formations les plus demandées</p>
                </div>
                <a href="/cursos" class="btn-premium inline-flex items-center gap-2">
                    Voir tous les cours
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($courses->take(3) as $course)
                <div class="card-hover bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="h-56 bg-gradient-to-br from-purple-400 to-yellow-400 relative overflow-hidden">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover">
                        @endif
                        @if($course->price == 0)
                            <span class="absolute top-4 left-4 bg-green-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                                Gratuit
                            </span>
                        @endif
                        <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white text-sm font-bold px-3 py-1 rounded-full">
                            {{ $course->level }}
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3 hover:text-purple-600 transition">{{ $course->title }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>⭐ {{ $course->rating }}</span>
                                <span>•</span>
                                <span>{{ $course->students_count }} étudiants</span>
                            </div>
                            <div class="text-xl font-bold text-purple-600">
                                @if($course->price > 0)
                                    {{ number_format($course->price, 0) }} HTG
                                @else
                                    <span class="text-green-600">Gratuit</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="mt-4 block text-center btn-premium">
                            Voir le cours
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gradient-to-r from-purple-900 to-teal-900 text-white">
        <div class="container-premium">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Ce que disent nos étudiants</h2>
                <p class="text-xl text-purple-200 max-w-2xl mx-auto">Rejoignez des milliers de personnes qui ont transformé leur vie avec Étude Rapide</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-2xl">
                    <div class="text-yellow-300 text-4xl mb-4">"</div>
                    <p class="text-lg mb-6">Les cours sont clairs et structurés. J'ai pu apprendre à mon rythme et obtenir des résultats concrets.</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-purple-300"></div>
                        <div>
                            <div class="font-bold">Marie Jean</div>
                            <div class="text-purple-200">Marketing Digital</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-2xl">
                    <div class="text-yellow-300 text-4xl mb-4">"</div>
                    <p class="text-lg mb-6">Grâce aux certifications obtenues, j'ai pu décrocher un emploi dans mon domaine d'expertise.</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-teal-300"></div>
                        <div>
                            <div class="font-bold">Pierre Williams</div>
                            <div class="text-purple-200">Développement Web</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-2xl">
                    <div class="text-yellow-300 text-4xl mb-4">"</div>
                    <p class="text-lg mb-6">L'interface est intuitive et les instructeurs sont très compétents. Je recommande vivement!</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-yellow-300"></div>
                        <div>
                            <div class="font-bold">Sophie Pierre</div>
                            <div class="text-purple-200">Design Graphique</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 hero-gradient text-white">
        <div class="container-premium text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">Prêt à Transformer Votre Avenir?</h2>
            <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-2xl mx-auto">
                Rejoignez des milliers d'étudiants qui ont déjà commencé leur parcours vers le succès
            </p>
            <a href="{{ route('register') }}" class="btn-premium text-lg px-8 py-4 inline-flex items-center gap-2 mx-auto">
                <span>Commencer Maintenant - C'est Gratuit</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="container-premium">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div>
                    <h3 class="text-2xl font-bold mb-4 logo-text text-gradient-royal">Étude Rapide</h3>
                    <p class="text-gray-400 mb-6">Plateforme d'apprentissage premium pour la francophonie</p>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Cours</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="/cursos" class="hover:text-white transition">Tous les cours</a></li>
                        <li><a href="#" class="hover:text-white transition">Catégories</a></li>
                        <li><a href="#" class="hover:text-white transition">Instructeurs</a></li>
                        <li><a href="#" class="hover:text-white transition">Certifications</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Support</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition">Communauté</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Légal</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Conditions</a></li>
                        <li><a href="#" class="hover:text-white transition">Confidentialité</a></li>
                        <li><a href="#" class="hover:text-white transition">Cookies</a></li>
                        <li><a href="#" class="hover:text-white transition">Droits d'auteur</a></li>
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

        // Add animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fadeInUp');
                    }
                });
            }, observerOptions);

            // Observe elements to animate
            document.querySelectorAll('.card-premium, .stat-number').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>

</html>
