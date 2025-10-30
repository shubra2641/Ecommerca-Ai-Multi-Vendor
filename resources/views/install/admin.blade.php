<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account - Easy Store Installation</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Admin Account</h1>
            <p>Create your administrator account</p>
        </div>

        <form method="POST" action="{{ route('install.admin.store') }}">
            @csrf

            @if($errors->any())
            <div class="alert alert-error">
                <strong>Error:</strong>
                <ul class="list-compact">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    class="@error('name') error @enderror">
                @error('name')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="@error('email') error @enderror">
                @error('email')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password"
                    name="password"
                    id="password"
                    class="@error('password') error @enderror">
                @error('password')
                <div class="error">{{ $message }}</div>
                @enderror
                <small class="hint">Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password"
                    name="password_confirmation"
                    id="password_confirmation">
            </div>

            <div class="alert alert-info">
                <strong>Important:</strong> This account will have full administrative access to your Easy Store marketplace. Keep your credentials secure.
            </div>

            <div class="btn-group">
                <a href="{{ route('install.database') }}" class="btn btn-secondary">
                    Back
                </a>

                <button type="submit" class="btn btn-primary">
                    Continue
                </button>
            </div>
        </form>
    </div>
</body>

</html>