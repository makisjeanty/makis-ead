<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Abonnement - Étude Rapide</title>
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
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="logo-text text-3xl font-bold">
                    <span style="color: #6B21A8;">Étude</span> <span style="color: #F59E0B;">Rapide</span>
                </a>
                <nav class="flex items-center gap-6">
                    <a href="{{ route('student.dashboard') }}" class="text-gray-700 hover:text-purple-700">Tableau de bord</a>
                    <a href="{{ route('courses.index') }}" class="text-gray-700 hover:text-purple-700">Cours</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Page Title -->
        <h1 class="text-4xl font-bold mb-8 logo-text" style="color: #6B21A8;">Mon Abonnement</h1>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Subscription Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Current Plan -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Plan {{ $subscription->plan_name }}</h2>
                            <p class="text-gray-600">{{ $subscription->getFormattedPrice() }}/mois</p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            {{ $subscription->isActive() ? 'bg-green-100 text-green-700' : '' }}
                            {{ $subscription->isPastDue() ? 'bg-red-100 text-red-700' : '' }}
                            {{ $subscription->isCanceled() ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>

                    <!-- Subscription Details -->
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Début de la période</p>
                            <p class="font-semibold">{{ $subscription->current_period_start->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Fin de la période</p>
                            <p class="font-semibold">{{ $subscription->current_period_end->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Prochain paiement</p>
                            <p class="font-semibold">
                                @if($subscription->cancel_at_period_end)
                                    <span class="text-red-600">Annulé</span>
                                @else
                                    {{ $subscription->current_period_end->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Jours restants</p>
                            <p class="font-semibold">{{ $subscription->daysRemaining() }} jours</p>
                        </div>
                    </div>

                    @if($subscription->cancel_at_period_end)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-yellow-800">
                                ⚠️ Votre abonnement sera annulé le {{ $subscription->current_period_end->format('d/m/Y') }}. 
                                Vous conservez l'accès jusqu'à cette date.
                            </p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('subscription.portal') }}" class="btn-premium">
                            <span>Gérer l'Abonnement</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </a>

                        @if($subscription->cancel_at_period_end)
                            <form action="{{ route('subscription.resume') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                    Reprendre l'Abonnement
                                </button>
                            </form>
                        @elseif($subscription->isActive())
                            <button onclick="confirmCancel()" class="px-6 py-3 border-2 border-red-600 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors">
                                Annuler l'Abonnement
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Features -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold mb-6">Fonctionnalités Incluses</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($subscription->getFeatures() as $feature)
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="font-bold text-lg mb-4">Statistiques</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Cours accessibles</p>
                            <p class="text-2xl font-bold" style="color: #6B21A8;">
                                @if($subscription->plan_name === 'enterprise')
                                    Illimité
                                @elseif($subscription->plan_name === 'professional')
                                    200+
                                @else
                                    50+
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Membre depuis</p>
                            <p class="text-lg font-semibold">{{ $subscription->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Upgrade CTA -->
                @if($subscription->plan_name !== 'enterprise' && $subscription->isActive())
                    <div class="gradient-bg text-white rounded-2xl shadow-xl p-6">
                        <h3 class="font-bold text-lg mb-2">Passez au niveau supérieur!</h3>
                        <p class="text-sm opacity-90 mb-4">
                            Débloquez plus de fonctionnalités avec un plan supérieur.
                        </p>
                        <a href="{{ route('pricing') }}" class="block w-full text-center bg-white text-purple-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            Voir les Plans
                        </a>
                    </div>
                @endif

                <!-- Help -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="font-bold text-lg mb-2">Besoin d'aide?</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Notre équipe de support est là pour vous aider.
                    </p>
                    <a href="{{ route('contact.index') }}" class="text-purple-700 font-semibold hover:underline">
                        Contactez-nous →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full">
            <h3 class="text-2xl font-bold mb-4">Annuler l'abonnement?</h3>
            <p class="text-gray-600 mb-6">
                Vous conserverez l'accès jusqu'à la fin de votre période de facturation actuelle ({{ $subscription->current_period_end->format('d/m/Y') }}).
            </p>
            <div class="flex gap-4">
                <form action="{{ route('subscription.cancel-subscription') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                        Confirmer l'Annulation
                    </button>
                </form>
                <button onclick="closeModal()" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Garder l'Abonnement
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmCancel() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }
    </script>
</body>
</html>
