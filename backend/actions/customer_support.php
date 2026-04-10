<?php
session_start();

// 🔒 Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../frontend/customer_login.html");
    exit();
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['customer_id'];
    $subject     = mysqli_real_escape_string($conn, $_POST['subject']);
    $message     = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO customer_support (customer_id, subject, message, status) 
            VALUES ('$customer_id', '$subject', '$message', 'Open')";

    header('Content-Type: application/json');
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'your application submitted succcessfully !']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . mysqli_error($conn)]);
    }
    exit();
}


mysqli_close($conn);
?>
