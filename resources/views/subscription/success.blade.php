<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement Réussi - Étude Rapide</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
        }
        .success-animation {
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Success Icon -->
            <div class="success-animation mb-8">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl font-bold mb-4 logo-text" style="color: #6B21A8;">
                Bienvenue dans la Famille!
            </h1>

            <!-- Message -->
            <p class="text-xl text-gray-700 mb-8">
                Votre abonnement a été activé avec succès. Vous avez maintenant accès à tous les avantages de votre plan premium.
            </p>

            <!-- Features -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <h3 class="font-bold text-lg mb-4">Ce qui vous attend:</h3>
                <ul class="space-y-3 text-left">
                    <li class="flex items-center gap-3">
                        <span class="text-2xl">🎓</span>
                        <span>Accès immédiat à tous vos cours</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-2xl">📧</span>
                        <span>Email de confirmation envoyé</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-2xl">💳</span>
                        <span>Gestion facile de votre abonnement</span>
                    </li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('courses.index') }}" class="btn-premium justify-center">
                    <span>Explorer les Cours</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="{{ route('subscription.dashboard') }}" class="px-6 py-3 border-2 border-purple-700 text-purple-700 font-semibold rounded-lg hover:bg-purple-50 transition-colors">
                    Mon Abonnement
                </a>
            </div>
        </div>
    </div>
</body>
</html>
