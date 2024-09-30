<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Neo Store</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.4/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="admin.php" class="text-white text-2xl font-bold">Neo Store Admin</a>
            <div>
                <a href="admin.php" class="text-white px-4">Dashboard</a>
                <a href="orders.php" class="text-white px-4">Orders</a>
                <a href="users.php" class="text-white px-4">Users</a>
                <a href="inventory.php" class="text-white px-4">Inventory</a>
                <a href="logout.php" class="text-white px-4">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Edit Book Form -->
    <section class="container mx-auto p-6 mt-10">
        <h2 class="text-3xl font-bold mb-6">Edit Book</h2>
        <?php
        require 'db.php'; // Database connection
        
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "SELECT * FROM books WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id]);
            $book = $stmt->fetch();
        }
        ?>
        <form action="update-book.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($book['id']); ?>">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 mb-2">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" class="w-full p-3 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="author" class="block text-gray-700 mb-2">Author:</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" class="w-full p-3 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 mb-2">Price:</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" class="w-full p-3 border border-gray-300 rounded" required>
            </div>
            <div class="mb-4">
                <label for="stock" class="block text-gray-700 mb-2">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" class="w-full p-3 border border-gray-300 rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Update Book</button>
        </form>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-600 p-4 text-white text-center">
        <p>&copy; 2024 Neo Store. All rights reserved.</p>
    </footer>

</body>
</html>
