<x-app-layout>
    <div id="loading-spinner" class="fixed inset-0 bg-white bg-opacity-80 z-50 hidden flex justify-center items-center backdrop-blur-sm transition-opacity duration-300">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-12 w-12 text-amber-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-amber-600 font-semibold animate-pulse">Carregando cursos...</span>
        </div>
    </div>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Explorar Cursos
            </h2>
            <form method="GET" action="{{ route('student.courses.index') }}" onsubmit="showLoading()" class="w-full md:w-1/3 relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="O que você quer aprender?" 
                       class="w-full rounded-lg border-gray-300 pl-4 pr-10 py-3 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-sm group-hover:shadow-md">
                <button type="submit" class="absolute right-2 top-2.5 text-gray-400 hover:text-amber-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="bg-gradient-to-r from-amber-500 to-orange-600 py-10 shadow-lg mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-extrabold tracking-tight mb-2">Evolua sua carreira hoje</h1>
                <p class="text-amber-100 text-lg font-medium">Os melhores conteúdos práticos para o seu crescimento.</p>
            </div>
            <div class="hidden md:block text-5xl opacity-30 animate-bounce">
                🚀
            </div>
        </div>
    </div>

    <div class="pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 text-lg">Filtros</h3>
                        @if(request()->anyFilled(['category', 'level', 'price', 'search']))
                            <a href="{{ route('student.courses.index') }}" onclick="showLoading()" class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-wide">Limpar tudo</a>
                        @endif
                    </div>
                    
                    <form id="filter-form" method="GET" action="{{ route('student.courses.index') }}">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Nível</h4>
                            <div class="space-y-3">
                                @foreach(['Iniciante', 'Intermediário', 'Avançado'] as $lvl)
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" name="level[]" value="{{ $lvl }}" onchange="submitFilters()"
                                           {{ in_array($lvl, (array)request('level')) ? 'checked' : '' }} 
                                           class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 h-4 w-4 cursor-pointer"> 
                                    <span class="ml-3 text-gray-600 group-hover:text-amber-600 transition text-sm">{{ $lvl }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-2">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Investimento</h4>
                            <select name="price" onchange="submitFilters()" class="w-full rounded-md border-gray-300 text-sm focus:border-amber-500 focus:ring focus:ring-amber-200 focus:ring-opacity-50 transition cursor-pointer">
                                <option value="">Todos os preços</option>
                                <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>Gratuito</option>
                                <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>Pago</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <div class="flex-1">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600 font-medium">{{ $courses->total() }} cursos encontrados</p>
                    
                    <div class="relative">
                        <select class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-8 rounded-lg leading-tight focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm font-medium cursor-pointer">
                            <option>Mais recentes</option>
                            <option>Mais populares</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>

                @if($courses->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8">
                    @foreach($courses as $course)
                    
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full overflow-hidden group transform hover:-translate-y-1">
                        
                        <div class="relative h-48 overflow-hidden bg-gray-100">
                             <img loading="lazy" decoding="async" src="{{ $course->image_url ?? 'https://via.placeholder.com/400x250' }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-out">
                            
                            <div class="absolute top-3 right-3 shadow-md">
                                @if($course->price == 0)
                                    <span class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full tracking-wide">GRÁTIS</span>
                                @else
                                    <span class="bg-white text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full border border-gray-200">
                                        R$ {{ number_format($course->price, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded uppercase tracking-wide border border-amber-100">
                                    {{ $course->category ?? 'Geral' }}
                                </span>
                                <div class="flex items-center bg-gray-50 px-2 py-1 rounded-lg">
                                    <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="text-xs font-bold text-gray-700">{{ number_format($course->rating, 1) }}</span>
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2 group-hover:text-amber-600 transition-colors duration-200">
                                {{ $course->title }}
                            </h3>
                            <p class="text-sm text-gray-600 line-clamp-3 mb-6 leading-relaxed">
                                {{ $course->description }}
                            </p>

                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <span class="text-xs text-gray-500 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    {{ $course->level }}
                                </span>
                                
                                <a href="{{ $course->price > 0 ? route('student.checkout.purchase', $course->slug) : route('student.classroom.watch', $course->slug) }}" 
                                   class="bg-amber-500 text-white text-sm font-bold px-4 py-2 rounded-lg hover:bg-amber-600 hover:shadow-lg transition-all duration-200 transform active:scale-95">
                                    {{ $course->price > 0 ? "Comprar Agora" : "Acessar Grátis" }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $courses->withQueryString()->links() }}
                </div>
                
                @else
                    <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
                        <div class="bg-gray-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Nenhum curso encontrado</h3>
                        <p class="text-gray-500 mt-1 max-w-sm mx-auto">Não encontramos resultados para sua busca. Tente remover os filtros.</p>
                        <a href="{{ route('student.courses.index') }}" onclick="showLoading()" class="mt-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-amber-700 bg-amber-100 hover:bg-amber-200">
                            Limpar Filtros
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loading-spinner').classList.remove('hidden');
        }

        function submitFilters() {
            showLoading();
            document.getElementById('filter-form').submit();
        }
    </script>
</x-app-layout>
