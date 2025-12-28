<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Étude Rapide</title>
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
        .etude-text {
            color: #6B21A8;
        }
        .rapide-text {
            color: #F59E0B;
        }
        .illustration-bg {
            background: linear-gradient(135deg, #F59E0B 0%, #FCD34D 50%, #6B21A8 100%);
        }
        .form-container {
            min-height: 100vh;
        }
        .input-premium {
            transition: all 0.3s ease;
        }
        .input-premium:focus {
            border-color: #F59E0B;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        .achievement-badge {
            animation: bounce 2s ease-in-out infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Left Side - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 form-container">
            <div class="w-full max-w-md">
                <!-- Logo/Brand -->
            <div class="mb-8">
                <a href="/" class="logo-text text-4xl font-bold">
                    <span class="etude-text">Étude</span> <span class="rapide-text">Rapide</span>
                </a>
            </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10">
                    <div class="mb-8">
                        <div class="inline-flex items-center gap-2 mb-4">
                            <span class="text-3xl">🎓</span>
                            <span class="badge-premium">Nouveau</span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Créer un compte</h2>
                        <p class="text-gray-600">Commencez votre parcours d'excellence</p>
                    </div>

                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nom complet
                            </label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="input-premium w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="Makis Jeanty">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Adresse Email
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="input-premium w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="makisjeanty@gmail.com">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Mot de passe
                            </label>
                            <input id="password" type="password" name="password" required
                                   class="input-premium w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="Minimum 8 caractères">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirmer le mot de passe
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="input-premium w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none">
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start">
                            <input type="checkbox" name="terms" required class="w-4 h-4 mt-1 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <label class="ml-2 text-sm text-gray-600">
                                J'accepte les <a href="#" class="text-purple-600 hover:underline">conditions d'utilisation</a> et la <a href="#" class="text-purple-600 hover:underline">politique de confidentialité</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-premium w-full justify-center mt-6">
                            <span>Créer mon compte</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </form>

                    <!-- Back to Home Button -->
            <a href="/" class="inline-flex items-center text-base font-medium text-gray-600 hover:text-amber-600 transition-colors mb-6 px-4 py-2 rounded-lg hover:bg-amber-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à l'accueil
            </a>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Déjà inscrit?</span>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                        Se connecter
                    </a>
                </div>

                <!-- Benefits -->
                <div class="mt-8 grid grid-cols-3 gap-4 text-center text-xs text-gray-600">
                    <div>
                        <div class="text-2xl mb-1">✓</div>
                        <div>Accès illimité</div>
                    </div>
                    <div>
                        <div class="text-2xl mb-1">🏆</div>
                        <div>Certificats</div>
                    </div>
                    <div>
                        <div class="text-2xl mb-1">💬</div>
                        <div>Support 24/7</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 illustration-bg relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 right-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 left-20 w-96 h-96 bg-purple-900 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <!-- Achievement Badges -->
                <div class="achievement-badge mb-8">
                    <div class="text-8xl mb-4">🎯</div>
                </div>
                
                <h2 class="text-5xl font-bold mb-6 text-center logo-text">
                    Rejoignez<br/>
                    <span class="text-6xl">10,000+ Étudiants</span>
                </h2>
                
                <p class="text-xl text-center mb-12 max-w-md opacity-90">
                    Transformez vos ambitions en réalité avec nos cours premium
                </p>
                
                <!-- Features -->
                <div class="space-y-6 max-w-md">
                    <div class="flex items-center gap-4 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-3xl">📚</div>
                        <div>
                            <div class="font-semibold">500+ Cours Premium</div>
                            <div class="text-sm opacity-80">Dans tous les domaines</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-3xl">👨‍🏫</div>
                        <div>
                            <div class="font-semibold">Instructeurs Experts</div>
                            <div class="text-sm opacity-80">Professionnels certifiés</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-3xl">🏅</div>
                        <div>
                            <div class="font-semibold">Certificats Reconnus</div>
                            <div class="text-sm opacity-80">Valorisez votre CV</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
