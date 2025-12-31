@extends('layouts.web')

@section('title', 'Blog')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container-premium">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">Notre Blog</h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Découvrez nos derniers articles, conseils et actualités sur l'apprentissage en ligne.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
                    @if($post->image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-purple-600 to-teal-400 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <span>{{ $post->published_at->format('d M Y') }}</span>
                            @if($post->user)
                                <span class="mx-2">•</span>
                                <span>{{ $post->user->name }}</span>
                            @endif
                        </div>
                        
                        <h2 class="text-xl font-bold mb-3 text-gray-900 hover:text-purple-600 transition-colors">
                            <a href="{{ route('blog.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h2>
                        
                        <div class="text-gray-600 mb-4 line-clamp-3 flex-1">
                            {!! Str::limit(strip_tags($post->content), 150) !!}
                        </div>
                        
                        <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700 transition-colors mt-auto">
                            Lire la suite
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Aucun article publié pour le moment</h3>
                    <p class="text-gray-500 mt-2">Revenez bientôt pour découvrir nos nouveaux contenus.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
