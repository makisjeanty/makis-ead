<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarifs - Étude Rapide</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/luxury-premium.css') }}">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #6B21A8 0%, #9333EA 50%, #F59E0B 100%);
        }
        .pricing-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(107, 33, 168, 0.3);
        }
        .popular-badge {
            background: linear-gradient(135deg, #F59E0B 0%, #FCD34D 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="logo-text text-3xl font-bold">
                    <span style="color: #6B21A8;">Étude</span> <span style="color: #F59E0B;">Rapide</span>
                </a>
                <nav class="hidden md:flex items-center gap-8">
                    <a href="/" class="text-gray-700 hover:text-purple-700 font-medium transition-colors">Accueil</a>
                    <a href="{{ route('courses.index') }}" class="text-gray-700 hover:text-purple-700 font-medium transition-colors">Cours</a>
                    <a href="{{ route('pricing') }}" class="text-purple-700 font-semibold">Tarifs</a>
                    <a href="{{ route('contact.index') }}" class="text-gray-700 hover:text-purple-700 font-medium transition-colors">Contact</a>
                    @auth
                        <a href="{{ route('student.dashboard') }}" class="btn-premium">Mon Espace</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-premium">Connexion</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 logo-text">Choisissez Votre Plan</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                Investissez dans votre avenir avec nos abonnements premium
            </p>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-20 -mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg max-w-2xl mx-auto">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg max-w-2xl mx-auto">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($plans as $key => $plan)
                    <div class="pricing-card bg-white rounded-2xl shadow-xl overflow-hidden {{ $plan['popular'] ?? false ? 'ring-4 ring-amber-400' : '' }}">
                        @if($plan['popular'] ?? false)
                            <div class="popular-badge text-white text-center py-2 font-bold text-sm">
                                ⭐ PLUS POPULAIRE
                            </div>
                        @endif

                        <div class="p-8">
                            <!-- Plan Name -->
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h3>
                            
                            <!-- Price -->
                            <div class="mb-6">
                                <span class="text-5xl font-bold" style="color: #6B21A8;">${{ number_format($plan['price'], 0) }}</span>
                                <span class="text-gray-600">/mois</span>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-4 mb-8">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-start gap-3">
                                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- CTA Button -->
                            @auth
                                @if(auth()->user()->subscribedTo($key))
                                    <button disabled class="w-full px-6 py-4 bg-gray-300 text-gray-600 font-bold rounded-lg cursor-not-allowed">
                                        Plan Actuel
                                    </button>
                                @elseif(auth()->user()->hasActiveSubscription())
                                    <form action="{{ route('subscription.checkout') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $key }}">
                                        <button type="submit" class="w-full btn-premium justify-center">
                                            <span>Changer de Plan</span>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('subscription.checkout') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan" value="{{ $key }}">
                                        <button type="submit" class="w-full btn-premium justify-center">
                                            <span>Commencer Maintenant</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="block w-full text-center btn-premium">
                                    <span>Commencer Maintenant</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-12 logo-text" style="color: #6B21A8;">Questions Fréquentes</h2>
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">💳 Puis-je annuler à tout moment?</h3>
                    <p class="text-gray-600">Oui, vous pouvez annuler votre abonnement à tout moment depuis votre tableau de bord.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">🔄 Puis-je changer de plan?</h3>
                    <p class="text-gray-600">Absolument! Vous pouvez passer à un plan supérieur ou inférieur à tout moment.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">💰 Y a-t-il des frais cachés?</h3>
                    <p class="text-gray-600">Non, le prix affiché est le prix final. Aucun frais supplémentaire.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">🎓 Les certificats sont-ils inclus?</h3>
                    <p class="text-gray-600">Oui, tous les plans incluent des certificats de complétion pour vos cours.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-bg text-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6 logo-text">Prêt à Transformer Votre Avenir?</h2>
            <p class="text-xl mb-8 opacity-90">
                Rejoignez des milliers d'étudiants qui ont déjà commencé leur parcours d'excellence
            </p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-purple-700 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors">
                    <span>Créer un Compte Gratuit</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="logo-text text-3xl font-bold mb-4">
                <span style="color: #9333EA;">Étude</span> <span style="color: #F59E0B;">Rapide</span>
            </div>
            <p class="text-gray-400 mb-6">Transformez votre avenir avec une éducation de qualité premium</p>
            <p class="text-sm text-gray-500">© 2024 Étude Rapide. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
