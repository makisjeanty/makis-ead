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
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 form-container bg-white">
            <div class="w-full max-w-md">
                <!-- Logo/Brand -->
                <div class="mb-8 text-center lg:text-left">
                    <a href="/" class="logo-text text-4xl font-bold">
                        <span class="etude-text">Étude</span> <span class="rapide-text">Rapide</span>
                    </a>
                </div>

                <!-- Form Card -->
                <div class="bg-white p-2">
                    <div class="mb-8">
                        <div class="inline-flex items-center gap-2 mb-4">
                            <span class="text-3xl">🎓</span>
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold">Nouveau Compte</span>
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
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                       class="input-premium w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                       placeholder="Makis Jeanty">
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Adresse Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                       class="input-premium w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                       placeholder="makisjeanty@gmail.com">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Mot de passe
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input id="password" type="password" name="password" required
                                       class="input-premium w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirmer le mot de passe
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </span>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                       class="input-premium w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            Créer mon compte
                        </button>

                        <!-- Login Link -->
                        <div class="text-center mt-6">
                            <p class="text-sm text-gray-600">
                                Déjà inscrit ?
                                <a href="{{ route('login') }}" class="font-bold text-yellow-600 hover:text-yellow-500">
                                    Se connecter
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 illustration-bg relative overflow-hidden items-center justify-center">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 right-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 left-20 w-96 h-96 bg-purple-800 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white text-center">
                <!-- Achievement Badge Animation -->
                <div class="achievement-badge mb-8 bg-white/20 p-6 rounded-full backdrop-blur-md">
                    <span class="text-6xl">🚀</span>
                </div>
                
                <h2 class="text-4xl font-bold mb-6">
                    Rejoignez l'élite
                </h2>
                
                <p class="text-xl mb-12 max-w-md opacity-90">
                    Débloquez votre potentiel avec nos cours certifiants et notre communauté active.
                </p>
                
                <div class="space-y-4 text-left w-full max-w-md">
                    <div class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-full bg-green-400 flex items-center justify-center text-white font-bold">✓</div>
                        <div>
                            <div class="font-bold">Accès illimité</div>
                            <div class="text-sm opacity-80">À tous les cours gratuits</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-full bg-blue-400 flex items-center justify-center text-white font-bold">✓</div>
                        <div>
                            <div class="font-bold">Certificats</div>
                            <div class="text-sm opacity-80">Reconnus internationalement</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 bg-white/10 p-4 rounded-xl backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-full bg-purple-400 flex items-center justify-center text-white font-bold">✓</div>
                        <div>
                            <div class="font-bold">Support Premium</div>
                            <div class="text-sm opacity-80">Assistance prioritaire</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
