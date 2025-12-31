<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Makis EAD</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Meu Dashboard</h1>
                <div class="flex items-center gap-4">
                    <a href="{{ route('courses.index') }}" class="text-blue-600 hover:text-blue-800">Ver Cursos</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Sair</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Cursos</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['total_courses'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Concluídos</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['completed_courses'] }}
                                    </dd>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Em Progresso</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['in_progress'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Certificados</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $stats['certificates'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Courses -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Meus Cursos</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Cursos em que você está matriculado</p>
                </div>
                <div class="border-t border-gray-200">
                    @forelse($enrollments as $enrollment)
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-blue-600">{{ $enrollment->course->title }}</h4>
                                    <p class="mt-1 text-sm text-gray-500">Matriculado em
                                        {{ $enrollment->enrolled_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ number_format($enrollment->progress_percentage, 0) }}%</p>
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mt-1">
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.classroom.watch', $enrollment->course->slug) }}"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Continuar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <p class="text-gray-500">Você ainda não está matriculado em nenhum curso.</p>
                            <a href="{{ route('courses.index') }}"
                                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Explorar Cursos
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recommended Courses -->
            @if($recommendedCourses->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Cursos Recomendados</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Cursos populares que você pode gostar</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach($recommendedCourses as $course)
                                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}"
                                            class="w-full h-32 object-cover">
                                    @else
                                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">Sem imagem</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h4 class="font-medium text-sm mb-2">{{ $course->title }}</h4>
                                        <p class="text-blue-600 font-bold">
                                            @if($course->price > 0)
                                                R$ {{ number_format($course->price, 2, ',', '.') }}
                                            @else
                                                Grátis
                                            @endif
                                        </p>
                                        <a href="{{ route('courses.show', $course->slug) }}"
                                            class="mt-2 block text-center px-3 py-2 border border-blue-600 text-sm text-blue-600 rounded hover:bg-blue-50">
                                            Ver Curso
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>
</body>

</html>
