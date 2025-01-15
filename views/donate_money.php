<!-- views/donate_money.php -->

<?php
$title = 'Donate Money';
ob_start();
?>

<div class="p-4 shadow rounded">
    <h2 class="text-center mb-4">Support Our Cause</h2>
    <p class="text-center mb-4">Your donation can make a difference. Please select an amount and payment method to proceed.</p>
    <form method="POST" action="/DonationProjecttt/index.php?action=donate&donation_type=money">
        <div class="form-group">
            <label for="amount">Amount ($):</label>
            <input type="number" class="form-control" name="amount" id="amount" required>
        </div>
        
        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" class="form-control" id="payment_method">
                <option value="cash">Cash</option>
                <option value="visa">Visa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-3">Donate Now</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
