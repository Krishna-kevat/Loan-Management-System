<?php
require_once '../config.php';

// Collect form data
$fullname   = $_POST['fullname'];
$email      = $_POST['email'];
$password   = $_POST['password'];
$phone      = $_POST['phone'];
$dob        = $_POST['dob'];
$address    = $_POST['address'];
$aadhaar    = $_POST['aadhaar'];
$pan        = $_POST['pan'];
$income     = $_POST['income'];
$employment = $_POST['employment'];


// 🔐 Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query
$sql = "INSERT INTO customer_registration 
(fullname, email, password, phone, dob, gender, address, aadhaar, pan, income, employment) 
VALUES 
('$fullname', '$email', '$hashed_password', '$phone', '$dob', '$gender', '$address', '$aadhaar', '$pan', '$income', '$employment')";
$r = mysqli_query($conn, $sql);
// Execute query
if ($r) {
    echo "<h2> Registration Successful!</h2>";
    echo "<p><a href=\"../../frontend/customer_login.html\">Click here to login</a></p>";
} else {
    echo " Error: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
