<?php
require 'vendor/autoload.php';

// Connect to MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->orderDB;
$gridFS = $db->selectGridFSBucket();

if (!isset($_GET['file_id'])) {
    die("Invalid file ID.");
}

$fileID = new MongoDB\BSON\ObjectId($_GET['file_id']);
$fileStream = $gridFS->openDownloadStream($fileID);

$metaData = $db->fs->files->findOne(["_id" => $fileID]);

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . $metaData['filename'] . "\"");

fpassthru($fileStream);
?>
