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

require_once '../backend/config.php';

// --- Analytics Calculations ---

// 📊 General Stats
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM customer_registration"))['count'];
$total_applications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM loan_application"))['count'];

// 📈 Role-Specific Stats
$pending_count = 0;
$stat_label = "";
$financial_stat = null;

if ($role === 'Manager') {
    // Pending Approvals (Status: Review)
    $pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM loan_application WHERE status='Review'"))['count'];
    $stat_label = "Pending Approvals";
    
    // Total Disbursed (Approved)
    $financial_stat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM loan_application WHERE status='Approved'"))['total'] ?? 0;
} elseif ($role === 'Loan Officer') {
    // Pending Reviews (Status: Submitted)
    $pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM loan_application WHERE status='Submitted'"))['count'];
    $stat_label = "Pending Reviews";
} elseif ($role === 'Clerk') {
    // Open Support Tickets
    $pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM customer_support WHERE status='Open'"))['count'];
    $stat_label = "Open Tickets";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard | Purwase</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


<div class="container">
  <div class="hero" style="padding: 2rem 0; text-align: left;">
    <h2>Staff Dashboard</h2>
    <p style="color: var(--text-muted);">Welcome back, <?php echo htmlspecialchars($fullname); ?>. Role: <span style="color: var(--secondary); font-weight: 600;"><?php echo htmlspecialchars($role); ?></span></p>
  </div>

  <!-- Analytics Section -->
  <div class="analytics-grid">
    <div class="stat-card">
      <span class="label">Total Customers</span>
      <span class="value"><?php echo number_format($total_customers); ?></span>
      <span class="trend" style="color: #4ade80;">👥 Active accounts</span>
    </div>
    
    <div class="stat-card">
      <span class="label">Total Applications</span>
      <span class="value"><?php echo number_format($total_applications); ?></span>
      <span class="trend" style="color: var(--secondary);">📑 Lifetime processed</span>
    </div>

    <div class="stat-card">
      <span class="label"><?php echo $stat_label; ?></span>
      <span class="value"><?php echo number_format($pending_count); ?></span>
      <span class="trend" style="color: var(--accent);">⏳ Action required</span>
    </div>

    <?php if ($financial_stat !== null): ?>
    <div class="stat-card">
      <span class="label">Total Disbursed</span>
      <span class="value">₹<?php echo number_format($financial_stat); ?></span>
      <span class="trend" style="color: #4ade80;">💰 Approved loans</span>
    </div>
    <?php endif; ?>
  </div>

  <!-- Main Content Grid -->
  <div class="grid">
    <!-- Profile Card -->
    <div class="card">
      <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Your Profile Details</h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="form-group">
          <label>Staff ID</label>
          <div style="font-size: 1.1rem; font-weight: 600;"><?php echo htmlspecialchars($staff_id); ?></div>
        </div>
        <div class="form-group">
          <label>Role</label>
          <div style="font-size: 1.1rem; font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($role); ?></div>
        </div>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <div style="font-size: 1.1rem; font-weight: 600;"><?php echo htmlspecialchars($email); ?></div>
      </div>
      <div class="form-group" style="margin-bottom: 0;">
        <label>Last Login</label>
        <div style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date("d M Y, H:i:s"); ?></div>
      </div>
    </div>

    <!-- Shortcuts Card -->
    <div class="card">
      <h3 style="margin-bottom: 1.5rem; color: var(--secondary);">Quick Actions</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Frequently used tools for your <?php echo strtolower($role); ?> role.</p>
      <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
        <?php if ($role === 'Manager'): ?>
          <a href="manager_loan_approval.php" class="btn btn-primary">Proceed to Loan Approvals</a>
          <a href="manager_reports.php" class="btn btn-secondary">Access Financial Reports</a>

        <?php elseif ($role === 'Loan Officer'): ?>
          <a href="loan_officer_review.php" class="btn btn-primary">Start Reviewing Applications</a>
          <a href="loanofficer_customer_loan.php" class="btn btn-secondary">View Customer Loan History</a>
        <?php elseif ($role === 'Clerk'): ?>
          <a href="clerk_data_entry.php" class="btn btn-primary">Interest Data Entry</a>
          <a href="clerk_manage_customerrequest.php" class="btn btn-secondary">Manage Customer Support</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<footer>
  <p>&copy; <?php echo date("Y"); ?> Purwase Company | Corporate Staff Portal</p>
</footer>


  </main>
</div>

</body>
</html>


