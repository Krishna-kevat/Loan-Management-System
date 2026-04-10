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
  <title>Dashboard | Purwase</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/theme-switcher.js"></script>
</head>
<body>

  <header>
    <h1>Purwase</h1>
    <nav>
      <ul>
        <li><a href="customer_dashboard.php">Dashboard</a></li>
        <li><a href="apply_loan.html">Apply Loan</a></li>
        <li><a href="my_loans.php">My Loans</a></li>
        <li><a href="customer_profile.php">Profile</a></li>
        <li><a href="customer_support.html">Support</a></li>
        <li><a href="../backend/logout/customer_logout.php" style="color: var(--accent);">Logout</a></li>
        <li>
          <button class="theme-toggle" aria-label="Toggle Theme">
            <span class="sun">☀️</span>
            <span class="moon">🌙</span>
          </button>
        </li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Welcome back, <?php echo htmlspecialchars($fullname); ?> 👋</h2>
      <p style="color: var(--text-muted);">Manage your loans and account details securely.</p>
    </div>

    <div class="grid">
      <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Account Overview</h3>
        <div class="form-group">
          <label>Customer ID</label>
          <div style="font-size: 1.1rem; font-weight: 600;"><?php echo htmlspecialchars($customer_id); ?></div>
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <div style="font-size: 1.1rem; font-weight: 600;"><?php echo htmlspecialchars($email); ?></div>
        </div>
        <div class="form-group">
          <label>Last Login</label>
          <div style="color: var(--text-muted);"><?php echo date("d M Y, H:i:s"); ?></div>
        </div>
      </div>

      <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--secondary);">Quick Actions</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <a href="apply_loan.html" class="btn btn-primary">Apply for New Loan</a>
          <a href="my_loans.php" class="btn btn-secondary">Check Loan Status</a>
          <a href="customer_profile.php" class="btn btn-secondary">Update Profile</a>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 Purwase Company | Customer Dashboard</p>
  </footer>

</body>
</html>

