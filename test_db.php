<?php
require 'config.php';
require 'classes/Database.php';

try {
    $db = new Database();
    echo "Database Connected Successfully!";
} catch(Exception $e) {
    echo "Connection Failed: " . $e->getMessage();
}
