<?php
require_once '../config/Database.php';
require_once '../models/User.php';
require_once '../models/commands/AddBeneficiaryCommand.php';
require_once '../models/commands/AddUserCommand.php';
require_once '../models/commands/CommandManager.php';

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_type'])) {
    echo "Access denied.";
    exit;
}

// Database connection
$db = Database::getInstance()->getConnection();
$userModel = new User($db);
$commandManager = new CommandManager();

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

// Coordinator-specific functionality
if ($_SESSION['user_type'] === 'coordinator') {
    // Handle adding beneficiaries
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_beneficiary'])) {
        $name = $_POST['name'];
        $needs = $_POST['needs'];

        $stmt = $db->prepare("INSERT INTO beneficiaries (name, needs) VALUES (:name, :needs)");
        $stmt->execute([':name' => $name, ':needs' => $needs]);

        // Store the last added ID in the session
        $_SESSION['last_added_id'] = $db->lastInsertId();
        $_SESSION['last_removed_beneficiary'] = null; // Clear redo stack
        echo "<script>alert('Beneficiary added successfully!');</script>";
    }

    // Handle undo operation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['undo'])) {
        if (isset($_SESSION['last_added_id'])) {
            $lastAddedId = $_SESSION['last_added_id'];

            // Fetch the beneficiary data before deletion
            $stmt = $db->prepare("SELECT * FROM beneficiaries WHERE id = :id");
            $stmt->execute([':id' => $lastAddedId]);
            $lastBeneficiary = $stmt->fetch(PDO::FETCH_ASSOC);

            // Delete the last added beneficiary
            $stmt = $db->prepare("DELETE FROM beneficiaries WHERE id = :id");
            $stmt->execute([':id' => $lastAddedId]);

            // Store the removed beneficiary data in the session for redo
            $_SESSION['last_removed_beneficiary'] = $lastBeneficiary;
            $_SESSION['last_added_id'] = null; // Clear undo stack
            echo "<script>alert('Undo successful!');</script>";
        } else {
            echo "<script>alert('Nothing to undo!');</script>";
        }
    }

    // Handle redo operation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redo'])) {
        if (isset($_SESSION['last_removed_beneficiary'])) {
            $lastBeneficiary = $_SESSION['last_removed_beneficiary'];

            // Reinsert the removed beneficiary
            $stmt = $db->prepare("INSERT INTO beneficiaries (id, name, needs) VALUES (:id, :name, :needs)");
            $stmt->execute([
                ':id' => $lastBeneficiary['id'],
                ':name' => $lastBeneficiary['name'],
                ':needs' => $lastBeneficiary['needs']
            ]);

            // Move the reinserted beneficiary back to last added ID
            $_SESSION['last_added_id'] = $lastBeneficiary['id'];
            $_SESSION['last_removed_beneficiary'] = null; // Clear redo stack
            echo "<script>alert('Redo successful!');</script>";
        } else {
            echo "<script>alert('Nothing to redo!');</script>";
        }
    }

    // Fetch beneficiaries
    $stmt = $db->query("SELECT * FROM beneficiaries");
    $beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch donors
    $stmt = $db->query("SELECT id, email FROM users WHERE type = 'user'");
    $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        <!-- Super Admin View -->
        <?php if ($_SESSION['user_type'] === 'super_admin'): ?>
            <h3 class="text-center">Manage User Roles</h3>
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
                        <option value="coordinator">Coordinator</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Role</button>
            </form>

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

       <!-- Coordinator View -->
<?php elseif ($_SESSION['user_type'] === 'coordinator'): ?>
    <h3>Add Beneficiary</h3>
<form method="POST" class="form-group">
    <div class="mb-3">
        <label for="name" class="form-label">Beneficiary Name:</label>
        <input type="text" id="name" name="name" class="form-control">
    </div>
    <div class="mb-3">
        <label for="needs" class="form-label">Needs:</label>
        <input type="text" id="needs" name="needs" class="form-control">
    </div>
    <div class="text-center">
        <button type="submit" name="add_beneficiary" class="btn btn-primary">Add Beneficiary</button>
    </div>
</form>

<div class="text-center mt-3">
    <form method="POST" style="display: inline;">
        <button type="submit" name="undo" class="btn btn-warning">Undo</button>
    </form>
    <form method="POST" style="display: inline;">
        <button type="submit" name="redo" class="btn btn-success">Redo</button>
    </form>
</div>


    <h3 class="mt-5">Beneficiaries</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Needs</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($beneficiaries as $beneficiary): ?>
                <tr>
                    <td><?= htmlspecialchars($beneficiary['id']) ?></td>
                    <td><?= htmlspecialchars($beneficiary['name']) ?></td>
                    <td><?= htmlspecialchars($beneficiary['needs']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Donors</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donors as $donor): ?>
                <tr>
                    <td><?= htmlspecialchars($donor['id']) ?></td>
                    <td><?= htmlspecialchars($donor['email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

