<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement Annulé - Étude Rapide</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Icon -->
            <div class="mb-8">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl font-bold mb-4 logo-text text-gray-900">
                Abonnement Non Complété
            </h1>

            <!-- Message -->
            <p class="text-xl text-gray-700 mb-8">
                Vous avez annulé le processus d'abonnement. Aucun frais n'a été appliqué.
            </p>

            <!-- Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                <p class="text-blue-800">
                    💡 Besoin d'aide pour choisir le bon plan? Notre équipe est là pour vous aider!
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('pricing') }}" class="btn-premium justify-center">
                    <span>Voir les Plans</span>
                </a>
                <a href="{{ route('contact.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Contactez-nous
                </a>
            </div>
        </div>
    </div>
</body>
</html>
