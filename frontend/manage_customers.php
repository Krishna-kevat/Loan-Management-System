<?php
session_start();

// 🔒 Allow only Super Admin
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../backend/auth/admin_login.php");
    exit();
}

require_once '../backend/config.php';

// 🔍 Defensive check for status column (prevents crash if migration not run)
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM customer_registration LIKE 'status'");
$has_status_col = (mysqli_num_rows($check_column) > 0);
$status_field = $has_status_col ? "c.status" : "'Active'";

// ✅ Fetch customers with their loan details
$query = "SELECT c.customer_id, c.fullname, c.email, c.phone, c.address, $status_field AS user_status,
                 la.Loan_id, la.loan_type, la.amount, la.tenure, la.purpose, la.status AS loan_status, la.applied_date
          FROM customer_registration c
          LEFT JOIN loan_application la ON c.customer_id = la.customer_id
          ORDER BY c.customer_id DESC, la.applied_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management | Purwase Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/theme-switcher.js"></script>
    <style>
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .status-submitted { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-review { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-none { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); }

        .user-active { color: #10b981; }
        .user-blocked { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .action-btn {
            padding: 0.35rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1600px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>User Oversight</h2>
      <p style="color: var(--text-muted);">View all registered customers and their associated loan transactions across the platform.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact & Address</th>
                    <th>Loan Profile</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Account Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No customers found.</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusClassRaw = strtolower(str_replace(' ', '', $row['status'] ?? 'none'));
                        if (strpos($statusClassRaw, 'rejected') !== false) $statusClassRaw = 'rejected';
                        if ($statusClassRaw === 'underreviewbymanager') $statusClassRaw = 'review';
                        $statusClass = "status-" . $statusClassRaw;
                        
                        echo "<tr>
                            <td>
                                <div style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</div>
                                <div style='font-size: 0.8rem; color: var(--secondary);'>ID: #{$row['customer_id']}</div>
                            </td>
                            <td>
                                <div style='font-size: 0.9rem;'>".htmlspecialchars($row['email'])."</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted); mb-1;'>".htmlspecialchars($row['phone'])."</div>
                                <div style='font-size: 0.75rem; color: var(--text-muted); max-width: 200px;'>".htmlspecialchars($row['address'])."</div>
                            </td>
                            <td>";
                                if ($row['Loan_id']) {
                                    echo "<div style='font-weight: 600;'>{$row['loan_type']}</div>
                                          <div style='font-size: 0.8rem; color: var(--text-muted);'>#{$row['Loan_id']}</div>";
                                } else {
                                    echo "<span style='color: var(--text-muted); font-style: italic;'>No loans</span>";
                                }
                        echo "</td>
                            <td>";
                                if ($row['amount']) {
                                    echo "<div style='font-weight: 600;'>₹".number_format($row['amount'])."</div>
                                          <div style='font-size: 0.8rem; color: var(--text-muted);'>{$row['tenure']} Months</div>";
                                } else {
                                    echo "-";
                                }
                        echo "</td>
                            <td style='max-width: 200px; font-size: 0.85rem; color: var(--text-muted);'>
                                ".($row['purpose'] ? htmlspecialchars($row['purpose']) : '-') ."
                            </td>
                            <td style='white-space: nowrap; color: var(--text-muted); font-size: 0.85rem;'>
                                " . ($row['applied_date'] ? date("d M Y", strtotime($row['applied_date'])) : "Joined Board") . "
                            </td>
                            <td>
                                <div style='display: flex; gap: 0.5rem; align-items: center;'>";
                                    $uStatus = $row['user_status'] ?? 'Active';
                                    if ($uStatus === 'Active') {
                                        echo "<span class='badge user-active'>Active</span>";
                                        echo "<a href='../backend/actions/admin_customer_actions.php?action=block&customer_id=".$row['customer_id']."' class='action-btn' style='background: rgba(239,68,68,0.1); color:#ef4444;'>Block</a>";
                                    } else {
                                        echo "<span class='badge user-blocked'>Blocked</span>";
                                        echo "<a href='../backend/actions/admin_customer_actions.php?action=unblock&customer_id=".$row['customer_id']."' class='action-btn' style='background: rgba(16,185,129,0.1); color:#10b981;'>Unblock</a>";
                                    }
                                    
                                    echo "<a href='../backend/actions/admin_customer_actions.php?action=delete&customer_id=".$row['customer_id']."' 
                                       class='action-btn' 
                                       style='background: rgba(255,255,255,0.05); color: var(--text-muted);' 
                                       onclick='return confirm(\"Are you sure? This will delete all loan history for this customer.\")'>
                                       Delete
                                    </a>
                                </div>
                            </td>
                        </tr>";
                    }
                }
                mysqli_close($conn);
                ?>

            </tbody>

        </table>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Purwase Company | Global Customer Management</p>
  </footer>

  </main>
</div>

</body>
</html>

