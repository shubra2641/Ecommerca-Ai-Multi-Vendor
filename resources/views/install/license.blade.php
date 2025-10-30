<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Verification - Easy Store Installation</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>License Verification</h1>
            <p>Enter your purchase code to verify your license</p>
        </div>

        <form method="POST" action="{{ route('install.license.store') }}">
            @csrf

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

            <div class="form-group">
                <label for="purchase_code">Purchase Code</label>
                <input type="text"
                    name="purchase_code"
                    id="purchase_code"
                    value="{{ old('purchase_code') }}"
                    placeholder="Enter your purchase code"
                    class="@error('purchase_code') error @enderror">
                @error('purchase_code')
                <div class="error">{{ $message }}</div>
                @enderror
                <small style="color: #666; font-size: 12px;">You can find your purchase code in your account dashboard</small>
            </div>

            <div class="alert alert-info">
                <strong>License Information:</strong><br>
                This license is valid for domain: <strong>{{ request()->getHost() }}</strong><br>
                Make sure you're installing on the correct domain.
            </div>

            <div class="btn-group">
                <a href="{{ route('install.admin') }}" class="btn btn-secondary">
                    Back
                </a>

                <button type="submit" class="btn btn-primary">
                    Verify License
                </button>
            </div>
        </form>
    </div>
</body>

</html>