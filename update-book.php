<?php
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $query = "UPDATE books SET title = ?, author = ?, price = ?, stock = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$title, $author, $price, $stock, $id]);
    
    header("Location: inventory.php");
    exit();
}
?>
