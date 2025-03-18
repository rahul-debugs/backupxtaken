<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: logins.php");
    exit();
}

$mongoClient = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$database = "orderDB";
$collection = "orders";

// Fetch all orders
$query = new MongoDB\Driver\Query([]);
$orders = $mongoClient->executeQuery("$database.$collection", $query)->toArray();

// Handle order update or deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["order_id"]) && isset($data["printed"])) {
        try {
            $bulkWrite = new MongoDB\Driver\BulkWrite();
            $bulkWrite->update(
                ["_id" => new MongoDB\BSON\ObjectId($data["order_id"])],
                ['$set' => ["printed" => (bool)$data["printed"]]]
            );

            $mongoClient->executeBulkWrite("$database.$collection", $bulkWrite);

            // Send message to user (Implement messaging function if needed)
            $message = "Your order has been printed. Total cost: ₹" . ($data["cost"] ?? "N/A");
            // sendMessageToUser($data["phone"], $message); // Implement this function

            echo json_encode(["success" => true, "message" => "Order marked as printed."]);
        } catch (Exception $e) {
            error_log("Error updating order: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Failed to update order."]);
        }
        exit();
    }

    if (isset($data["order_id"]) && isset($data["delete"])) {
        try {
            $bulkWrite = new MongoDB\Driver\BulkWrite();
            $bulkWrite->delete(["_id" => new MongoDB\BSON\ObjectId($data["order_id"])]);
            $mongoClient->executeBulkWrite("$database.$collection", $bulkWrite);

            echo json_encode(["success" => true, "message" => "Order deleted successfully."]);
        } catch (Exception $e) {
            error_log("Error deleting order: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Failed to delete order."]);
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { width: 80%; background: #fff; padding: 20px; border-radius: 10px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #28a745; color: white; }
        button { padding: 5px 10px; background: red; color: white; border: none; cursor: pointer; }
        button:hover { background: darkred; }
        .logout { float: right; padding: 10px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; }
        .logout:hover { background: darkred; }
        .checkbox { cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Panel</h2>
    <a href="logout.php" style="text-decoration: none;">
        <button>Logout</button>
    </a>
    <table>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Payment Method</th>
            <th>Pages</th>
            <th>Copies</th>
            <th>Cost</th>
            <th>PDF</th>
            <th>Printed</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr id="row_<?php echo $order->_id; ?>">
                <td><?php echo $order->name; ?></td>
                <td><?php echo $order->address; ?></td>
                <td><?php echo $order->phone; ?></td>
                <td><?php echo $order->paymentMethod; ?></td>
                <td><?php echo $order->numPages; ?></td>
                <td><?php echo $order->numCopies; ?></td>
                <td>₹<?php echo $order->cost ?? "N/A"; ?></td>
                <td><a href="<?php echo $order->pdfFilePath; ?>" target="_blank">Download</a></td>
                <td>
                    <input type="checkbox" class="checkbox" 
                        onchange="markPrinted('<?php echo $order->_id; ?>', '<?php echo $order->phone; ?>', <?php echo $order->cost ?? 0; ?>)"
                        <?php echo isset($order->printed) && $order->printed ? 'checked' : ''; ?>>
                </td>
                <td>
                    <button onclick="deleteOrder('<?php echo $order->_id; ?>', 'row_<?php echo $order->_id; ?>')">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
function deleteOrder(orderID, rowId) {
    if (confirm("Are you sure you want to delete this order?")) {
        fetch("admin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order_id: orderID, delete: true })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(rowId).remove();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error deleting order.");
        });
    }
}

function markPrinted(orderID, phone, cost) {
    if (confirm("Mark this order as printed?")) {
        fetch("admin.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order_id: orderID, printed: true, phone: phone, cost: cost })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error updating order status.");
        });
    }
}
</script>

</body>
</html>
