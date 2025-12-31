<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Pendente - Makis EAD</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pagamento Pendente</h2>
                <p class="text-gray-600 mb-6">Seu pagamento está sendo processado. Você receberá um e-mail assim que for confirmado.</p>
                <div class="space-y-3">
                    <a href="{{ route('student.dashboard') }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                        Ir para Dashboard
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
