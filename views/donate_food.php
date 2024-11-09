<!-- views/donate_food.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Donation</title>
</head>
<body>
    <h1>Donate Food</h1>
    <form method="POST" action="/DonationProjecttt/index.php?action=donate&donation_type=food">
        <label for="foodItem">Food Item:</label>
        <input type="text" name="foodItem" id="foodItem" required><br>
        
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" required><br>

        <label for="extras">Add Extras:</label><br>
        <input type="checkbox" name="extras[]" value="fruit" id="fruit">
        <label for="fruit">Add Fruit</label><br>

        <input type="checkbox" name="extras[]" value="vegetables" id="vegetables">
        <label for="vegetables">Add Vegetables</label><br>

        <button type="submit">Donate</button>
    </form>
</body>
</html>
