<?php
class CommandManager {
    private $undoStack = [];
    private $redoStack = [];

    public function executeCommand(Command $command) {
        $command->execute();
        $this->undoStack[] = $command;
        $this->redoStack = []; // Clear redo stack after a new command
    }

    public function undo() {
        if (!empty($this->undoStack)) {
            $command = array_pop($this->undoStack);
            $command->undo();
            $this->redoStack[] = $command; // Add to redo stack
        }
    }

    public function redo() {
        if (!empty($this->redoStack)) {
            $command = array_pop($this->redoStack);
            $command->redo(); // Call the redo method
            $this->undoStack[] = $command; // Add back to undo stack
        }
    }
}
?>
