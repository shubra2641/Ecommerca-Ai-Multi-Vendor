@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-3">Welcome</h2>
            <p class="lead">This setup wizard will guide you through preparing the application for first use. Click continue to run a quick environment check.</p>
            <a href="{{ route('install.requirements') }}" class="btn btn-primary">Continue</a>
        </div>
    </div>
</div>
@endsection
