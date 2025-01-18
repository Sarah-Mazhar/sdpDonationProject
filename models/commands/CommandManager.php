<?php
class CommandManager {
    private $undoStack = [];
    private $redoStack = [];

    public function executeCommand(Command $command) {
        $command->execute();
        $this->undoStack[] = $command;
        $this->redoStack = [];
    }

    public function undo() {
        if (!empty($this->undoStack)) {
            $command = array_pop($this->undoStack);
            $command->undo();
            $this->redoStack[] = $command;
        }
    }

    public function redo() {
        if (!empty($this->redoStack)) {
            $command = array_pop($this->redoStack);
            $command->redo();
            $this->undoStack[] = $command;
        }
    }
}
?>
