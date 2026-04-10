<?php
session_start();
require_once '../config.php';

// 🔒 Ensure only Admin can access
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    die("Unauthorized access.");
}

$action = $_GET['action'] ?? '';
$customer_id = mysqli_real_escape_string($conn, $_GET['customer_id'] ?? '');

if (empty($customer_id)) {
    die("Invalid Customer ID.");
}

// 🛠️ Self-Healing DB: Ensure 'status' column exists before operations
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM customer_registration LIKE 'status'");
if (mysqli_num_rows($check_col) == 0) {
    mysqli_query($conn, "ALTER TABLE customer_registration ADD COLUMN status VARCHAR(20) DEFAULT 'Active'");
}

if ($action === 'block') {

    $sql = "UPDATE customer_registration SET status = 'Blocked' WHERE customer_id = '$customer_id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/manage_customers.php?msg=Customer blocked successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'unblock') {
    $sql = "UPDATE customer_registration SET status = 'Active' WHERE customer_id = '$customer_id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/manage_customers.php?msg=Customer unblocked successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'delete') {
    // Delete customer loans first (optional depending on FK constraints)
    mysqli_query($conn, "DELETE FROM loan_application WHERE customer_id = '$customer_id'");
    
    $sql = "DELETE FROM customer_registration WHERE customer_id = '$customer_id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/manage_customers.php?msg=Customer deleted successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
