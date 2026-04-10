<?php
session_start();

// 🔒 Security check
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../backend/auth/admin_login.php");
    exit();
}

require_once '../backend/config.php';
$username = $_SESSION['username'];

// --- ANALYTICS QUERIES ---

// 1. Total Disbursed Capital
$q_disbursed = mysqli_query($conn, "SELECT SUM(amount) as total FROM loan_application WHERE status='Approved'");
$res_disbursed = mysqli_fetch_assoc($q_disbursed);
$total_disbursed = $res_disbursed['total'] ?? 0;

// 2. Global Application Volume
$q_apps = mysqli_query($conn, "SELECT COUNT(*) as total FROM loan_application");
$res_apps = mysqli_fetch_assoc($q_apps);
$total_apps = $res_apps['total'] ?? 0;

// 3. Registered Customer Base
$q_customers = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer_registration");
$res_customers = mysqli_fetch_assoc($q_customers);
$total_customers = $res_customers['total'] ?? 0;

// 4. Active Staff Force (Approved only)
$q_staff = mysqli_query($conn, "SELECT COUNT(*) as total FROM staff_registration WHERE status='Approved'");
$res_staff = mysqli_fetch_assoc($q_staff);
$total_staff = $res_staff['total'] ?? 0;

// 5. Staff Role Distribution
$q_roles = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM staff_registration GROUP BY role");

// 6. Recent Applications
$q_recent = mysqli_query($conn, "SELECT la.*, c.fullname FROM loan_application la JOIN customer_registration c ON la.customer_id = c.customer_id ORDER BY la.applied_date DESC LIMIT 5");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Command Centre | Purwase</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">

  <div class="container" style="max-width: 1600px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Global Command Centre</h2>
      <p style="color: var(--text-muted);">Welcome back, <span style="color: var(--secondary); font-weight: 600;"><?php echo htmlspecialchars($username); ?></span>. Monitoring platform growth and financial integrity.</p>
    </div>

    <!-- Analytics Dashboard Grid -->
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="stat-card">
            <div class="stat-label">Total Disbursed</div>
            <div class="stat-value">₹<?php echo number_format($total_disbursed); ?></div>
            <div class="stat-desc" style="color: #4ade80;">Approved Loan Capital</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Lifetime Applications</div>
            <div class="stat-value"><?php echo number_format($total_apps); ?></div>
            <div class="stat-desc">Global Pipeline Volume</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Customer Base</div>
            <div class="stat-value"><?php echo number_format($total_customers); ?></div>
            <div class="stat-desc" style="color: var(--secondary);">Total Registrations</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Active Workforce</div>
            <div class="stat-value"><?php echo number_format($total_staff); ?></div>
            <div class="stat-desc" style="color: var(--accent);">Certified Staff Accounts</div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 2rem; margin-top: 3rem;">
        
        <!-- Staff Breakdown -->
        <div class="card">
            <h3 style="margin-bottom: 1.5rem;">Team Composition</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php while($role = mysqli_fetch_assoc($q_roles)): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                        <span style="font-weight: 600;"><?php echo $role['role']; ?>s</span>
                        <span class="badge" style="background: var(--primary); color: white;"><?php echo $role['count']; ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
            <div style="margin-top: 2rem;">
                <a href="admin_manage_staff.php" class="btn btn-sm" style="width: 100%; border: 1px solid var(--border-color); background: transparent;">Go to Staff Module</a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="table-container shadow-xl">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Recent Applications</h3>
                <a href="admin_view_report.php" style="font-size: 0.85rem; color: var(--secondary); text-decoration: none;">View All &rarr;</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Loan Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($app = mysqli_fetch_assoc($q_recent)): 
                        $statusClass = "status-" . strtolower(str_replace(' ', '', $app['status']));
                        if (strpos($statusClass, 'rejected') !== false) $statusClass = 'status-rejected';
                    ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($app['fullname']); ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date("d M Y", strtotime($app['applied_date'])); ?></div>
                            </td>
                            <td style="color: var(--secondary); font-weight: 600;"><?php echo $app['loan_type']; ?></td>
                            <td style="font-weight: 600;">₹<?php echo number_format($app['amount']); ?></td>
                            <td><span class="badge <?php echo $statusClass; ?>" style="font-size: 0.7rem;"><?php echo $app['status']; ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- System Status Footer -->
    <div class="card" style="margin-top: 3rem; background: rgba(255,255,255,0.02); border-style: dashed;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 2rem;">
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #4ade80; border-radius: 50%; margin-right: 5px;"></span>
                    Server Status: <span style="color: var(--text-main); font-weight: 600;">Optimal</span>
                </div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    DB Connection: <span style="color: var(--text-main); font-weight: 600;">Active</span>
                </div>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Last Refresh: <?php echo date("H:i:s"); ?>
            </div>
        </div>
    </div>

  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Purwase Company | Global Analytics Centre</p>
  </footer>

  </main>
</div>

</body>
</html>
