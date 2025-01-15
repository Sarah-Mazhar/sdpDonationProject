<?php
interface DonationAdminInterface {
    public function viewDonations();
    public function deleteDonation(int $donationId);
}
