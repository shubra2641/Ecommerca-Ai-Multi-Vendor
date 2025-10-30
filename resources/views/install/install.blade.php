<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Easy Store Installation</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Ready to Install</h1>
            <p>Everything is configured. Let's install your marketplace.</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <strong>Success:</strong> {{ session('success') }}
        </div>
        @endif

        <h3>Installation Summary</h3>
        <div class="steps">
            <div class="step completed">
                <div class="step-icon">✓</div>
                <div>System Requirements</div>
            </div>
            <div class="step completed">
                <div class="step-icon">✓</div>
                <div>Database Configuration</div>
            </div>
            <div class="step completed">
                <div class="step-icon">✓</div>
                <div>Admin Account</div>
            </div>
            <div class="step completed">
                <div class="step-icon">✓</div>
                <div>License Verification</div>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>What will be installed?</strong><br>
            • Database tables and structure<br>
            • Default settings and configuration<br>
            • Administrator account<br>
            • Basic marketplace setup
        </div>

        @if($errors->any())
        <div class="alert alert-error">
            <strong>Error:</strong>
            <ul style="margin: 5px 0 0 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="alert alert-warning" style="margin-top:16px;">
            <strong>Note:</strong> Fresh install will drop all existing tables and recreate them.
        </div>

        <div class="btn-group" style="gap:10px; flex-wrap: wrap;">
            <a href="{{ route('install.license') }}" class="btn btn-secondary">
                Back
            </a>

            <!-- Fresh install without demo data (no seeding) -->
            <form method="POST" action="{{ route('install.process') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="fresh" value="1">
                <input type="hidden" name="seed" value="0">
                <button type="submit" class="btn btn-primary">
                    Install (Fresh – No Data)
                </button>
            </form>

            <!-- Fresh install with demo/required data (run seeders) -->
            <form method="POST" action="{{ route('install.process') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="fresh" value="1">
                <input type="hidden" name="seed" value="1">
                <button type="submit" class="btn btn-success">
                    Install + Data (Fresh + Seed)
                </button>
            </form>
        </div>
    </div>
</body>

</html>