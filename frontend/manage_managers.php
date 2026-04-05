<?php
session_start();

// 🔒 Security check: Only Super Admin can access
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: admin_login.php");
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
    <title>Purwase Manage Managers</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7fa; margin:0; padding:20px; }
        .container { max-width:1200px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#2c3e50; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:left; }
        th { background:#34495e; color:white; }
        a.action { padding:6px 12px; border-radius:4px; text-decoration:none; font-size:14px; }
        a.approve { background:green; color:white; }
        a.reject { background:orange; color:white; }
        a.delete { background:red; color:white; }
        a:hover { opacity:0.8; }
    </style>
</head>
<body>
<div class="container">
    <h2>Super Admin - Purwase Manage Managers</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Role</th>
            <th>Action</th>
        </tr>

        <?php
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='6' style='text-align:center;'>No Managers Found</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['staff_id']}</td>
                    <td>".htmlspecialchars($row['fullname'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>{$row['status']}</td>
                    <td>{$row['role']}</td>
                    <td>";

                // Show buttons based on status
                if ($row['status'] === 'Pending') {
                    echo "<a class='action approve' href='manage_managers.php?action=approve&staff_id={$row['staff_id']}'>Approve</a> ";
                    echo "<a class='action reject' href='manage_managers.php?action=reject&staff_id={$row['staff_id']}'>Reject</a> ";
                }

                // Always allow delete
                echo "<a class='action delete' href='manage_managers.php?action=delete&staff_id={$row['staff_id']}' onclick=\"return confirm('Are you sure you want to delete this manager?');\">Delete</a>";

                echo "</td></tr>";
            }
        }
        mysqli_close($conn);
        ?>
    </table>
</div>
</body>
</html>
