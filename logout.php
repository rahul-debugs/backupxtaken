<?php
session_start();
session_destroy();
echo "Logging out... Redirecting to login.";
header("Refresh:2; url=index.html");
?>
