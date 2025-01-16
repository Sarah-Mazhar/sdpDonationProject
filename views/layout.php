<!-- views/layout.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Donation Project' ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f5f7fa; font-family: Arial, sans-serif; }
        .form-container { max-width: 400px; margin-top: 20px; }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; border-color: #0056b3; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Donation Project</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="/DonationProjecttt/index.php?action=login&login_type=email">Login with Email</a></li>
                <li class="nav-item"><a class="nav-link" href="/DonationProjecttt/index.php?action=login&login_type=mobile">Login with Mobile</a></li>
                <li class="nav-item"><a class="nav-link" href="/DonationProjecttt/index.php?action=signup">Sign Up</a></li>
                <li class="nav-item"><a class="nav-link" href="/DonationProjecttt/index.php?action=donate&donation_type=money">Donate Money</a></li>
                <li class="nav-item"><a class="nav-link" href="/DonationProjecttt/index.php?action=donate&donation_type=food">Donate Food</a></li>
                <li class="nav-item"><a class="nav-link" href="/DonationProjecttt/index.php?action=show_beneficiaries">Show Beneficiaries</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <?= $content ?? '' ?>
    </div>
</body>
</html>
