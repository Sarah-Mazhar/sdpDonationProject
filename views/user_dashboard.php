<?php
session_start();

// Redirect to login if not logged in or if type is not 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/Database.php';

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Fetch the user's email dynamically from the `users` table
$userEmail = "User"; // Default fallback
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT email FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && isset($user['email'])) {
        $userEmail = $user['email'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            color: white;
            padding: 10px 20px;
        }
        .navbar .nav-buttons {
            display: flex;
            gap: 10px;
        }
        .navbar button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .navbar button:hover {
            background: #c0392b;
        }
        .navbar .view-donations {
            background: #3498db;
        }
        .navbar .view-donations:hover {
            background: #2980b9;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .options {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f4f4f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .option:hover {
            background: #e9e9ef;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span>
            Welcome, <?= htmlspecialchars($userEmail); ?>!
        </span>
        <div class="nav-buttons">
            <form action="user_donations.php" method="GET">
                <button type="submit" class="view-donations">View Donations</button>
            </form>
            <form action="" method="POST">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h1>User Dashboard</h1>
        <div class="options">
            <div class="option" onclick="location.href='/DonationProjecttt/index.php?action=donate&donation_type=money';">Donate money</div>
            <div class="option" onclick="location.href='/DonationProjecttt/index.php?action=donate&donation_type=food';">Donate Food</div>
            <div class="option" onclick="location.href='volunteer_events.php';">Volunteer in Event</div>
        </div>
    </div>
</body>
</html>
