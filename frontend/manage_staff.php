<?php
session_start();

// 🔒 Only allow Manager access
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager') {
    die("<h2 style='text-align:center; color:var(--accent); margin-top: 4rem;'>Access Denied. Please login as Manager.</h2>");
}

require_once '../backend/config.php';

// Fetch pending staff
$result = mysqli_query($conn, "SELECT * FROM staff_registration WHERE status='Pending' AND role != 'Manager' ORDER BY joining_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff | Purwase Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 90px;
        }
        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #ef4444; color: white; }
        .action-btn:hover { filter: brightness(1.1); }
    </style>
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1200px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Pending Staff Authorization</h2>
      <p style="color: var(--text-muted);">Review and authorize new staff registrations (Clerks & Loan Officers).</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($result) == 0){
                    echo "<tr><td colspan='5' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No pending staff requests found.</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td style='font-weight: 600; color: var(--secondary);'>#".htmlspecialchars($row['staff_id'])."</td>
                            <td style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                            <td style='color: var(--text-muted);'>".htmlspecialchars($row['role'])."</td>
                            <td>
                                <div style='display:flex; gap:0.5rem;'>
                                    <a class='action-btn btn-approve' href='../backend/actions/approve_staff.php?staff_id=".$row['staff_id']."'>Approve</a>
                                    <a class='action-btn btn-reject' href='../backend/actions/reject_staff.php?staff_id=".$row['staff_id']."'>Reject</a>
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
    <p>&copy; 2025 Purwase Company | Staff Management Portal</p>
  </footer>

  </main>
</div>

</body>
</html>

