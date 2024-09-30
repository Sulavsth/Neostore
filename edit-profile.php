<?php
session_start();
require './db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate input
    if (empty($name) || empty($email) || empty($phone)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (!preg_match("/^(98|97)\d{8}$/", $phone)) {
        $message = "Phone number must be 10 digits and start with 98 or 97.";
    } else {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $message = "Email is already taken.";
        } else {
            $updateFields = ["name" => $name, "email" => $email, "phone" => $phone];
            
            // Process image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['profile_image']['name'];
                $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array(strtolower($filetype), $allowed)) {
                    $message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                } else {
                    $newname = "../uploads" . uniqid() . "." . $filetype;
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $newname)) {
                        $updateFields["profile_image"] = $newname;
                    } else {
                        $message = "Failed to upload image.";
                    }
                }
            }
            
            if (empty($message)) {
                // Update user data
                $sql = "UPDATE users SET " . implode(" = ?, ", array_keys($updateFields)) . " = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge(array_values($updateFields), [$_SESSION['user_id']]));
                
                $message = "Profile updated successfully.";
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - NeoStore</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    .profile-image-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .profile-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .profile-image:hover {
        transform: scale(1.05);
    }
    </style>
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
                <a href="cart.php" class="text-white hover:text-gray-300">Cart</a>
                <a href="edit-profile.php" class="text-white hover:text-gray-300">Edit Profile</a>
                <a href="logout.php" class="text-white hover:text-gray-300">Logout</a>
            </div>
        </div>
    </div>
</nav>

<section class="py-14 m-4 px-3 md:px-0">
    <div class="container mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center">Edit Profile</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 max-w-2xl mx-auto">
        <?php if ($message): ?>
            <div class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3 mb-4" role="alert">
                <p class="font-bold"><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <div class="profile-image-container mb-4">
                        <img src="<?php echo $user['profile_image'] ? htmlspecialchars($user['profile_image']) : 'default-profile.png'; ?>" 
                             alt="Current Profile Picture" class="profile-image">
                    </div>
                    <label for="profile_image" class="block text-gray-700 text-sm font-bold mb-2">Update Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" class="w-full p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="w-full p-2 border rounded" required pattern="^(98|97)[0-9]{8}$">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Profile</button>
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
