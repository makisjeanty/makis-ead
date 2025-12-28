<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Cards de resumo --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Usuários</p>
                    <p class="text-2xl font-bold">120</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Cursos</p>
                    <p class="text-2xl font-bold">8</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Matrículas</p>
                    <p class="text-2xl font-bold">342</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Receita</p>
                    <p class="text-2xl font-bold">R$ 12.450</p>
                </div>
            </div>

            {{-- Boas-vindas --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">
                    Bem-vindo, {{ auth()->user()->name }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Aqui você acompanha seus dados e acessa as principais funções do sistema.
                </p>
            </div>

            {{-- Ações rápidas --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Ações rápidas</h3>

                <div class="flex flex-wrap gap-4">
                    <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Criar curso
                    </a>

                    <a href="#" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Ver matrículas
                    </a>

                    <a href="#" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                        Perfil
                    </a>
                </div>
            </div>

            {{-- Últimas atividades --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Últimas atividades</h3>

                <ul class="space-y-2 text-sm">
                    <li>✅ Novo usuário registrado</li>
                    <li>📘 Curso “Laravel Básico” atualizado</li>
                    <li>💳 Nova matrícula confirmada</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
