<!DOCTYPE html>
<html>
<head>
    <title>User Balances</title>
    <link rel="stylesheet" href="{{ asset('front/css/envato-extracted.css') }}" />
</head>
<body>
    <h1>User Balances</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ number_format($user->balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
