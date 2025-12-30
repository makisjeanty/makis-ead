<div class="border border-gray-200 rounded-lg mb-4 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <h3 class="text-sm font-medium text-gray-800">{{ $module['module']->title }}</h3>
        <p class="text-xs text-gray-500 mt-1">
            {{ $module['completed_lessons'] }} de {{ $module['total_lessons'] }} aulas completas
        </p>
    </div>
    <div class="p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progresso</span>
            <span class="text-sm font-medium text-blue-600">{{ $module['progress_percentage'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div 
                class="h-2 rounded-full bg-blue-500" 
                style="width: {{ $module['progress_percentage'] }}%"
            ></div>
        </div>
    </div>
</div>