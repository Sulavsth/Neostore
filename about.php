<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - NeoStore</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
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

    <section class="py-14 m-4 px-3 md:px-0">
        <div class="container mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center">About NeoStore</h2>
            <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden p-6">
                <p class="mb-4">NeoStore is a modern bookshop dedicated to bringing the joy of reading to book lovers everywhere. Founded in 2023, we offer a curated selection of books across various genres, from bestsellers to hidden gems.</p>
                <p class="mb-4">Our mission is to create a welcoming space for readers to discover new worlds, gain knowledge, and find their next favorite book. We believe in the power of literature to inspire, educate, and entertain.</p>
                <p>At NeoStore, we're not just selling books; we're building a community of passionate readers. Join us in our love for the written word!</p>
            </div>
        </div>
    </section>
    <section class="bg-gray-100 text-white py-14">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl text-gray-800 font-bold mb-4">Ready to Start Your Reading Journey?</h2>
            <p class="mb-8 text-gray-800">Explore our collection and find your next favorite book today!</p>
            <a href="index.php" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition duration-300">Shop Now</a>
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
</body>
</html>
