<?php
require_once '../config/Database.php';
require_once '../models/User.php';

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_type'])) {
    echo "Access denied.";
    exit;
}

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();
$userModel = new User($conn);

// Handle role assignment for super admins
if ($_SESSION['user_type'] === 'super_admin' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['type'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['type'];

    if ($userModel->updateUserRole($userId, $newRole)) {
        echo "<script>alert('User role updated successfully!');</script>";
        echo "<script>window.location.href = '/DonationProjecttt/views/success.php';</script>";
    } else {
        echo "<script>alert('Failed to update user role.');</script>";
    }
}

// Fetch all users for super admin view
$groupedUsers = [];
if ($_SESSION['user_type'] === 'super_admin') {
    $groupedUsers = $userModel->getUsersGroupedByType();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Welcome, <?= htmlspecialchars($_SESSION['user_type']) ?>!</h1>

        <?php if ($_SESSION['user_type'] === 'super_admin'): ?>
            <h3 class="text-center">Manage User Roles</h3>

            <!-- Role Assignment Form -->
            <form method="POST" class="form-group">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Select User:</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <?php foreach ($groupedUsers as $type => $typeUsers): ?>
                            <optgroup label="<?= ucfirst($type) ?> Users">
                                <?php foreach ($typeUsers as $user): ?>
                                    <option value="<?= htmlspecialchars($user['id']) ?>">
                                        <?= htmlspecialchars($user['email']) ?> (<?= htmlspecialchars($user['type']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Assign Role:</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="donation_admin">Donation Admin</option>
                        <option value="payment_admin">Payment Admin</option>
                        <option value="user">Regular User</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Role</button>
            </form>

            <!-- List of All Users -->
            <h3 class="text-center mt-5">All Users</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedUsers as $type => $typeUsers): ?>
                        <?php foreach ($typeUsers as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['type']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($_SESSION['user_type'] === 'payment_admin'): ?>
            <h3 class="text-center">Manage Payments</h3>
            <p>Here you can add, edit, and delete payments.</p>
            <!-- Implement payment management functionality -->

        <?php elseif ($_SESSION['user_type'] === 'donation_admin'): ?>
            <h3 class="text-center">Manage Donations</h3>
            <p>Here you can add, edit, and delete donations.</p>
            <!-- Implement donation management functionality -->

        <?php else: ?>
            <h3 class="text-center">No Administrative Privileges</h3>
            <p>You have no authority to manage this system.</p>
        <?php endif; ?>
    </div>
</body>
</html>
