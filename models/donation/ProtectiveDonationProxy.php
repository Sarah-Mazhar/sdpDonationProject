<?php
require_once 'DonationAdminInterface.php';
require_once 'RealDonationAdmin.php';

class ProtectiveDonationProxy implements DonationAdminInterface {
    private $realAdmin;
    private $userRole;

    public function __construct(string $userRole) {
        $this->realAdmin = new RealDonationAdmin();
        $this->userRole = $userRole;
    }

    public function viewDonations() {
        $this->realAdmin->viewDonations();
    }

    public function deleteDonation(int $donationId) {
        if ($this->userRole === 'admin') {
            $this->realAdmin->deleteDonation($donationId);
        } else {
            echo "Access denied: Insufficient permissions.";
        }
    }
}
