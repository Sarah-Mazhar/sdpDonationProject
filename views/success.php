<?php
// Required Files
require_once '../config/Database.php';
require_once '../models/Iterator/UserIterator.php';
require_once '../models/User.php';
require_once '../models/commands/AddBeneficiaryCommand.php';
require_once '../models/commands/AddUserCommand.php';
require_once '../models/commands/CommandManager.php';
require_once '../models/Proxy/ProxyAdminAccess.php';
require_once '../models/Proxy/RealAdmin.php';
require_once '../controllers/PaymentController.php';
require_once '../controllers/DonationController.php';

session_start();

if (!isset($_SESSION['user_type'])) {
    echo "Access denied.";
    exit;
}

$db = Database::getInstance()->getConnection();
$userModel = new User($db);
$commandManager = new CommandManager();
$userIterator = $userModel->getUsersForIterator();
$donationController = new DonationController();

$groupedUsers = [];
if ($_SESSION['user_type'] === 'super_admin') {
    try {
        echo "User ID: " . $_SESSION['user_id'] . "<br>";
        echo "User Type: " . $_SESSION['user_type'] . "<br>";

        $user = new User($_SESSION['user_id'], $_SESSION['user_type']);
        $proxyAdminAccess = new ProxyAdminAccess($user, $db);
        $proxyAdminAccess->accessAdminPanel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['type'])) {
            $userId = $_POST['user_id'];
            $newRole = $_POST['type'];

            if ($userModel->updateUserRole($userId, $newRole)) {
                echo "<script>alert('User role updated successfully!');</script>";
                echo "<script>window.location.href = '/DonationProjecttt/views/success.php';</script>";
            } else {
                echo "<script>alert('Failed to update user role.');</script>";
            }
        }

        $groupedUsers = $userModel->getUsersGroupedByType();
    } catch (Exception $e) {
        echo "<div style='color: red; text-align: center;'>{$e->getMessage()}</div>";
        exit;
    }
}

if ($_SESSION['user_type'] === 'donation_admin' || $_SESSION['user_type'] === 'super_admin') {
    $allDonations = $donationController->listAllDonations();
}

if ($_SESSION['user_type'] === 'coordinator') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_beneficiary'])) {
            $name = $_POST['name'];
            $needs = $_POST['needs'];

            $stmt = $db->prepare("INSERT INTO beneficiaries (name, needs) VALUES (:name, :needs)");
            $stmt->execute([':name' => $name, ':needs' => $needs]);

            $_SESSION['last_added_id'] = $db->lastInsertId();
            $_SESSION['last_removed_beneficiary'] = null;
            echo "<script>alert('Beneficiary added successfully!');</script>";
        }

        if (isset($_POST['undo'])) {
            if (isset($_SESSION['last_added_id'])) {
                $lastAddedId = $_SESSION['last_added_id'];

                $stmt = $db->prepare("SELECT * FROM beneficiaries WHERE id = :id");
                $stmt->execute([':id' => $lastAddedId]);
                $lastBeneficiary = $stmt->fetch(PDO::FETCH_ASSOC);

                $stmt = $db->prepare("DELETE FROM beneficiaries WHERE id = :id");
                $stmt->execute([':id' => $lastAddedId]);

                $_SESSION['last_removed_beneficiary'] = $lastBeneficiary;
                $_SESSION['last_added_id'] = null;
                echo "<script>alert('Undo successful!');</script>";
            } else {
                echo "<script>alert('Nothing to undo!');</script>";
            }
        }

        if (isset($_POST['redo'])) {
            if (isset($_SESSION['last_removed_beneficiary'])) {
                $lastBeneficiary = $_SESSION['last_removed_beneficiary'];

                $stmt = $db->prepare("INSERT INTO beneficiaries (id, name, needs) VALUES (:id, :name, :needs)");
                $stmt->execute([
                    ':id' => $lastBeneficiary['id'],
                    ':name' => $lastBeneficiary['name'],
                    ':needs' => $lastBeneficiary['needs']
                ]);

                $_SESSION['last_added_id'] = $lastBeneficiary['id'];
                $_SESSION['last_removed_beneficiary'] = null;
                echo "<script>alert('Redo successful!');</script>";
            } else {
                echo "<script>alert('Nothing to redo!');</script>";
            }
        }
    }

    $stmt = $db->query("SELECT * FROM beneficiaries");
    $beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <style>
        .event-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="events.php" class="btn btn-info event-btn">Event</a>
        <h1 class="text-center mb-4">Welcome, <?= htmlspecialchars($_SESSION['user_type']) ?>!</h1>

        <?php if ($_SESSION['user_type'] === 'super_admin'): ?>
            <h3 class="text-center">Manage User Roles</h3>
            <form method="POST" class="form-group">
                <div class="mb-3">
                    <label for="user_id" class="form-label">Select User:</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <?php while ($userIterator->hasNext()): ?>
                            <?php $user = $userIterator->next(); ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>">
                                <?= htmlspecialchars($user['email']) ?> (<?= htmlspecialchars($user['type']) ?>)
                            </option>
                        <?php endwhile; ?>
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
                    <?php $userIterator->reset(); ?>
                    <?php while ($userIterator->hasNext()): ?>
                        <?php $user = $userIterator->next(); ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['type']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        
        <?php elseif ($_SESSION['user_type'] === 'payment_admin'): ?>
        <div class="container py-4">
            <h2 class="text-center text-primary mb-4">Payment Management Dashboard 🗓️</h2>
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Money Donations</h5>
                </div>
                <div class="card-body">
                    <?php
                    $paymentController = new PaymentController();
                    $moneyDonations = $paymentController->viewPayments();
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">User Email</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($moneyDonations as $donation): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($donation['id']) ?></td>
                                        <td>$<?= number_format(htmlspecialchars($donation['amount']), 2) ?></td>
                                        <td><?= htmlspecialchars($donation['user_email']) ?></td>
                                        <td><?= htmlspecialchars($donation['created_at']) ?></td>
                                        <td class="text-center">
                                            <form method="POST" action="/DonationProjecttt/index.php?action=delete_payment" style="display: inline;">
                                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($donation['id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete 🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($moneyDonations)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No donations found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                    <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Add a Money Donation 💵</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/DonationProjecttt/index.php?action=add_payment">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount:</label>
                                <input type="number" step="0.01" id="amount" name="amount" class="form-control" placeholder="Enter donation amount" required>
                            </div>
                            <div class="col-md-6">
                                <label for="method" class="form-label">Payment Method:</label>
                                <select id="method" name="method" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="visa">Visa</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Add Payment ✔️
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

        <?php if ($_SESSION['user_type'] === 'donation_admin' || $_SESSION['user_type'] === 'super_admin'): ?>
            <h3 class="text-center">All Donations</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Food Item</th>
                        <th>Quantity</th>
                        <th>User ID</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allDonations)): ?>
                        <?php foreach ($allDonations as $donation): ?>
                            <tr>
                                <td><?= htmlspecialchars($donation['id']) ?></td>
                                <td><?= htmlspecialchars($donation['type']) ?></td>
                                <td><?= htmlspecialchars($donation['amount'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($donation['food_item'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($donation['quantity'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($donation['user_id']) ?></td>
                                <td><?= htmlspecialchars($donation['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No donations available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
        <?php endif; ?>
    </div>
</body>
</html>

