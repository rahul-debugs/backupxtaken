<?php
require 'vendor/autoload.php';

header('Content-Type: application/json');

// Connect to MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->orderDB;
$collection = $db->orders;
$gridFS = $db->selectGridFSBucket();

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['file_id']) || !isset($data['order_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$fileID = new MongoDB\BSON\ObjectId($data['file_id']);
$orderID = new MongoDB\BSON\ObjectId($data['order_id']);

try {
    // Delete file from GridFS
    $gridFS->delete($fileID);
    
    // Remove order record
    $collection->deleteOne(["_id" => $orderID]);

    echo json_encode(["success" => true, "message" => "File deleted successfully."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error deleting file: " . $e->getMessage()]);
}
