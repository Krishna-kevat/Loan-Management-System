<?php
session_start();

// 🔒 Allow only Super Admin
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: super_admin_login.php");
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
    <title>Purwase Approved Loan Officers</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7fa; margin:0; padding:20px; }
        .container { max-width:1000px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
        h2 { text-align:center; color:#2c3e50; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:left; }
        th { background:#34495e; color:white; }
    </style>
</head>
<body>
<div class="container">
    <h2>Super Admin - Purwase Approved Loan Officers</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>

        <?php
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='4' style='text-align:center;'>No Approved Loan Officers Found</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['staff_id']}</td>
                    <td>".htmlspecialchars($row['fullname'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>{$row['status']}</td>
                </tr>";
            }
        }
        mysqli_close($conn);
        ?>
    </table>
</div>
</body>
</html>
