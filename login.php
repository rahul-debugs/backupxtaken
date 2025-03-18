<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['uname'];
    $password = $_POST['upswd'];

    // Find user in MongoDB
    $user = $collection->findOne(['username' => $username]);

    if ($user) {
        // Verify password
        if ($user['password'] === $password) { 
            $_SESSION['username'] = $username;
            echo "
                <html>
                <head>
                    <title>Loading...</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
                        }
                        .loader {
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            border: 8px solid rgba(255, 255, 255, 0.3);
                            border-top-color: white;
                            animation: spin 1s linear infinite;
                        }
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'home.html'; // Redirect to home page
                        }, 2500);
                    </script>
                </head>
                <body>
                    <div class='loader'></div>
                </body>
                </html>
            ";
            exit();
        } else {
            echo "<script>alert('❌ Incorrect password!'); window.location.href='index.html';</script>";
        }
    } else {
        echo "<script>alert('❌ User not found!'); window.location.href='index.html';</script>";
    }
}
?>
