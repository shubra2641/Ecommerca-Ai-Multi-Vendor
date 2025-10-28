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
<meta property="og:image" content="{{ \App\Helpers\GlobalHelper::storageImageUrl($post->featured_image) }}">@endif
<meta property="og:site_name" content="{{ config('app.name') }}">
@endsection
@section('content')
<section class="page-header">
    <div class="container">
        <x-breadcrumb :items="[
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Blog'), 'url' => route('blog.index'), 'icon' => 'fas fa-blog'],
            ['title' => $post->title, 'url' => '#']
        ]" />
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
                    <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($post->featured_image) ?: asset('images/placeholder.png') }}" alt="{{ $post->title }}">
                </div>
                @endif
                <div class="post-content content-style">
                    {!! $post->body_html !!}
                </div>
                <style>
                .content-style strong {
                    font-weight: bold;
                }
                .content-style em {
                    font-style: italic;
                }
                .content-style h1, .content-style h2, .content-style h3, .content-style h4, .content-style h5, .content-style h6 {
                    margin: 1.5rem 0 1rem;
                    font-weight: 600;
                    line-height: 1.3;
                }
                .content-style h1 { font-size: 2rem; }
                .content-style h2 { font-size: 1.75rem; }
                .content-style h3 { font-size: 1.5rem; }
                .content-style h4 { font-size: 1.25rem; }
                .content-style h5 { font-size: 1.1rem; }
                .content-style h6 { font-size: 1rem; }
                .content-style ul {
                    margin: 1rem 0;
                    padding-left: 2rem;
                }
                .content-style li {
                    margin-bottom: 0.5rem;
                }
                .content-style p {
                    margin: 1rem 0;
                    line-height: 1.6;
                }
                </style>
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