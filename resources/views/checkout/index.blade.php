<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Makis EAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Finalizar Compra</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Escolha a forma de pagamento</p>
                </div>
                
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <!-- Course List -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Cursos selecionados:</h4>
                        @foreach($courses as $course)
                            <div class="flex justify-between items-center py-3 border-b">
                                <div>
                                    <p class="font-medium">{{ $course->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $course->level }}</p>
                                </div>
                                <p class="font-bold text-blue-600">R$ {{ number_format($course->price, 2, ',', '.') }}</p>
                            </div>
                        @endforeach
                        
                        <div class="flex justify-between items-center py-4 text-lg font-bold">
                            <span>Total:</span>
                            <span class="text-blue-600">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-900">Selecione a forma de pagamento:</h4>
                            
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 border-gray-300 hover:border-blue-500">
                                <input type="radio" name="gateway" value="mercadopago" class="h-4 w-4 text-blue-600" required>
                                <div class="ml-4">
                                    <span class="block text-sm font-medium text-gray-900">Mercado Pago</span>
                                    <span class="block text-sm text-gray-500">Cartão de crédito, débito, PIX e boleto</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 border-gray-300 hover:border-blue-500">
                                <input type="radio" name="gateway" value="stripe" class="h-4 w-4 text-blue-600">
                                <div class="ml-4">
                                    <span class="block text-sm font-medium text-gray-900">Stripe</span>
                                    <span class="block text-sm text-gray-500">Cartão de crédito internacional</span>
                                </div>
                            </label>
                        </div>

                        @if($errors->any())
                            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mt-6 flex justify-between">
                            <a href="{{ route('cart.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Voltar ao Carrinho
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                                Prosseguir para Pagamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
