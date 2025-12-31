@extends('layouts.web')

@section('title', $post->title)

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container-premium max-w-4xl">
        <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
            @if($post->image)
                <div class="w-full h-64 md:h-96 overflow-hidden">
                    <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-6 md:p-10">
                <header class="mb-8">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <a href="{{ route('blog.index') }}" class="text-purple-600 hover:text-purple-700 font-medium flex items-center mr-auto">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Retour au blog
                        </a>
                        <span>{{ $post->published_at->format('d M Y') }}</span>
                        @if($post->user)
                            <span class="mx-2">•</span>
                            <span>{{ $post->user->name }}</span>
                        @endif
                    </div>

                    <h1 class="text-3xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                        {{ $post->title }}
                    </h1>
                </header>

                <div class="prose prose-lg prose-purple max-w-none">
                    {!! $post->content !!}
                </div>
                
                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <div class="flex space-x-4">
                        <!-- Social Share buttons could go here -->
                    </div>
                </div>
            </div>
        </article>
        
        <div class="mt-12 text-center">
            <a href="{{ route('blog.index') }}" class="btn-premium px-8 py-3 rounded-full inline-flex items-center">
                Voir tous les articles
            </a>
        </div>
    </div>
</div>
@endsection
