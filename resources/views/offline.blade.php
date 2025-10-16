@extends('front.layout')
@section('title','Offline')
@section('content')
<div class="container py-5 fade-in-up">
    <div class="u-max-w-prose u-center">
        <h1 class="mb-3">You are offline</h1>
        <p class="u-muted">It looks like you lost your connection. Content you previously opened may still be available. Once you're back online, reload this page.</p>
        <a href="/" class="btn-ghost mt-3">Go Home</a>
    </div>
</div>
@endsection