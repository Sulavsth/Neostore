<?php
session_start();
require './db.php';

// Function to get cart items from database
function getCartFromDB($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cart[$row['book_id']] = [
            'title' => $row['title'],
            'price' => $row['price'],
            'quantity' => $row['quantity']
        ];
    }
    return $cart;
}

// Load cart from database if user is logged in
if (isset($_SESSION['user_id'])) {
    $_SESSION['cart'] = getCartFromDB($pdo, $_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $user_id = $_SESSION['user_id'] ?? 0;
        $book_id = $_POST['book_id'];
        $quantity = $_POST['quantity'];

        if ($quantity > 0) {
            // Update database
            $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE user_id = ? AND book_id = ?");
            $stmt->execute([$quantity, $user_id, $book_id]);

            // Update session
            $_SESSION['cart'][$book_id]['quantity'] = $quantity;
        } else {
            // Remove from database
            $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ? AND book_id = ?");
            $stmt->execute([$user_id, $book_id]);

            // Remove from session
            unset($_SESSION['cart'][$book_id]);
        }

        echo json_encode(['success' => true]);
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - NeoStore</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <nav class="bg-gray-900 p-4">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <div class="-mx-4">
                    <a href="index.php" class="text-white text-2xl font-bold px-4">NeoStore</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-white hover:text-gray-300">Home</a>
                    <a href="./contact.php" class="text-white hover:text-gray-300">Contact</a>
                    <a href="./about.php" class="text-white hover:text-gray-300">About</a>
                    <a href="cart.php" class="text-white hover:text-gray-300">Cart</a>
                    <a href="edit-profile.php" class="text-white hover:text-gray-300">Edit Profile</a>
                    <a href="logout.php" class="text-white hover:text-gray-300">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-14 m-4 px-3 md:px-0 flex-grow">
        <div class="container mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center">Your Cart</h2>
            <?php if (empty($cart)): ?>
                <p class="text-center text-gray-600">Your cart is empty.</p>
            <?php else: ?>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Title</th>
                                <th class="py-3 px-6 text-left">Price</th>
                                <th class="py-3 px-6 text-left">Quantity</th>
                                <th class="py-3 px-6 text-left">Subtotal</th>
                                <th class="py-3 px-6 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            <?php foreach ($cart as $book_id => $item): ?>
                                <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td class="py-3 px-6 text-left">₨ <?php echo number_format($item['price'], 2); ?></td>
                                    <td class="py-3 px-6 text-left">
                                        <input type="number" min="0" value="<?php echo $item['quantity']; ?>" 
                                               onchange="updateQuantity(<?php echo $book_id; ?>, this.value)"
                                               class="w-16 p-1 border rounded">
                                    </td>
                                    <td class="py-3 px-6 text-left">₨ <?php echo number_format($subtotal, 2); ?></td>
                                    <td class="py-3 px-6 text-left">
                                        <button onclick="updateQuantity(<?php echo $book_id; ?>, 0)"
                                                class="text-red-500 hover:text-red-700">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-100">
                                <td colspan="3" class="py-3 px-6 text-right">Total:</td>
                                <td class="py-3 px-6">₨ <?php echo number_format($total, 2); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-6 text-center">
                    <a href="checkout.php" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="bg-gray-800 text-white py-7">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-3 m-4 md:mb-0">
                    <h3 class="text-lg md:text-xl font-semibold">NeoStore</h3>
                    <p class="text-sm text-gray-400">Your gateway to endless stories.</p>
                </div>
                <ul class="flex m-4 space-x-3">
                    <li><a href="index.php" class="text-sm text-gray-400 hover:text-white">Home</a></li>
                    <li><a href="./contact.php" class="text-sm text-gray-400 hover:text-white">Contact</a></li>
                    <li><a href="./about.php" class="text-sm text-gray-400 hover:text-white">About</a></li>
                </ul>
            </div>
            <div class="mt-6 md:mt-8 m-4 border-t border-gray-700 pt-3">
                <p class="text-xs md:text-sm text-gray-400">&copy; 2024 NeoStore. All rights reserved.</p>
            </div>
        </div>
    </footer>

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
