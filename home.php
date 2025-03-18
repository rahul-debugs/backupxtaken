<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "Access denied! Redirecting to login...";
    header("Refresh:2; url=index.html");
    exit();
}

echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
echo "<a href='logout.php'>Logout</a>";
?>
