@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3>System Requirements</h3>
                    <p>PHP Version: <strong>{{ $phpVersion }}</strong></p>
                    <ul>
                        @foreach($extensions as $ext => $ok)
                            <li class="mb-1">{{ $ext }}: <span class="badge {{ $ok ? 'bg-success' : 'bg-danger' }}">{{ $ok ? 'OK' : 'Missing' }}</span></li>
                        @endforeach
                    </ul>

                    <hr />
                    <h5>Filesystem Permissions</h5>
                    <p>Click the button below to verify writable paths required by the application.</p>
                    <button id="checkPerms" class="btn btn-outline-primary">Check Permissions</button>

                    <div id="permResults" class="mt-3"></div>

                    <div class="mt-4">
                        <a href="{{ route('install.database') }}" class="btn btn-primary">Next: Database</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection
