<!-- views/donate_money.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Donation</title>
</head>
<body>
    <h1>Donate Money</h1>
    <form method="POST" action="/DonationProjecttt/index.php?action=donate&donation_type=money">
        <label for="amount">Amount ($):</label>
        <input type="number" name="amount" id="amount" required><br>
        <select name="payment_method">
        <option value="cash">Cash</option>
        <option value="visa">Visa</option>
    </select>
        <button type="submit">Donate</button>
    </form>
</body>
</html>
