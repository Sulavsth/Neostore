<?php
session_start();
require './db.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Neo Store</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.4/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Your Cart</h1>
        <?php if (empty($cart)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table class="w-full mb-6">
                <thead>
                    <tr>
                        <th class="text-left">Title</th>
                        <th class="text-left">Price</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $book_id => $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td>₨ <?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" min="0" value="<?php echo $item['quantity']; ?>" 
                                       onchange="updateQuantity(<?php echo $book_id; ?>, this.value)"
                                       class="w-16 p-1 border rounded">
                            </td>
                            <td>₨ <?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <button onclick="updateQuantity(<?php echo $book_id; ?>, 0)"
                                        class="text-red-500 hover:text-red-700">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right font-bold">Total:</td>
                        <td class="font-bold">₨ <?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <a href="checkout.php" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Proceed to Checkout</a>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function updateQuantity(bookId, quantity) {
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                update_quantity: 1,
                book_id: bookId,
                quantity: quantity
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error updating cart. Please try again.');
            }
        });
    }
    </script>
</body>
</html>
