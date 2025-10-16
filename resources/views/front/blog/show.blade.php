@extends('front.layout')
@section('title', $post->seo_title ?? $post->title)
@section('meta')
@if($post->seo_description)
<meta name="description" content="{{ $post->seo_description }}">@endif
@if($post->seo_tags)
<meta name="keywords" content="{{ $post->seo_tags }}">@endif
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->seo_title ?? $post->title }}">
@if($post->seo_description)
<meta property="og:description" content="{{ $post->seo_description }}">@endif
<meta property="og:url" content="{{ url()->current() }}">
@if($post->featured_image)
<meta property="og:image" content="{{ asset('storage/'.$post->featured_image) }}">@endif
<meta property="og:site_name" content="{{ config('app.name') }}">
@endsection
@section('content')
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}" class="breadcrumb-item">{{ __('Home') }}</a>
            <span class="breadcrumb-separator">/</span>
            <a href="{{ route('blog.index') }}" class="breadcrumb-item">{{ __('Blog') }}</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active">{{ $post->title }}</span>
        </nav>
    <h1 class="page-title">{{ $post->title }}</h1>
    </div>
</section>

<!-- Blog Post Section -->
<section class="blog-post-section">
    <div class="container">
        <div class="blog-post-layout">
            <article class="blog-post">
                <div class="post-meta">
                    <span>{{ $post->published_at?->format('M d, Y') }}</span>
                    @if($post->category)
                        <a href="{{ route('blog.category',$post->category->slug) }}" class="link-dark">{{ $post->category->name }}</a>
                    @endif
                </div>
                @if($post->featured_image)
                    <div class="post-featured-image">
                        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}">
                    </div>
                @endif
                <div class="post-content content-style">
                    {{ $post->body }}
                </div>
                @if($post->tags->count())
                    <div class="post-tags">
                        @foreach($post->tags as $tag)
                            <a href="{{ route('blog.tag',$tag->slug) }}">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif
            </article>
            <aside class="post-sidebar">
                <div>
                    <h3>{{ __('Recent Posts') }}</h3>
                    <ul>
                        @foreach($related as $r)
                            <li><a href="{{ route('blog.show',$r->slug) }}">{{ $r->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection