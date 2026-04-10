<?php
require_once '../backend/config.php';

// Add status column to customer_registration
$sql = "ALTER TABLE customer_registration ADD COLUMN status VARCHAR(20) DEFAULT 'Active'";

if (mysqli_query($conn, $sql)) {
    echo "Migration Successful: 'status' column added to customer_registration.";
} else {
    echo "Migration Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
