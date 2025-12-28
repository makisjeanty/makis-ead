<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Étude Rapide</title>
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
            background: linear-gradient(135deg, #6B21A8 0%, #9333EA 50%, #0D9488 100%);
        }
        .form-container {
            min-height: 100vh;
        }
        .input-premium {
            transition: all 0.3s ease;
        }
        .input-premium:focus {
            border-color: #6B21A8;
            box-shadow: 0 0 0 3px rgba(107, 33, 168, 0.1);
        }
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Left Side - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 illustration-bg relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-yellow-300 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <!-- Decorative Elements -->
                <div class="floating-element mb-8">
                    <svg class="w-32 h-32 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                
                <h1 class="text-5xl font-bold mb-6 text-center logo-text">
                    Bienvenue sur<br/>
                    <span class="text-6xl">Étude Rapide</span>
                </h1>
                
                <p class="text-xl text-center mb-8 max-w-md opacity-90">
                    Transformez votre avenir avec une éducation de qualité premium
                </p>
                
                <div class="grid grid-cols-3 gap-6 mt-12">
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">10K+</div>
                        <div class="text-sm opacity-80">Étudiants</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">500+</div>
                        <div class="text-sm opacity-80">Cours</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold mb-2">95%</div>
                        <div class="text-sm opacity-80">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
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
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Connexion</h2>
                        <p class="text-gray-600">Accédez à votre espace d'apprentissage</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

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

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Adresse Email
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="input-premium w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Mot de passe
                            </label>
                            <input id="password" type="password" name="password" required
                                   class="input-premium w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none">
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-purple-600 hover:text-purple-800">
                                    Mot de passe oublié?
                                </a>
                            @endif
                        </div>

                        <!-- Back to Home Button -->
                        <a href="/" class="inline-flex items-center text-base font-medium text-gray-600 hover:text-purple-700 transition-colors mb-6 px-4 py-2 rounded-lg hover:bg-purple-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour à l'accueil
                        </a>
                        <!-- Submit Button -->
                        <button type="submit" class="btn-premium w-full justify-center">
                            <span>Se connecter</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Nouveau sur Étude Rapide?</span>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <a href="{{ route('register') }}" class="block w-full text-center px-6 py-3 border-2 border-purple-600 text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition-colors">
                        Créer un compte
                    </a>
                </div>

                <!-- Footer Links -->
                <div class="mt-8 text-center text-sm text-gray-600">
                    <a href="/" class="hover:text-purple-600 mx-2">Accueil</a>
                    <span>•</span>
                    <a href="/cursos" class="hover:text-purple-600 mx-2">Cours</a>
                    <span>•</span>
                    <a href="#" class="hover:text-purple-600 mx-2">Aide</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
