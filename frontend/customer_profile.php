<?php
session_start();

// 🔒 Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.html");
    exit();
}

require_once '../backend/config.php';

// Fetch customer details
$customer_id = $_SESSION['customer_id'];

$sql = "SELECT fullname, email, phone, dob, gender, address, aadhaar, pan, income, employment 
        FROM customer_registration 
        WHERE customer_id = '$customer_id' LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("❌ Customer profile not found.");
}

$customer = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purwase My Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f7fa;
      color: #333;
    }

    header {
      background: #2c3e50;
      padding: 15px;
      color: white;
      text-align: center;
    }

    .container {
      max-width: 800px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2980b9;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th, table td {
      text-align: left;
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    table th {
      background: #2980b9;
      color: white;
      width: 30%;
    }

    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 15px;
      background: #2980b9;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .btn:hover {
      background: #1c5d8a;
    }
  </style>
</head>
<body>

<header>
  <h1>Purwase Customer Profile</h1>
</header>

<div class="container">
  <h2>Welcome, <?php echo htmlspecialchars($customer['fullname']); ?> 👋</h2>

  <table>
    <tr><th>Full Name</th><td><?php echo htmlspecialchars($customer['fullname']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($customer['email']); ?></td></tr>
    <tr><th>Phone</th><td><?php echo htmlspecialchars($customer['phone']); ?></td></tr>
    <tr><th>Date of Birth</th><td><?php echo htmlspecialchars($customer['dob']); ?></td></tr>
    <tr><th>Gender</th><td><?php echo htmlspecialchars($customer['gender']); ?></td></tr>
    <tr><th>Address</th><td><?php echo htmlspecialchars($customer['address']); ?></td></tr>
    <tr><th>Aadhaar</th><td><?php echo htmlspecialchars($customer['aadhaar']); ?></td></tr>
    <tr><th>PAN</th><td><?php echo htmlspecialchars($customer['pan']); ?></td></tr>
    <tr><th>Annual Income</th><td><?php echo htmlspecialchars($customer['income']); ?></td></tr>
    <tr><th>Employment Type</th><td><?php echo htmlspecialchars($customer['employment']); ?></td></tr>
  </table>

  <a href="customer_dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>
