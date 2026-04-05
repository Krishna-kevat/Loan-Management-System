<?php
session_start();

// 🔒 Only allow Manager access
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager') {
    die("<h2 style='text-align:center; color:red;'>Access Denied. Please login as Manager.</h2>");
}

require_once '../backend/config.php';

// Handle actions
if (isset($_GET['action']) && isset($_GET['Loan_id'])) {
    $loan_id = intval($_GET['Loan_id']);

    if ($_GET['action'] === 'approve') {
        // Approve loan
        mysqli_query($conn, "UPDATE loan_application SET status='Approved' WHERE Loan_id=$loan_id");
    } elseif ($_GET['action'] === 'reject') {
        // Reject loan
        mysqli_query($conn, "UPDATE loan_application SET status='Rejected by Manager' WHERE Loan_id=$loan_id");
    }

    header("Location: manager_loan_approval.php");
    exit();
}

// Fetch applications sent by Loan Officer
$result = mysqli_query($conn, "SELECT la.*, c.fullname, c.email 
    FROM loan_application la 
    JOIN customer_registration c ON la.customer_id = c.customer_id 
    WHERE la.status='Under Review by Manager' 
    ORDER BY la.applied_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purwase Manager - Approve Loans</title>
<style>
body { font-family: Arial, sans-serif; background: #eef2f7; margin:0; padding:20px; }
.container { max-width: 1200px; margin: auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2c3e50; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; margin-top:20px; font-size:14px; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; vertical-align:middle; }
th { background:#2c3e50; color:#fff; }
tr:nth-child(even) { background: #f9f9f9; }
a.action { padding:6px 12px; border-radius:4px; text-decoration:none; font-size:14px; display:inline-block; }
a.approve { background:green; color:white; }
a.reject { background:red; color:white; margin-left:5px; }
a.approve:hover { background:#0a5e0a; }
a.reject:hover { background:#a00000; }
.doc-links a { display:block; margin: 3px 0; color: #3498db; text-align:left; }
.doc-links a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
<h2>Purwase Manager - Loan Approval</h2>

<table>
<tr>
    <th>Loan ID</th>
    <th>Customer</th>
    <th>Email</th>
    <th>Loan Type</th>
    <th>Amount</th>
    <th>Tenure</th>
    <th>Purpose</th>
    <th>Income</th>
    <th>Documents</th>
    <th>Applied Date</th>
    <th>Action</th>
</tr>

<?php
if (mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='11' style='text-align:center; color:red;'>No applications pending approval</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['Loan_id']}</td>
            <td>".htmlspecialchars($row['fullname'])."</td>
            <td>".htmlspecialchars($row['email'])."</td>
            <td>".htmlspecialchars($row['loan_type'])."</td>
            <td>₹".number_format($row['amount'])."</td>
            <td>{$row['tenure']} months</td>
            <td>".htmlspecialchars($row['purpose'])."</td>
            <td>₹".number_format($row['income'])."</td>
            <td class='doc-links'>";

        // ✅ Handle multiple documents
        $documents = json_decode($row['document'], true);
        if (is_array($documents)) {
            foreach ($documents as $doc) {
                $docName = basename($doc);
                // remove prefix before first underscore if exists
                if (strpos($docName, "_") !== false) {
                    $docName = substr($docName, strpos($docName, "_") + 1);
                }
                echo "<a href='{$doc}' target='_blank'>📎 {$docName}</a>";
            }
        } else {
            if (!empty($row['document'])) {
                $docName = basename($row['document']);
                if (strpos($docName, "_") !== false) {
                    $docName = substr($docName, strpos($docName, "_") + 1);
                }
                echo "<a href='{$row['document']}' target='_blank'>📎 {$docName}</a>";
            } else {
                echo "<span style='color:gray;'>No Document</span>";
            }
        }

        echo "</td>
            <td>".date("d-m-Y", strtotime($row['applied_date']))."</td>
            <td>
                <div style='display:flex; gap:8px; flex-wrap:wrap;'>
                    <a class='action approve' href='manager_loan_approval.php?action=approve&Loan_id={$row['Loan_id']}'>Approve</a>
                    <a class='action reject' href='manager_loan_approval.php?action=reject&Loan_id={$row['Loan_id']}'>Reject</a>
                </div>
            </td>
        </tr>";
    }
}

mysqli_close($conn);
?>
</table>
</div>
</body>
</html>
