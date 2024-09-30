<?php
// register-process.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "neo_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@.*\.com$/', $email)) {
    die("Invalid email format. Email must contain '@' and end with '.com'");
}

// Validate phone number
if (!preg_match('/^\d{10}$/', $phone)) {
    die("Invalid phone number. Must be exactly 10 digits.");
}

// Validate password
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $password)) {
    die("Invalid password. Must be at least 8 characters long, include 1 uppercase letter, 1 number, and 1 special character.");
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check for email and phone number duplication
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
$stmt->bind_param("ss", $email, $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Error: Email or phone number already exists.";
    exit();
}

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

if ($stmt->execute()) {
    echo "Registration successful!";
    // Redirect to login page or user profile
    // header("Location: login.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>