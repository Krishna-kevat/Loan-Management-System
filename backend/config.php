<?php
// backend/config.php

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "krishna_loan_management";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
