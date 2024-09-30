<?php
session_start();
require './db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Fetch order details from database
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $stmt = $pdo->prepare("SELECT oi.*, b.title FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $receipt_content = generateReceipt($order, $order_items);

        // Set headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="receipt_order_' . $order_id . '.txt"');
        header('Content-Length: ' . strlen($receipt_content));

        // Output receipt content
        echo $receipt_content;
        exit;
    }
}

function generateReceipt($order, $order_items) {
    $receipt = "Order #{$order['id']}\n\n";
    $receipt .= "Items:\n";
    foreach ($order_items as $item) {
        $receipt .= "{$item['title']} - Quantity: {$item['quantity']} - Price: ₨ " . number_format($item['price'] * $item['quantity'], 2) . "\n";
    }
    $receipt .= "\nTotal Amount: ₨ " . number_format($order['total_amount'], 2);
    return $receipt;
}
