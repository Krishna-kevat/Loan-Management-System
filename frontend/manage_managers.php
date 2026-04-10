<?php
session_start();

// 🔒 Security check: Only Super Admin can access
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../backend/auth/admin_login.php");
    exit();
}

require_once '../backend/config.php';

// ✅ Handle actions
if (isset($_GET['action']) && isset($_GET['staff_id'])) {
    $staff_id = intval($_GET['staff_id']);

    if ($_GET['action'] === 'approve') {
        $sql = "UPDATE staff_registration 
                SET status='Approved' 
                WHERE staff_id=$staff_id AND role='Manager'";
    } elseif ($_GET['action'] === 'reject') {
        $sql = "UPDATE staff_registration 
                SET status='Rejected' 
                WHERE staff_id=$staff_id AND role='Manager'";
    } elseif ($_GET['action'] === 'delete') {
        $sql = "DELETE FROM staff_registration 
                WHERE staff_id=$staff_id AND role='Manager'";
    }

    if (isset($sql)) {
        if (!mysqli_query($conn, $sql)) {
            die("❌ Query Failed: " . mysqli_error($conn));
        }
    }

    header("Location: manage_managers.php");
    exit();
}


// ✅ Fetch only managers (No staff/loan officers)
$result = mysqli_query($conn, 
"SELECT * FROM staff_registration 
 WHERE role='Manager' 
 ORDER BY staff_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Managers | Purwase Admin</title>
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
        .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .action-btn {
            padding: 0.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
        }
        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #f59e0b; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .action-btn:hover { filter: brightness(1.1); }
    </style>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1400px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Manager Management</h2>
      <p style="color: var(--text-muted);">Review and authorize managerial accounts for system oversight.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Manager Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='6' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No Managers Found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusClass = "status-" . strtolower($row['status']);
                        echo "<tr>
                            <td style='font-weight: 600; color: var(--secondary);'>#{$row['staff_id']}</td>
                            <td style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                            <td style='color: var(--text-muted); font-size: 0.9rem;'>{$row['role']}</td>
                            <td>
                                <span class='badge $statusClass'>{$row['status']}</span>
                            </td>
                            <td>
                                <div style='display:flex; gap:0.5rem;'>";

                                if ($row['status'] === 'Pending') {
                                    echo "<a class='action-btn btn-approve' href='manage_managers.php?action=approve&staff_id={$row['staff_id']}'>Approve</a>";
                                    echo "<a class='action-btn btn-reject' href='manage_managers.php?action=reject&staff_id={$row['staff_id']}'>Reject</a>";
                                }

                                echo "<a class='action-btn btn-delete' href='manage_managers.php?action=delete&staff_id={$row['staff_id']}' onclick=\"return confirm('Delete this manager account?');\">Delete</a>";
                                
                        echo "</div></td></tr>";
                    }
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Purwase Company | Administrative Oversight</p>
  </footer>

  </main>
</div>

</body>
</html>

