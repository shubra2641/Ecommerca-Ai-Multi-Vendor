<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete - Easy Store</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Installation Complete!</h1>
            <p>Your Easy Store marketplace is ready to use</p>
        </div>

        <div class="alert alert-success">
            <strong>Success!</strong> Your marketplace has been successfully installed and configured.
        </div>

        <h3>What's Next?</h3>
        <ul class="text-muted lh-16">
            <li><strong>Configure Settings:</strong> Customize your marketplace settings, payment methods, and more.</li>
            <li><strong>Customize Design:</strong> Upload your logo, choose colors, and customize the appearance.</li>
            <li><strong>Add Vendors:</strong> Invite vendors to join your marketplace and start selling.</li>
        </ul>

        <div class="alert alert-warning">
            <strong>Important:</strong> For security reasons, please delete the installation files after completing the setup.
        </div>

        <div class="mt-30">
            <a href="{{ route('admin.login') }}" class="btn btn-primary mb-10">
                Go to Admin Dashboard
            </a>

            <a href="{{ route('home') }}" class="btn btn-secondary ">
                View Website
            </a>
        </div>

        <div class="footer">
            Easy Store Installation Complete - Thank you for choosing Easy Store!
        </div>
    </div>
</body>

</html>