<?php
require '/home/dhanush/backupxtaken/vendor/autoload.php'; // Adjust path if needed

$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->myDatabase;  // Replace 'myDatabase' with your actual database name
$collection = $database->users;  // Replace 'users' with your collection name

?>

