<?php
class AddBeneficiaryCommand implements Command {
    private $db;
    private $beneficiaryData;

    public function __construct($db, $beneficiaryData) {
        $this->db = $db;
        $this->beneficiaryData = $beneficiaryData;
    }

    public function execute() {
        $stmt = $this->db->prepare("INSERT INTO beneficiaries (name, needs) VALUES (?, ?)");
        $stmt->execute([$this->beneficiaryData['name'], $this->beneficiaryData['needs']]);
    }

    public function undo() {
        $stmt = $this->db->prepare("DELETE FROM beneficiaries WHERE name = ?");
        $stmt->execute([$this->beneficiaryData['name']]);
    }
}
