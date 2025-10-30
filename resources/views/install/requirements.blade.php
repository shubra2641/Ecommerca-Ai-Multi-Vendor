<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requirements - Easy Store Installation</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>System Requirements</h1>
            <p>Let's check if your server meets the requirements</p>
        </div>

        <!-- PHP Version -->
        <h3>PHP Version</h3>
        <div class="requirement {{ $requirements['php_version'] ? 'success' : 'error' }}">
            <span>PHP 8.1+ Required</span>
            <span>{{ PHP_VERSION }}</span>
        </div>

        <!-- PHP Extensions -->
        <h3>PHP Extensions</h3>
        <div class="requirements">
            @foreach($requirements['extensions'] as $extension => $status)
            <div class="requirement {{ $status ? 'success' : 'error' }}">
                <span>{{ $extension }}</span>
                <span>{{ $status ? 'OK' : 'Missing' }}</span>
            </div>
            @endforeach
        </div>

        <!-- Writable Directories -->
        <h3>Writable Directories</h3>
        <div class="requirements">
            @foreach($requirements['writable_directories'] as $directory => $status)
            <div class="requirement {{ $status ? 'success' : 'error' }}">
                <span>{{ $directory }}</span>
                <span>{{ $status ? 'Writable' : 'Not Writable' }}</span>
            </div>
            @endforeach
        </div>

        <div class="btn-group">
            <a href="{{ route('install.welcome') }}" class="btn btn-secondary">
                Back
            </a>

            @if($allRequirementsMet)
            <a href="{{ route('install.database') }}" class="btn btn-primary">
                Continue
            </a>
            @else
            <button disabled class="btn disabled">
                Fix Requirements First
            </button>
            @endif
        </div>
    </div>
</body>

</html>