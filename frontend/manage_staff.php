<?php
session_start();

// 🔒 Only allow Manager access
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager') {
    die("<h2 style='text-align:center; color:red;'>Access Denied. Please login as Manager.</h2>");
}

require_once '../backend/config.php';

// Fetch pending staff
$result = mysqli_query($conn, "SELECT * FROM staff_registration WHERE status='Pending' ANd role != 'manager' ORDER BY joining_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Purwase Manage Staff Requests</title>
<style>
body { font-family: Arial; background: #f4f7fa; margin:0; }
.container { max-width: 1000px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #2c3e50; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
th { background: #34495e; color: #fff; }
a.approve { color: green; text-decoration: none; font-weight: bold; }
a.reject { color: red; text-decoration: none; font-weight: bold; }
a.approve:hover, a.reject:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
<h2>Purwase Pending Staff Requests</h2>

<table>
<tr>
<th>Staff ID</th>
<th>Full Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php
if(mysqli_num_rows($result) == 0){
    echo "<tr><td colspan='5' style='text-align:center;'>No pending staff requests</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>".htmlspecialchars($row['staff_id'])."</td>
            <td>".htmlspecialchars($row['fullname'])."</td>
            <td>".htmlspecialchars($row['email'])."</td>
            <td>".htmlspecialchars($row['role'])."</td>
            <td>
                <a class='approve' href='../backend/actions/approve_staff.php?staff_id=".$row['staff_id']."'>Approve</a> | 
                <a class='reject' href='../backend/actions/reject_staff.php?staff_id=".$row['staff_id']."'>Reject</a>
            </td>
        </tr>";
    }
}

// Close DB connection
mysqli_close($conn);
?>

</table>
</div>
</body>
</html>
