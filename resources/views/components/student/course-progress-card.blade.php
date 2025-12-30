<div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
    <div class="p-5">
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-4">
                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16" />
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500 truncate">{{ $course->category->name ?? 'Sem categoria' }}</p>
                
                <div class="mt-3">
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                        <span>Progresso</span>
                        <span class="font-medium">{{ $progress['progress_percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div 
                            class="h-2.5 rounded-full {{ $progress['progress_percentage'] == 100 ? 'bg-green-500' : 'bg-blue-500' }}" 
                            style="width: {{ $progress['progress_percentage'] }}%"
                        ></div>
                    </div>
                </div>
                
                <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        {{ $progress['completed_lessons'] }}/{{ $progress['total_lessons'] }} aulas
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        @if($progress['progress_percentage'] == 100)
                            <span class="text-green-600 font-medium">Concluído</span>
                        @else
                            <span>Em andamento</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 flex space-x-3">
            <a 
                href="{{ route('student.classroom.watch', ['courseSlug' => $course->slug]) }}" 
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                </svg>
                Continuar
            </a>
            
            @if($progress['progress_percentage'] == 100)
            <button 
                type="button" 
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Certificado
            </button>
            @endif
        </div>
    </div>
</div>