<?php
session_start();

// 🔒 Ensure Clerk is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Clerk') {
    header("Location: staff_login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "krishna_loan_management");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
    <title>Purwase Clerk - Customer Support Requests</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7fa; margin:0; padding:20px; }
        .container { max-width:1200px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#2c3e50; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:left; vertical-align: top; }
        th { background:#34495e; color:white; }
        
        /* ✅ Button Styling */
        a.action {
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            white-space: nowrap;
            display: inline-block;
            text-align: center;
            min-width: 90px;
        }
        a.progress { background: orange; color: white; }
        a.close { background: green; color: white; }
        a.delete { background: red; color: white; }
        a:hover { opacity:0.85; }

        /* ✅ Status Colors */
        .status-open { color: red; font-weight: bold; }
        .status-progress { color: orange; font-weight: bold; }
        .status-closed { color: green; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>Purwase Clerk - Customer Support Requests</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>

        <?php
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='8' style='text-align:center;'>No Support Requests Found</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                // ✅ Status color logic
                $status_class = "";
                if ($row['status'] === "Open") {
                    $status_class = "status-open";
                } elseif ($row['status'] === "In Progress") {
                    $status_class = "status-progress";
                } elseif ($row['status'] === "Closed") {
                    $status_class = "status-closed";
                }

                echo "<tr>
                    <td>{$row['support_id']}</td>
                    <td>".htmlspecialchars($row['fullname'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>".htmlspecialchars($row['subject'])."</td>
                    <td>".htmlspecialchars($row['message'])."</td>
                    <td class='$status_class'>{$row['status']}</td>
                    <td>{$row['created_at']}</td>
                    <td>
                        <div style='display:flex; gap:6px; flex-wrap:wrap;'>
                ";

                // ✅ Show actions based on status
                if ($row['status'] === 'Open') {
                    echo "<a class='action progress' href='clerk_manage_customerrequest.php?action=progress&support_id={$row['support_id']}'>In Progress</a>";
                }
                if ($row['status'] === 'Open' || $row['status'] === 'In Progress') {
                    echo "<a class='action close' href='clerk_manage_customerrequest.php?action=close&support_id={$row['support_id']}'>Close</a>";
                }

                // Always allow delete
                echo "<a class='action delete' href='clerk_manage_customerrequest.php?action=delete&support_id={$row['support_id']}' onclick=\"return confirm('Are you sure you want to delete this support request?');\">Delete</a>";

                echo "</div></td></tr>";
            }
        }
        mysqli_close($conn);
        ?>
    </table>
</div>
</body>
</html>
