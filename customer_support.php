<?php
session_start();

// 🔒 Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.html");
    exit();
}

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "krishna_loan_management";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['customer_id'];
    $subject     = mysqli_real_escape_string($conn, $_POST['subject']);
    $message     = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO customer_support (customer_id, subject, message, status) 
            VALUES ('$customer_id', '$subject', '$message', 'Open')";

    if (mysqli_query($conn, $sql)) {
        // ✅ Redirect back with success message
        echo'your application submitted succcessfully !';
        exit();
    } else {
        // ❌ Redirect back with error
        header("Location: customer_support.html?error=" . urlencode(mysqli_error($conn)));
        exit();
    }
}

mysqli_close($conn);
?>
