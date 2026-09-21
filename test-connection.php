<?php
require 'config/database.php';

try {
    $pdo = getConnection();
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}