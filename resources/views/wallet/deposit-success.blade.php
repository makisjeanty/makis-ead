<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depósito Confirmado - Makis EAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Depósito Confirmado!</h2>
                <p class="text-gray-600 mb-6">Seu saldo foi atualizado com sucesso.</p>
                <div class="space-y-3">
                    <a href="{{ route('wallet.index') }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 font-medium">
                        Ver Carteira
                    </a>
                    <a href="{{ route('courses.index') }}" class="block w-full px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Explorar Cursos
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
