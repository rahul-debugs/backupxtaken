<?php
session_start();
require 'vendor/autoload.php'; // Ensure MongoDB PHP Library is installed

// MongoDB connection
try {
    $mongoClient = new MongoDB\Driver\Manager("mongodb://localhost:27017");
} catch (Exception $e) {
    die("MongoDB Connection Error: " . $e->getMessage());
}

$database = "orderDB";
$collection = "admins";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
        $query = new MongoDB\Driver\Query(["username" => $username]);
        $admins = $mongoClient->executeQuery("$database.$collection", $query)->toArray();

        if (!empty($admins)) {
            $admin = $admins[0];

            if (password_verify($password, $admin->password)) {
                session_regenerate_id(true); // Prevent session fixation attacks
                $_SESSION["admin_logged_in"] = true;
                $_SESSION["admin_username"] = $username;
                header("Location: admin.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f8f9fa; padding: 50px; }
        .login-box { width: 320px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background: #fff; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .error { color: red; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
