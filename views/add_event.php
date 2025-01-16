<?php
require_once __DIR__ . '/../config/Database.php';

$database = Database::getInstance();
$conn = $database->getConnection();

// Array of months for the dropdown
$months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['event_name'];
    $month = $_POST['month'];
    $eventDescription = $_POST['event_description'];

    $query = "INSERT INTO events (month, event_name, event_description, created_at, updated_at) 
              VALUES (:month, :event_name, :event_description, NOW(), NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':event_name', $eventName);
    $stmt->bindParam(':event_description', $eventDescription);

    if ($stmt->execute()) {
        header("Location: events.php"); // Redirect to events.php
        exit();
    } else {
        $error = "Error adding event.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Add New Event</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="event_name">Event Name</label>
        <input type="text" name="event_name" id="event_name" required>

        <label for="month">Month</label>
        <select name="month" id="month" required>
            <?php foreach ($months as $month): ?>
                <option value="<?php echo htmlspecialchars($month); ?>"><?php echo htmlspecialchars($month); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="event_description">Event Description</label>
        <textarea name="event_description" id="event_description" rows="4" required></textarea>

        <button type="submit">Save Event</button>
    </form>
</div>

</body>
</html>
