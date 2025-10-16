@extends('front.layout')
@section('title','Blog')
@section('content')
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}" class="breadcrumb-item">{{ __('Home') }}</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active">{{ __('Blog') }}</span>
        </nav>
        <h1 class="page-title">{{ __('Blog') }}</h1>
        <p class="page-description">{{ __('Stay updated with our latest news and insights') }}</p>
    </div>
</section>

<!-- Blog Posts Section -->
<section class="blog-section">
    <div class="container">
        @if($posts->count())
            <div class="blog-grid">
                @foreach($posts as $post)
                    <article class="blog-card">
                        @include('front.components.post-card',['post'=>$post])
                    </article>
                @endforeach
            </div>
            @if($posts->hasPages())
                <div class="pagination-wrapper">{{ $posts->links() }}</div>
            @endif
        @else
            <div class="empty-state">
                <h3 class="empty-state-title">{{ __('No Blog Posts Yet') }}</h3>
                <p class="empty-state-description">{{ __('Check back later for our latest updates and insights.') }}</p>
            </div>
        @endif
    </div>
</section>
@endsection