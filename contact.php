<?php
session_start();
require 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message_content = $_POST['message'];

    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute([$name, $email, $subject, $message_content])) {
        $message = "Your message has been sent successfully!";
    } else {
        $message = "There was an error sending your message. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - NeoStore</title>
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
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center">Contact Us</h2>
            <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
                <?php if ($message): ?>
                    <div class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3 mb-4" role="alert">
                        <p class="font-bold"><?php echo htmlspecialchars($message); ?></p>
                    </div>
                <?php endif; ?>
                <form action="contact.php" method="POST" class="p-6">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                        <input type="text" id="name" name="name" required class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" id="email" name="email" required class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Subject:</label>
                        <input type="text" id="subject" name="subject" required class="w-full p-2 border rounded">
                    </div>
                    <div class="mb-4">
                        <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                        <textarea id="message" name="message" required class="w-full p-2 border rounded" rows="5"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">Send Message</button>
                </form>
            </div>
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
