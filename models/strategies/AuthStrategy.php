<?php

interface AuthStrategy {
    public function authenticate($identifier, $password);
}
?>
