<?php
session_start();

// 🔒 Allow only Super Admin
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../backend/auth/admin_login.php");
    exit();
}

require_once '../backend/config.php';

// ✅ Fetch only Approved Loan Officers
$result = mysqli_query($conn, "SELECT * FROM staff_registration WHERE role='Loan Officer' AND status='Approved' ORDER BY staff_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Officers | Purwase Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1200px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Approved Loan Officers</h2>
      <p style="color: var(--text-muted);">View all authorized loan officers who are currently active in the system.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Officer Name</th>
                    <th>Email Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='4' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No Approved Loan Officers Found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td style='font-weight: 600; color: var(--secondary);'>#{$row['staff_id']}</td>
                            <td style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                            <td>
                                <span style='font-weight: 600; color: #4ade80;'>{$row['status']}</span>
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
    <p>&copy; <?php echo date("Y"); ?> Purwase Company | Administrative Oversight</p>
  </footer>

  </main>
</div>

</body>
</html>

