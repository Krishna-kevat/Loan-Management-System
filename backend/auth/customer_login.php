<?php
session_start();

require_once '../config.php';

// Only run when form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Run query
    $sql = "SELECT * FROM customer_registration WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Store session
            $_SESSION['customer_id'] = $row['customer_id'] ?? null;
            $_SESSION['fullname']    = $row['fullname'];
            $_SESSION['email']       = $row['email'];

            // Redirect directly to dashboard
            header("Location: ../../frontend/customer_dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}

mysqli_close($conn);
?>