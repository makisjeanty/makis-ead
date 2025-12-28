<x-app-layout>
    <div class="flex h-screen bg-gray-100" style="height: calc(100vh - 65px);">
        <div class="w-80 bg-white border-r overflow-y-auto hidden md:block">
            <div class="p-4 border-b bg-gray-50">
                <h2 class="font-bold text-gray-700">{{ $course->title }}</h2>
                <div class="text-xs text-gray-500 mt-1">Progresso: 10%</div>
            </div>
            
            @foreach($course->modules as $module)
            <div x-data="{ open: true }" class="border-b">
                <button @click="open = !open" class="w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 text-left">
                    <span class="font-semibold text-sm text-gray-700">{{ $module->title }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" class="bg-white">
                    @foreach($module->lessons as $lesson)
                    <a href="{{ route('student.classroom.watch', ['slug' => $course->slug, 'lesson' => $lesson->id]) }}" 
                       class="block p-3 pl-6 text-sm hover:bg-indigo-50 border-l-4 {{ $currentLesson->id == $lesson->id ? 'border-indigo-500 bg-indigo-50 text-indigo-700 font-bold' : 'border-transparent text-gray-600' }}">
                        <div class="flex items-center">
                            @if($currentLesson->id == $lesson->id) 
                                <span class="mr-2 text-indigo-600">●</span> 
                            @else
                                <span class="mr-2 text-gray-300">○</span>
                            @endif
                            {{ $lesson->title }}
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex-1 overflow-y-auto p-8 bg-white">
            <div class="max-w-3xl mx-auto">
                <div class="mb-8 border-b pb-4">
                    <span class="text-sm font-bold text-indigo-600 tracking-wide uppercase">Aula {{ $currentLesson->sort_order + 1 }}</span>
                    <h1 class="text-3xl font-extrabold text-gray-900 mt-1">{{ $currentLesson->title }}</h1>
                </div>

                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {!! $currentLesson->content !!}
                </div>

                <div class="mt-12 p-6 bg-gray-50 rounded-xl border border-gray-200 text-center">
                    <p class="text-gray-600 mb-4">Leu tudo? Vamos testar seu conhecimento.</p>
                    <button class="bg-indigo-600 text-white px-8 py-3 rounded-full font-bold hover:bg-indigo-700 shadow-lg transform hover:-translate-y-1 transition duration-200">
                        Continuar para o Quiz ➤
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
