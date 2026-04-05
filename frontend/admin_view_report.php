<?php
session_start();

// 🔒 Ensure only Admin can access
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: admin_login.php");
    exit();
}

require_once '../backend/config.php';

// Fetch all customers with their loan applications
$sql = "SELECT c.customer_id, c.fullname, c.email, c.phone, 
               la.loan_id, la.loan_type, la.amount, la.tenure, la.status, la.total_interest, la.applied_date
        FROM customer_registration c
        LEFT JOIN loan_application la ON c.customer_id = la.customer_id
        ORDER BY c.customer_id ASC, la.applied_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purwase Admin - Customer Loan Report</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f7fa; padding:20px; }
.container { max-width: 1200px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2c3e50; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { border:1px solid #ccc; padding:10px; text-align:left; }
th { background:#34495e; color:white; }
tr:hover { background:#f1f1f1; }
.status-Pending { color: orange; font-weight:bold; }
.status-Approved { color: green; font-weight:bold; }
.status-Rejected { color: red; font-weight:bold; }
</style>
</head>
<body>
<div class="container">
<h2>📊 Purwase Admin - Customer Loan Report</h2>

<table>
<tr>
    <th>Customer ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Loan ID</th>
    <th>Loan Type</th>
    <th>Amount</th>
    <th>Tenure (Months)</th>
    <th>Status</th>
    <th>Total Interest</th>
    <th>Applied Date</th>
</tr>

<?php
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='11' style='text-align:center;'>No customer or loan data found.</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['customer_id']}</td>
            <td>".htmlspecialchars($row['fullname'])."</td>
            <td>".htmlspecialchars($row['email'])."</td>
            <td>".htmlspecialchars($row['phone'])."</td>
            <td>".($row['loan_id'] ?? '-') ."</td>
            <td>".($row['loan_type'] ?? '-') ."</td>
            <td>".($row['amount'] ? '₹'.number_format($row['amount']) : '-') ."</td>
            <td>".($row['tenure'] ?? '-') ."</td>
            <td class='status-{$row['status']}'>{$row['status']}</td>
            <td>".($row['total_interest'] ? '₹'.number_format($row['total_interest'],2) : '-') ."</td>
            <td>".($row['applied_date'] ? date("d-m-Y", strtotime($row['applied_date'])) : '-') ."</td>
        </tr>";
    }
}
mysqli_close($conn);
?>
</table>
</div>
</body>
</html>
