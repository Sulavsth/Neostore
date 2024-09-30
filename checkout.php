<?php
session_start();
require './db.php';

$conn = getDbConnection();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($address)) $errors[] = "Address is required.";

    if (empty($errors)) {
        // Calculate total
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Save order to database
        $order_sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("ssssd", $name, $email, $phone, $address, $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Save order items
        $item_sql = "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($item_sql);
        foreach ($cart as $book_id => $item) {
            $stmt->bind_param("iiid", $order_id, $book_id, $item['quantity'], $item['price']);
            $stmt->execute();
        }

        // Clear the cart from the session
        unset($_SESSION['cart']);

        // Clear the cart from the database for logged-in users
        if (isset($_SESSION['user_id'])) {
            $clear_cart_sql = "DELETE FROM carts WHERE user_id = ?";
            $stmt = $conn->prepare($clear_cart_sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
        }

        // Redirect to a thank you page
        header('Location: thank_you.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Neo Store</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
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
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="cart.php" class="text-white hover:text-gray-300">Cart</a>
                        <a href="edit-profile.php" class="text-white hover:text-gray-300">Edit Profile</a>
                        <a href="logout.php" class="text-white hover:text-gray-300">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-white hover:text-gray-300">Login</a>
                        <a href="register.php" class="text-white hover:text-gray-300">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                <table class="w-full mb-6">
                    <thead>
                        <tr>
                            <th class="text-left">Title</th>
                            <th class="text-left">Price</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $book_id => $item): ?>
                            <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td>₨ <?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₨ <?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right font-bold">Total:</td>
                            <td class="font-bold">₨ <?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                <form action="checkout.php" method="POST">
                    <div class="mb-4">
                        <label for="name" class="block mb-2">Full Name</label>
                        <input type="text" id="name" name="name" required
                               class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block mb-2">Contact Number</label>
                        <input type="tel" id="phone" name="phone" required
                               class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="address" class="block mb-2">Shipping Address</label>
                        <textarea id="address" name="address" required
                                  class="w-full p-2 border rounded"></textarea>
                    </div>
                    <button type="submit" 
                            class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
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
</body>
</html>
