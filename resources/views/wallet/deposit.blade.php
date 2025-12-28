<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Saldo - Makis EAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Adicionar Saldo à Carteira</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Escolha o valor e método de pagamento</p>
                </div>
                
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <!-- Current Balance -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-800">Saldo Atual</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</p>
                    </div>

                    <!-- Deposit Form -->
                    <form action="{{ route('wallet.deposit.process') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Amount Input -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Valor do Depósito</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">{{ $wallet->currency }}</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" 
                                           class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-16 pr-12 sm:text-sm border-gray-300 rounded-md" 
                                           placeholder="0.00" 
                                           min="10" 
                                           max="100000" 
                                           step="0.01" 
                                           required>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Valor mínimo: {{ $wallet->currency }} 10.00</p>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Valores Rápidos</label>
                                <div class="grid grid-cols-4 gap-3">
                                    <button type="button" onclick="document.getElementById('amount').value = 50" 
                                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                        50
                                    </button>
                                    <button type="button" onclick="document.getElementById('amount').value = 100" 
                                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                        100
                                    </button>
                                    <button type="button" onclick="document.getElementById('amount').value = 200" 
                                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                        200
                                    </button>
                                    <button type="button" onclick="document.getElementById('amount').value = 500" 
                                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                                        500
                                    </button>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Método de Pagamento</label>
                                <div class="space-y-3">
                                    <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 border-purple-500 bg-purple-50">
                                        <input type="radio" name="gateway" value="moncash" class="h-4 w-4 text-purple-600" checked required>
                                        <div class="ml-4">
                                            <span class="block text-sm font-medium text-gray-900">MonCash</span>
                                            <span class="block text-sm text-gray-500">Pagamento via MonCash</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @if($errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                    <ul class="list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <!-- Submit Buttons -->
                            <div class="flex justify-between pt-4">
                                <a href="{{ route('wallet.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Cancelar
                                </a>
                                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-md hover:opacity-90 font-medium">
                                    Prosseguir para Pagamento
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
