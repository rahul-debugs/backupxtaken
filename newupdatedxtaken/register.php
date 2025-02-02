<?php

$uname1 = $_POST['uname1'];
$email  = $_POST['email'];
$upswd1 = $_POST['upswd1'];
$upswd2 = $_POST['upswd2'];

// Check if all fields are populated
if (!empty($uname1) && !empty($email) && !empty($upswd1) && !empty($upswd2)) {

    // Debug the form data being received
    echo "Username: " . $uname1 . "<br>";
    echo "Email: " . $email . "<br>";
    echo "Password 1: " . $upswd1 . "<br>";
    echo "Password 2: " . $upswd2 . "<br>";

    // Database connection details
    $host = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "xtaken1";

    // Debug database connection
    echo "Connecting to database...<br>";
    $conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

    if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    } else {
        echo "Connected successfully!<br>";
    }

    $SELECT = "SELECT email FROM register WHERE email = ? LIMIT 1";
    $INSERT = "INSERT INTO register (uname1, email, upswd1, upswd2) VALUES (?, ?, ?, ?)";

    // Prepare statement for SELECT
    $stmt = $conn->prepare($SELECT);
    if ($stmt === false) {
        die('Error preparing SELECT statement: ' . $conn->error);  // This will show the actual error message
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->store_result();
    $rnum = $stmt->num_rows;

    // Checking if the email is already registered
    if ($rnum == 0) {
        $stmt->close();

        // Prepare statement for INSERT
        $stmt = $conn->prepare($INSERT);
        if ($stmt === false) {
            die('Error preparing INSERT statement: ' . $conn->error);  // This will show the actual error message
        }

        $stmt->bind_param("ssss", $uname1, $email, $upswd1, $upswd2);
        $stmt->execute();
        echo "New record inserted successfully";
    } else {
        echo "Someone already registered using this email";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "All fields are required";
    die();
}
?>