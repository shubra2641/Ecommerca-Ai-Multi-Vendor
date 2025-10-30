<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration - Easy Store Installation</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Database Configuration</h1>
            <p>Enter your database connection details</p>
        </div>

        <form method="POST" action="{{ route('install.database.store') }}">
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
                <label for="db_host">Database Host</label>
                <input type="text"
                    name="db_host"
                    id="db_host"
                    value="{{ old('db_host', 'localhost') }}"
                    class="@error('db_host') error @enderror">
                @error('db_host')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="db_port">Database Port</label>
                <input type="number"
                    name="db_port"
                    id="db_port"
                    value="{{ old('db_port', '3306') }}"
                    class="@error('db_port') error @enderror">
                @error('db_port')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="db_name">Database Name</label>
                <input type="text"
                    name="db_name"
                    id="db_name"
                    value="{{ old('db_name') }}"
                    class="@error('db_name') error @enderror">
                @error('db_name')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="db_username">Database Username</label>
                <input type="text"
                    name="db_username"
                    id="db_username"
                    value="{{ old('db_username') }}"
                    class="@error('db_username') error @enderror">
                @error('db_username')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="db_password">Database Password</label>
                <input type="password"
                    name="db_password"
                    id="db_password"
                    value="{{ old('db_password') }}"
                    class="@error('db_password') error @enderror">
                @error('db_password')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="btn-group">
                <a href="{{ route('install.requirements') }}" class="btn btn-secondary">
                    Back
                </a>

                <button type="submit" class="btn btn-primary">
                    Test Connection
                </button>
            </div>
        </form>
    </div>
</body>

</html>