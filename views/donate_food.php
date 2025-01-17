<!-- views/donate_food.php -->

<?php
$title = 'Donate Food';
ob_start();
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<div class="p-4 shadow rounded">
    <h2 class="text-center mb-4">Contribute Food to Those in Need</h2>
    <p class="text-center mb-4">Every donation helps feed the hungry. Please select the type of food and quantity you wish to donate.</p>
    <form method="POST" action="/DonationProjecttt/index.php?action=donate&donation_type=food">
        <div class="form-group">
            <label for="foodItem">Food Item:</label>
            <input type="text" class="form-control" name="foodItem" id="foodItem" required placeholder="Enter food item">
        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" name="quantity" id="quantity" required placeholder="Enter quantity">
        </div>

        <div class="form-group">
            <label>Add Extras:</label><br>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="extras[]" value="fruit" id="fruit">
                <label class="form-check-label" for="fruit">Add Fruit</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="extras[]" value="vegetables" id="vegetables">
                <label class="form-check-label" for="vegetables">Add Vegetables</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-3">Donate Food</button>
    </form>

    <form method="get" action="index.php" class="mt-3">
        <input type="hidden" name="action" value="print_receipt">
        <input type="hidden" name="type" value="food">
        <button type="submit" class="btn btn-success btn-block">
            <i class="fas fa-print"></i> Print Food Receipt
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
