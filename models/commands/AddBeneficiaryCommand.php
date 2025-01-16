<?php
require_once 'Command.php';

class AddBeneficiaryCommand implements Command {
    private $db;
    private $beneficiaryData;
    private $lastInsertedId;

    public function __construct($db, $beneficiaryData) {
        $this->db = $db;
        $this->beneficiaryData = $beneficiaryData;
    }

    public function execute() {
        $stmt = $this->db->prepare("INSERT INTO beneficiaries (name, needs) VALUES (:name, :needs)");
        $stmt->execute([
            ':name' => $this->beneficiaryData['name'],
            ':needs' => $this->beneficiaryData['needs']
        ]);
        $this->lastInsertedId = $this->db->lastInsertId();
    }

    public function undo() {
        if ($this->lastInsertedId) {
            $stmt = $this->db->prepare("DELETE FROM beneficiaries WHERE id = :id");
            $stmt->execute([':id' => $this->lastInsertedId]);
        }
    }

    public function redo() {
        if (!empty($this->beneficiaryData)) {
            $stmt = $this->db->prepare("INSERT INTO beneficiaries (id, name, needs) VALUES (:id, :name, :needs)");
            $stmt->execute([
                ':id' => $this->lastInsertedId,
                ':name' => $this->beneficiaryData['name'],
                ':needs' => $this->beneficiaryData['needs']
            ]);
        }
    }
}
