<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Easy Store Installation</title>
    <link href="{{ asset('assets/front/css/install.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Easy Store</h1>
            <p>Let's get your marketplace up and running</p>
        </div>

        <div class="steps">
            <div class="step current">
                <div class="step-icon">1</div>
                <div>Check Requirements</div>
            </div>
            <div class="step pending">
                <div class="step-icon">2</div>
                <div>Database Configuration</div>
            </div>
            <div class="step pending">
                <div class="step-icon">3</div>
                <div>Admin Account</div>
            </div>
            <div class="step pending">
                <div class="step-icon">4</div>
                <div>License Verification</div>
            </div>
            <div class="step pending">
                <div class="step-icon">5</div>
                <div>Installation</div>
            </div>
        </div>

        <p class="text-muted my-20">
            This installation wizard will guide you through setting up your Easy Store marketplace.
        </p>

        <a href="{{ route('install.requirements') }}" class="btn btn-primary">
            Start Installation
        </a>

        <div class="footer">
            Easy Store Installation Wizard v1.0
        </div>
    </div>
</body>

</html>