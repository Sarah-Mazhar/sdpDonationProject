
<?php
$title = 'Sign Up';
ob_start();
?>

<div class="card form-container shadow p-4">
    <h2 class="card-title text-center mb-4">Sign Up</h2>
    <form action="/DonationProjecttt/index.php?action=signup" method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" id="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" id="password" required placeholder="Enter your password">
        </div>
        <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="text" class="form-control" name="mobile" id="mobile" required placeholder="Enter your mobile number">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
