<?php
session_start();

// 🔒 Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: customer_login.html");
    exit();
}

// Fetch user details from session
$fullname = $_SESSION['fullname'];
$email    = $_SESSION['email'];
$customer_id = $_SESSION['customer_id'] ?? "N/A";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f7fa;
      color: #333;
    }

    header {
      background: #2c3e50;
      padding: 15px 20px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      font-size: 1.5rem;
      margin: 0;
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
      transition: background 0.3s ease;
    }

    nav ul li a:hover {
      background: #1c5d8a;
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

    .welcome h2 {
      margin-top: 0;
      color: #2c3e50;
    }

    .user-details {
      margin-top: 20px;
      text-align: left;
      background: #fdfdfd;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }

    .user-details p {
      margin: 8px 0;
      font-size: 15px;
    }

    footer {
      background: #2c3e50;
      color: #fff;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <header>
    <h1>Purwase - User Dashboard</h1>
    <nav>
      <ul>
        <li><a href="apply_loan.html">Apply Loan</a></li>
        <li><a href="my_loans.php">My Loan Status</a></li>
        <li><a href="customer_profile.php">Profile</a></li>
        <li><a href="customer_support.html">Support</a></li>
        <li><a href="customer_logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <div class="welcome">
      <h2>Welcome, <?php echo htmlspecialchars($fullname); ?> 👋</h2>
      <p>Here you can manage your loans, check status, update profile, and more.</p>
    </div>

    <div class="user-details">
      <h3>Your Account Details</h3>
      <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customer_id); ?></p>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($fullname); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
      <p><strong>Last Login:</strong> <?php echo date("d-m-Y H:i:s"); ?></p>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 Purwase Company | User Dashboard</p>
  </footer>

</body>
</html>
