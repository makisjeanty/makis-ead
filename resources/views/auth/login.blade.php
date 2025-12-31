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
        <div class="hidden lg:flex lg:w-1/2 illustration-bg relative overflow-hidden items-center justify-center">
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
                
                <div class="grid grid-cols-3 gap-6 mt-12 w-full max-w-lg">
                    <div class="text-center p-4 bg-white/10 rounded-xl backdrop-blur-sm">
                        <div class="text-4xl font-bold mb-2">10K+</div>
                        <div class="text-sm opacity-80">Étudiants</div>
                    </div>
                    <div class="text-center p-4 bg-white/10 rounded-xl backdrop-blur-sm">
                        <div class="text-4xl font-bold mb-2">500+</div>
                        <div class="text-sm opacity-80">Cours</div>
                    </div>
                    <div class="text-center p-4 bg-white/10 rounded-xl backdrop-blur-sm">
                        <div class="text-4xl font-bold mb-2">95%</div>
                        <div class="text-sm opacity-80">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
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
                            <span class="text-3xl">👋</span>
                            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm font-semibold">Content de vous revoir</span>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Connexion</h2>
                        <p class="text-gray-600">Accédez à votre espace d'apprentissage premium</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
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
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="input-premium w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                       placeholder="votre@email.com">
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

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm font-medium text-purple-600 hover:text-purple-500" href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-teal-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            Se connecter
                        </button>

                        <!-- Register Link -->
                        <div class="text-center mt-6">
                            <p class="text-sm text-gray-600">
                                Pas encore de compte ?
                                <a href="{{ route('register') }}" class="font-bold text-purple-600 hover:text-purple-500">
                                    Créer un compte gratuitement
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
