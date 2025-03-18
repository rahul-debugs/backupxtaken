<?php
session_start();
session_destroy();
header("Location: logins.php"); // Redirect back to login page
exit();
?>
