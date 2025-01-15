<?php
require_once 'DonationAdminInterface.php';

class RealDonationAdmin implements DonationAdminInterface {
    public function viewDonations() {
        echo "Displaying all donations.";
    }

    public function deleteDonation(int $donationId) {
        echo "Donation with ID $donationId deleted.";
    }
}
