@extends('front.layout')
@section('title', $category->seo_title ?? $category->name)
@section('meta')
@if($category->seo_description)
<meta name="description" content="{{ $category->seo_description }}">@endif
@if($category->seo_tags)
<meta name="keywords" content="{{ $category->seo_tags }}">@endif
@endsection
@section('content')
<section class="page-header">
    <div class="container">
        <x-breadcrumb :items="[
            ['title' => __('Home'), 'url' => route('home'), 'icon' => 'fas fa-home'],
            ['title' => __('Blog'), 'url' => route('blog.index'), 'icon' => 'fas fa-blog'],
            ['title' => $category->name, 'url' => '#']
        ]" />
        <h1 class="page-title">{{ $category->name }}</h1>
    </div>
</section>
<section class="blog-section">
    <div class="container">
        @if($posts->count())
            <div class="blog-grid">
                @foreach($posts as $post)
                    <article class="blog-card">@include('front.components.post-card',['post'=>$post])</article>
                @endforeach
            </div>
            @if($posts->hasPages())
                <div class="pagination-wrapper">{{ $posts->links() }}</div>
            @endif
        @else
            <div class="empty-state">
                <h3 class="empty-state-title">{{ __('No posts.') }}</h3>
            </div>
        @endif
    </div>
</section>
@endsection