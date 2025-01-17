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
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <form action="" method="POST">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>

    <div class="container">
        <h1>User Dashboard</h1>
        <div class="options">
            <div class="option" onclick="location.href='show_profile.php';">Show Profile</div>
            <div class="option" onclick="location.href='donate.php';">Donate Money and Food</div>
            <!-- Update the link for Volunteer in Event to go to volunteer_events.php -->
            <div class="option" onclick="location.href='volunteer_events.php';">Volunteer in Event</div>
        </div>
    </div>
</body>
</html>