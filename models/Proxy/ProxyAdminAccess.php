<?php

require_once 'AdminInterface.php';
require_once 'RealAdmin.php';
require_once '../models/User.php';

class ProxyAdminAccess implements AdminInterface {
    private $realAdmin;
    private $user;
    private $db;

    public function __construct(User $user, $db) {
        $this->user = $user;
        $this->db = $db;
    }

    public function accessAdminPanel() {
        // Debugging output
        error_log("User Type: " . $this->user->getType());
        
        if ($this->user->getType() === 'super_admin') {
            if ($this->realAdmin === null) {
                $this->realAdmin = new RealAdmin($this->db);
            }
            return $this->realAdmin->accessAdminPanel();
        } else {
            
        }
    }
    
    
        
}
