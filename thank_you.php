<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Neo Store</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
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

    <div class="flex-grow">
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">Thank You for Your Order!</h1>
            
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <p class="mb-4">Your order has been successfully placed. We appreciate your business!</p>
                
                <p class="mb-6">Your order will be processed and shipped soon. </p>
                
                <a href="index.php" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
                    Continue Shopping
                </a>
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
