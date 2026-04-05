<?php
// Database connection
$servername = "localhost"; 
$username   = "root";       
$password   = "";           
$dbname = "krishna_loan_management"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
    echo "<p><a href='customer_login.php'>Click here to login</a></p>";
} else {
    echo " Error: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
