<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['uname'];
    $password = $_POST['upswd']; // Change to password_hash() later for security

    // Check if username already exists
    $existingUser = $collection->findOne(['username' => $username]);
    
    if ($existingUser) {
        echo "❌ Username already exists!";
    } else {
        // Insert new user into MongoDB
        $collection->insertOne(['username' => $username, 'password' => $password]);
        echo "✅ Registration successful! Redirecting to login...";
        header("Refresh:2; url=index.html");
        exit();
    }
}
?>
