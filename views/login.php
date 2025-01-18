
<?php
$title = 'Login with Email';
ob_start();
?>

<div class="card form-container shadow p-4">
    <h2 class="card-title text-center mb-4">Login with Email</h2>
    <form action="/DonationProjecttt/index.php?action=login&login_type=email" method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-3">Login</button>
        <p class="text-center mt-3">
            Don't have an account? <a href="/DonationProjecttt/index.php?action=signup">Sign up</a>
        </p>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
