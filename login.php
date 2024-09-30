<?php
session_start();
require './db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "An error occurred during login. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NeoStore</title>
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
                    <a href="login.php" class="text-white hover:text-gray-300">Login</a>
                    <a href="register.php" class="text-white hover:text-gray-300">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-14 m-4 px-3 md:px-0">
        <div class="container mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center">Login</h2>
            <div class="max-w-md mx-auto bg-white shadow-md rounded-lg overflow-hidden">
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" class="p-6">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full p-3 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full p-3 border border-gray-300 rounded" required>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition duration-300">Login</button>
                </form>
                <p class="text-center mt-4 mb-6">Don't have an account? <a href="./register.php" class="text-indigo-600 hover:text-indigo-800">Register here</a></p>
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
