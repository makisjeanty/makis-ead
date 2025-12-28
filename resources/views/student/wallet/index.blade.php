<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Minha Carteira (MonCash)</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-gradient-to-r from-red-600 to-red-500 rounded-2xl shadow-lg p-6 text-white col-span-1">
                <h3 class="text-lg font-medium opacity-90">Saldo Disponível</h3>
                <div class="text-4xl font-extrabold mt-2">
                    HTG {{ number_format($user->wallet->balance, 2, ',', '.') }}
                </div>
                <p class="text-sm opacity-75 mt-1">Gourde Haitiano</p>
                
                <div class="mt-8 bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                    <form action="{{ route('student.wallet.deposit') }}" method="POST">
                        @csrf
                        <label class="text-sm font-bold block mb-2">Recarregar (MonCash)</label>
                        <div class="flex gap-2">
                            <input type="number" name="amount" placeholder="Valor" class="w-full rounded text-gray-900 text-sm p-2" required min="10">
                            <button type="submit" class="bg-white text-red-600 font-bold px-4 py-2 rounded hover:bg-gray-100 transition">
                                +
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 col-span-2">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Histórico de Transações</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-xs">
                            <tr>
                                <th class="p-3">Data</th>
                                <th class="p-3">Descrição</th>
                                <th class="p-3">Ref</th>
                                <th class="p-3 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transactions as $t)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3">
                                    <span class="block font-medium text-gray-800">{{ $t->description }}</span>
                                    <span class="text-xs text-gray-400 capitalize">{{ $t->status }}</span>
                                </td>
                                <td class="p-3 text-xs font-mono text-gray-500">{{ $t->reference_id }}</td>
                                <td class="p-3 text-right font-bold {{ $t->type == 'deposit' ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $t->type == 'deposit' ? '+' : '-' }} {{ number_format($t->amount, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $transactions->links() }}</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
