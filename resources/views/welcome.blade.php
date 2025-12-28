<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étude Rapide - Plateforme d'Apprentissage Premium</title>
    <meta name="description"
        content="Transformez votre avenir avec des cours en ligne de qualité premium. Rejoignez 10,000+ étudiants qui réussissent avec Étude Rapide.">
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/luxury-premium.css') }}">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #6B21A8 0%, #9333EA 50%, #0D9488 100%);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .fade-in {
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #6B21A8, #F59E0B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50"> <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container-premium py-4">
            <div class="flex justify-between items-center"> <!-- Logo --> <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/brand/logo.png') }}" alt="Étude Rapide" class="h-16"> </a>
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8"> <a href="/cursos"
                        class="text-gray-700 hover:text-purple-600 font-medium transition">Cours</a> <a href="#features"
                        class="text-gray-700 hover:text-purple-600 font-medium transition">Fonctionnalités</a> <a
                        href="/pricing" class="text-gray-700 hover:text-purple-600 font-medium transition">Tarifs</a> <a
                        href="/contact" class="text-gray-700 hover:text-purple-600 font-medium transition">Contact</a>
                </div> <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-4"> @auth <a href="{{ route('student.dashboard') }}"
                        class="text-purple-600 font-semibold hover:text-purple-800">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline"> @csrf <button type="submit"
                            class="text-gray-700 font-medium hover:text-red-600 transition ml-2"> Déconnexion </button>
                </form> @else <a href="{{ route('login') }}"
                        class="text-gray-700 font-medium hover:text-purple-600">Connexion</a> <a
                    href="{{ route('register') }}" class="btn-premium"> Commencer Gratuitement </a> @endauth
                </div> <!-- Mobile Menu Button --> <button class="md:hidden" onclick="toggleMobileMenu()"> <svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg> </button>
            </div> <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4">
                <div class="flex flex-col gap-4"> <a href="/cursos" class="text-gray-700 font-medium">Cours</a> <a
                        href="#features" class="text-gray-700 font-medium">Fonctionnalités</a> <a href="/pricing"
                        class="text-gray-700 font-medium">Tarifs</a> @auth <a href="{{ route('student.dashboard') }}"
                                class="text-purple-600 font-medium">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}"> @csrf <button type="submit"
                        class="text-red-600 font-medium w-full text-left"> Déconnexion </button> </form> @endauth
                    @guest <a href="{{ route('login') }}" class="text-gray-700 font-medium">Connexion</a> <a
                    href="{{ route('register') }}" class="btn-premium justify-center">Commencer</a> @endguest
                </div>
            </div>
        </nav>
    </header> <!-- Hero Section -->
    <section class="hero-gradient text-white py-20 md:py-32 relative overflow-hidden"> <!-- Decorative Elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-yellow-300 rounded-full blur-3xl"></div>
        </div>
        <div class="container-premium relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center"> <!-- Left Content -->
                <div class="fade-in">
                    <div
                        class="inline-flex items-center gap-2 bg-white bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                        <span class="text-yellow-300">⚡</span> <span class="text-sm font-semibold">Plateforme #1 en
                            Haïti</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 logo-text leading-tight"> Apprenez Rapidement,<br />
                        <span class="text-yellow-300">Réussissez Brillamment</span>
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 opacity-90 leading-relaxed"> Transformez votre avenir avec des
                        cours en ligne de qualité premium. Rejoignez 10,000+ étudiants qui réussissent. </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-12"> <a href="{{ route('register') }}"
                            class="btn-premium text-lg px-8 py-4 justify-center"> <span>Commencer Gratuitement</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg> </a> <a href="/cursos"
                            class="px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-purple-600 transition text-center">
                            Explorer les Cours </a> </div> <!-- Trust Indicators -->
                    <div class="flex items-center gap-6 flex-wrap">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div class="w-10 h-10 rounded-full bg-purple-300 border-2 border-white"></div>
                                <div class="w-10 h-10 rounded-full bg-yellow-300 border-2 border-white"></div>
                                <div class="w-10 h-10 rounded-full bg-teal-300 border-2 border-white"></div>
                            </div> <span class="text-sm font-medium">10,000+ étudiants actifs</span>
                        </div>
                        <div class="flex items-center gap-1"> <span class="text-yellow-300">⭐⭐⭐⭐⭐</span> <span
                                class="text-sm font-medium ml-2">4.9/5 (2,500 avis)</span> </div>
                    </div>
                </div> <!-- Right Illustration -->
                <div class="hidden md:block floating">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-yellow-300 to-purple-600 rounded-3xl blur-2xl opacity-30">
                        </div>
                        <div
                            class="relative bg-white bg-opacity-10 backdrop-blur-lg rounded-3xl p-8 border border-white border-opacity-20">
                            <div class="text-center mb-6">
                                <div class="text-6xl mb-4">🎓</div>
                                <h3 class="text-2xl font-bold mb-2">Votre Succès Commence Ici</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 bg-white bg-opacity-20 rounded-lg p-3">
                                    <div class="text-2xl">✓</div> <span class="font-medium">500+ Cours Premium</span>
                                </div>
                                <div class="flex items-center gap-3 bg-white bg-opacity-20 rounded-lg p-3">
                                    <div class="text-2xl">✓</div> <span class="font-medium">Certificats Reconnus</span>
                                </div>
                                <div class="flex items-center gap-3 bg-white bg-opacity-20 rounded-lg p-3">
                                    <div class="text-2xl">✓</div> <span class="font-medium">Support 24/7</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container-premium">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="stat-number">10K+</div>
                    <div class="text-gray-600 font-medium mt-2">Étudiants</div>
                </div>
                <div>
                    <div class="stat-number">500+</div>
                    <div class="text-gray-600 font-medium mt-2">Cours</div>
                </div>
                <div>
                    <div class="stat-number">95%</div>
                    <div class="text-gray-600 font-medium mt-2">Satisfaction</div>
                </div>
                <div>
                    <div class="stat-number">50+</div>
                    <div class="text-gray-600 font-medium mt-2">Instructeurs</div>
                </div>
            </div>
        </div>
    </section> <!-- Features Section -->
    <section id="features" class="py-20 luxury-pattern">
        <div class="container-premium">
            <div class="text-center mb-16">
                <h2 class="section-title">Pourquoi Choisir Étude Rapide?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto"> Une plateforme conçue pour votre réussite avec des
                    fonctionnalités premium </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8"> <!-- Feature 1 -->
                <div class="card-premium text-center">
                    <div class="text-5xl mb-4">🎯</div>
                    <h3 class="text-2xl font-bold mb-3 text-purple-900">Apprentissage Personnalisé</h3>
                    <p class="text-gray-600"> Des parcours adaptés à votre niveau et vos objectifs pour une progression
                        optimale </p>
                </div> <!-- Feature 2 -->
                <div class="card-premium text-center">
                    <div class="text-5xl mb-4">👨‍🏫</div>
                    <h3 class="text-2xl font-bold mb-3 text-purple-900">Instructeurs Experts</h3>
                    <p class="text-gray-600"> Apprenez avec des professionnels reconnus dans leur domaine </p>
                </div> <!-- Feature 3 -->
                <div class="card-premium text-center">
                    <div class="text-5xl mb-4">🏆</div>
                    <h3 class="text-2xl font-bold mb-3 text-purple-900">Certificats Reconnus</h3>
                    <p class="text-gray-600"> Valorisez votre CV avec des certifications professionnelles </p>
                </div> <!-- Feature 4 -->
                <div class="card-premium text-center">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-2xl font-bold mb-3 text-purple-900">Accès Multi-Appareils</h3>
                    <p class="text-gray-600"> Étudiez où vous voulez, quand vous voulez, sur tous vos appareils </p>
                </div> <!-- Feature 5 -->
                <div class="card-premium text-center">
                    <div class="text-5xl mb-4">💬</div>
                    <h3 class="text-2xl font-bold mb-3 text-purple-900">Communauté Active</h3>
                    <p class="text-gray-600"> Échangez avec des milliers d'étudiants motivés comme vous </p>
                </div> <!-- Feature 6 -->
                <div class="card-premium text-center">
                    <div class="text-5xl mb-4">⚡</div>
                    <h3 class="text-2xl font-bold mb-3 text-purple-900">Mises à Jour Continues</h3>
                    <p class="text-gray-600"> Contenu régulièrement mis à jour pour rester à la pointe </p>
                </div>
            </div>
        </div>
    </section> <!-- Popular Courses -->
    <section class="py-20 bg-white">
        <div class="container-premium">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="section-title">Cours Populaires</h2>
                    <p class="text-xl text-gray-600">Découvrez nos formations les plus demandées</p>
                </div> <a href="/cursos"
                    class="hidden md:block text-purple-600 font-semibold hover:text-purple-800 flex items-center gap-2">
                    Voir tous les cours <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg> </a>
            </div>
            <div class="grid md:grid-cols-3 gap-8"> @foreach($courses->take(3) as $course) <div
                class="card-premium group cursor-pointer">
                <div
                    class="h-48 bg-gradient-to-br from-purple-400 to-yellow-400 rounded-xl mb-4 relative overflow-hidden">
                    @if($course->image) <img src="{{ asset($course->image) }}" alt="{{ $course->title }}"
                    class="w-full h-full object-cover"> @endif @if($course->price == 0) <span
                        class="absolute top-4 right-4 badge-premium">Gratuit</span> @endif
                </div>
                <h3 class="text-xl font-bold mb-2 group-hover:text-purple-600 transition">{{ $course->title }}</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">{{ $course->description }}</p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-500"> <span>⭐ {{ $course->rating }}</span>
                        <span>•</span> <span>{{ $course->students_count }} étudiants</span>
                    </div>
                    <div class="text-2xl font-bold text-purple-600"> @if($course->price > 0)
                    {{ number_format($course->price, 0) }} HTG @else <span class="text-green-600">Gratuit</span>
                        @endif
                    </div>
                </div>
            </div> @endforeach </div>
            <div class="text-center mt-12 md:hidden"> <a href="/cursos" class="btn-premium inline-flex">Voir tous les
                    cours</a> </div>
        </div>
    </section> <!-- CTA Section -->
    <section class="py-20 hero-gradient text-white">
        <div class="container-premium text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-6"> Prêt à Transformer Votre Avenir? </h2>
            <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-2xl mx-auto"> Rejoignez des milliers d'étudiants qui ont
                déjà commencé leur parcours vers le succès </p> <a href="{{ route('register') }}"
                class="btn-premium text-lg px-8 py-4 inline-flex"> <span>Commencer Maintenant - C'est Gratuit</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg> </a>
        </div>
    </section> <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container-premium">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 logo-text text-gradient-royal">Étude Rapide</h3>
                    <p class="text-gray-400">Plateforme d'apprentissage premium pour la francophonie</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Cours</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/cursos" class="hover:text-white">Tous les cours</a></li>
                        <li><a href="#" class="hover:text-white">Catégories</a></li>
                        <li><a href="#" class="hover:text-white">Instructeurs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Centre d'aide</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
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
    <script> function toggleMobileMenu() { const menu = document.getElementById('mobileMenu'); menu.classList.toggle('hidden'); } </script>
</body>

</html>@include("components.chat-widget")