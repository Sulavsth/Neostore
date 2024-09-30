<?php
require 'db.php'; // Database connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "DELETE FROM books WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    
    header("Location: inventory.php");
    exit();
}
?>
