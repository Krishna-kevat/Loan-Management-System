<?php
session_start();

// 🔒 Ensure Clerk is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Clerk') {
    header("Location: staff_login.html");
    exit();
}

require_once '../backend/config.php';

// ✅ Handle status updates
if (isset($_GET['action']) && isset($_GET['support_id'])) {
    $support_id = intval($_GET['support_id']);

    if ($_GET['action'] === 'progress') {
        $sql = "UPDATE customer_support SET status='In Progress' WHERE support_id=$support_id";
    } elseif ($_GET['action'] === 'close') {
        $sql = "UPDATE customer_support SET status='Closed' WHERE support_id=$support_id";
    } elseif ($_GET['action'] === 'delete') {
        $sql = "DELETE FROM customer_support WHERE support_id=$support_id";
    }

    if (isset($sql)) {
        if (!mysqli_query($conn, $sql)) {
            die("❌ Query Failed: " . mysqli_error($conn));
        }
    }

    header("Location: clerk_manage_customerrequest.php");
    exit();
}

// ✅ Fetch all support tickets with customer details
$sql = "SELECT cs.support_id, cs.customer_id, cs.subject, cs.message, cs.status, cs.created_at, 
               c.fullname, c.email 
        FROM customer_support cs
        JOIN customer_registration c ON cs.customer_id = c.customer_id
        ORDER BY cs.support_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Support Requests | Purwase Clerk</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-open { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .status-progress { background: rgba(158, 158, 11, 0.1); color: #f59e0b; }
        .status-closed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        
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
        .btn-progress { background: #f59e0b; color: white; }
        .btn-close { background: #10b981; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .action-btn:hover { filter: brightness(1.1); }
    </style>
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1400px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Customer Support Requests</h2>
      <p style="color: var(--text-muted);">Manage and respond to customer inquiries and support tickets.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Details</th>
                    <th>Enquiry Details</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='6' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No Support Requests Found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusClassRaw = strtolower(str_replace(' ', '', $row['status']));
                        $statusClass = "status-" . $statusClassRaw;
                        echo "<tr>
                            <td style='font-weight: 600; color: var(--secondary);'>#{$row['support_id']}</td>
                            <td>
                                <div style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted);'>".htmlspecialchars($row['email'])."</div>
                            </td>
                            <td>
                                <div style='font-weight: 600; margin-bottom: 0.25rem;'>".htmlspecialchars($row['subject'])."</div>
                                <div style='font-size: 0.85rem; color: var(--text-muted); max-width: 400px;'>".htmlspecialchars($row['message'])."</div>
                            </td>
                            <td>
                                <span class='badge $statusClass'>{$row['status']}</span>
                            </td>
                            <td style='white-space: nowrap; color: var(--text-muted); font-size: 0.85rem;'>
                                ".date("d M Y, H:i", strtotime($row['created_at']))."
                            </td>
                            <td>
                                <div style='display:flex; gap:0.5rem;'>";
                                
                                if ($row['status'] === 'Open') {
                                    echo "<a class='action-btn btn-progress' href='clerk_manage_customerrequest.php?action=progress&support_id={$row['support_id']}'>Work</a>";
                                }
                                if ($row['status'] === 'Open' || $row['status'] === 'In Progress') {
                                    echo "<a class='action-btn btn-close' href='clerk_manage_customerrequest.php?action=close&support_id={$row['support_id']}'>Resolve</a>";
                                }
                                
                                echo "<a class='action-btn btn-delete' href='clerk_manage_customerrequest.php?action=delete&support_id={$row['support_id']}' onclick=\"return confirm('Delete this request?');\">Delete</a>";
                                
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
    <p>&copy; 2025 Purwase Company | Support Management Portal</p>
  </footer>

  </main>
</div>

</body>
</html>

