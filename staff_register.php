<?php
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "krishna_loan_management";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// Collect form data
$fullname     = $_POST['fullname'];
$email        = $_POST['email'];
$password     = $_POST['password'];
$phone        = $_POST['phone'];
$dob          = $_POST['dob'];
$gender       = $_POST['gender'];
$address      = $_POST['address'];
$staff_id     = $_POST['staff_id'];
$role         = $_POST['role'];
$salary       = $_POST['salary'];
$joining_date = $_POST['joining_date'];

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert as Pending
$sql = "INSERT INTO staff_registration 
(fullname, email, password, phone, dob, gender, address, staff_id, role, salary, joining_date, status) 
VALUES 
('$fullname', '$email', '$hashed_password', '$phone', '$dob', '$gender', '$address', '$staff_id', '$role', '$salary', '$joining_date', 'Pending')";

if (mysqli_query($conn, $sql)) {

    echo "<h2>Registration Submitted Successfully!</h2>";

    if ($role == "Manager") {
        echo "<p>Your registration is pending approval by the Admin.</p>";
    } else {
        echo "<p>Your registration is pending approval by the Manager.</p>";
    }

} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
