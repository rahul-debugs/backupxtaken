<?php
$new_password = "print123"; // Change to your new password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
echo "New hashed password: " . $hashed_password;
?>
