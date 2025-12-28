<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Carteira - Makis EAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Minha Carteira</h1>
                <div class="flex gap-4">
                    <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <a href="{{ route('courses.index') }}" class="text-blue-600 hover:text-blue-800">Cursos</a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Balance Card -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-8 mb-8 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90">Saldo Disponível</p>
                        <h2 class="text-5xl font-bold mt-2">{{ $wallet->currency }} {{ number_format($wallet->balance, 2) }}</h2>
                        <p class="text-sm mt-2 opacity-75">Status: <span class="font-semibold">{{ ucfirst($wallet->status) }}</span></p>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('wallet.deposit') }}" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition inline-block">
                            💰 Adicionar Saldo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Depositado</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $wallet->currency }} {{ number_format($stats['total_deposits'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Gasto</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $wallet->currency }} {{ number_format($stats['total_spent'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Transações Pendentes</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['pending_transactions'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Transações Recentes</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Últimas 10 transações</p>
                    </div>
                    <a href="{{ route('wallet.history') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Ver Todas →
                    </a>
                </div>
                <div class="border-t border-gray-200">
                    @forelse($recentTransactions as $transaction)
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        @if($transaction->type === 'credit')
                                            <span class="flex-shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                                <span class="text-green-600 text-xl">↑</span>
                                            </span>
                                        @else
                                            <span class="flex-shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100">
                                                <span class="text-red-600 text-xl">↓</span>
                                            </span>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }} {{ $wallet->currency }} {{ number_format($transaction->amount, 2) }}
                                    </p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $transaction->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-gray-500">Nenhuma transação ainda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</body>
</html>
