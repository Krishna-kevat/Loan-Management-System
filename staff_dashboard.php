<?php
session_start();

// 🔒 Redirect if not logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.html");
    exit();
}

// Fetch staff details from session
$staff_id = $_SESSION['staff_id'];
$fullname = $_SESSION['fullname'];
$email    = $_SESSION['email'];
$role     = $_SESSION['role']; // saved during login
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purwase Staff Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f7fa;
      color: #333;
    }

    header {
      background: #34495e;
      padding: 15px 20px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 1.5rem;
    }

    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 15px;
    }

    nav ul li {
      display: inline;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 5px;
      transition: background 0.3s;
    }

    nav ul li a:hover {
      background: #2c3e50;
    }

    .container {
      padding: 20px;
    }

    .welcome {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
      text-align: center;
    }

    .user-details {
      margin-top: 20px;
      padding: 15px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }

    footer {
      background: #34495e;
      color: #fff;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<header>
  <h1>Purwase Staff Dashboard</h1>
  <nav>
    <ul>
      <li><a href="staff_dashboard.php">Dashboard</a></li>

      <?php if ($role === 'Manager') { ?>
        <li><a href="backend/manager/manage_staff.php">Manage Staff</a></li>
        <li><a href="manager_loan_approval.php">Approve Loans</a></li>
        <li><a href="manager_reports.php">View Reports</a></li>
        <li><a href="manager_logout.php">Logout</a></li>
      <?php } ?>

      <?php if ($role === 'Loan Officer') { ?>
        <li><a href="loan_officer_review.php">Review Applications</a></li>
        <li><a href="loanofficer_customer_loan.php">Customer Loans</a></li>
        <li><a href="loanofficer_logout.php">Logout</a></li>
      <?php } ?>

      <?php if ($role === 'Clerk') { ?>
        <li><a href="clerk_data_entry.php">Data Entry</a></li>
        <li><a href="clerk_manage_customerrequest.php">Customer Support</a></li>
        <li><a href="clerk_logout.php">Logout</a></li>
      <?php } ?>

      
    </ul>
  </nav>
</header>

<div class="container">
  <div class="welcome">
    <h2>Welcome, <?php echo htmlspecialchars($fullname); ?> 👋</h2>
    <p>Role: <strong><?php echo htmlspecialchars($role); ?></strong></p>
  </div>

  <div class="user-details">
    <h3>Your Staff Details</h3>
    <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($staff_id); ?></p>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($fullname); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
    <p><strong>Last Login:</strong> <?php echo date("d-m-Y H:i:s"); ?></p>
  </div>
</div>

<footer>
  <p>&copy; 2025 Purwase Company | Staff Dashboard</p>
</footer>

</body>
</html>
