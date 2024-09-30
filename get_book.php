<?php
require 'db.php';

$query = "SELECT * FROM books LIMIT 8";
$stmt = $pdo->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($books);
