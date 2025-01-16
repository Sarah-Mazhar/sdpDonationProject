<?php
$title = 'Donation Successful';
ob_start();
?>

<div class="p-4 shadow rounded">
    <h2 class="text-center text-success mb-4">Thank You!</h2>
    <p class="text-center">Your donation was processed successfully. We appreciate your support for our cause.</p>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
