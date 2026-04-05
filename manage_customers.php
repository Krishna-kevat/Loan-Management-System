<?php
session_start();

// 🔒 Allow only Super Admin
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: super_admin_login.php");
    exit();
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "krishna_loan_management");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Fetch customers with their loan details (LEFT JOIN in case some have no loans yet)
$query = "SELECT c.customer_id, c.fullname, c.email, c.phone, c.address, 
                 la.Loan_id, la.loan_type, la.amount, la.tenure, la.purpose, la.status, la.applied_date
          FROM customer_registration c
          LEFT JOIN loan_application la ON c.customer_id = la.customer_id
          ORDER BY c.customer_id DESC, la.applied_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purwase Manage Customers</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7fa; margin:0; padding:20px; }
        .container { max-width:1200px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
        h2 { text-align:center; color:#2c3e50; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:10px; text-align:left; vertical-align:top; }
        th { background:#34495e; color:white; }
        tr:nth-child(even) { background:#f9f9f9; }
    </style>
</head>
<body>
<div class="container">
    <h2>Super Admin - Purwase Manage Customers & Loans</h2>

    <table>
        <tr>
            <th>Customer ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Loan ID</th>
            <th>Loan Type</th>
            <th>Amount</th>
            <th>Tenure</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Applied Date</th>
        </tr>

        <?php
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='12' style='text-align:center;'>No customers or loan records found</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['customer_id']}</td>
                    <td>".htmlspecialchars($row['fullname'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>".htmlspecialchars($row['phone'])."</td>
                    <td>".htmlspecialchars($row['address'])."</td>
                    <td>".($row['Loan_id'] ? $row['Loan_id'] : '-') ."</td>
                    <td>".($row['loan_type'] ? htmlspecialchars($row['loan_type']) : '-') ."</td>
                    <td>".($row['amount'] ? htmlspecialchars($row['amount']) : '-') ."</td>
                    <td>".($row['tenure'] ? htmlspecialchars($row['tenure']) : '-') ."</td>
                    <td>".($row['purpose'] ? htmlspecialchars($row['purpose']) : '-') ."</td>
                    <td>".($row['status'] ? htmlspecialchars($row['status']) : '-') ."</td>
                    <td>".($row['applied_date'] ? $row['applied_date'] : '-') ."</td>
                </tr>";
            }
        }
        mysqli_close($conn);
        ?>
    </table>
</div>
</body>
</html>
