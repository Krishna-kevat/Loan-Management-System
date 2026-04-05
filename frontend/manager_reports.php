<?php
session_start();

// 🔒 Only allow Manager access
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager') {
    die("<h2 style='text-align:center; color:red;'>Access Denied. Please login as Manager.</h2>");
}

require_once '../backend/config.php';

// Fetch all loan applications
$result = mysqli_query($conn, "SELECT la.*, c.fullname, c.email 
    FROM loan_application la 
    JOIN customer_registration c ON la.customer_id = c.customer_id 
    ORDER BY la.applied_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purwase Manager Loan Report</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f7fa; margin:0; padding:20px; }
.container { max-width: 1300px; margin: auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#2c3e50; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; margin-top:20px; font-size:14px; }
th, td { border:1px solid #ccc; padding:10px; text-align:center; vertical-align:middle; }
th { background:#34495e; color:#fff; }
tr:nth-child(even) { background: #f9f9f9; }

.status-approved { color: green; font-weight:bold; }
.status-rejected { color: red; font-weight:bold; }
.status-pending { color: orange; font-weight:bold; }
.status-review { color: blue; font-weight:bold; }

.doc-links a { display:block; margin: 3px 0; color: #3498db; text-align:left; }
.doc-links a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
<h2>📊 Purwase Loan Applications Report</h2>

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
    <th>Status</th>
    <th>Applied Date</th>
</tr>

<?php
if (mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='11' style='text-align:center;'>No loan applications found</td></tr>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        // status color coding
        $status_class = '';
        if ($row['status'] === 'Approved') {
            $status_class = 'status-approved';
        } elseif (strpos($row['status'], 'Rejected') !== false) {
            $status_class = 'status-rejected';
        } elseif ($row['status'] === 'Submitted') {
            $status_class = 'status-pending';
        } elseif ($row['status'] === 'Under Review by Manager') {
            $status_class = 'status-review';
        }

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
            <td class='{$status_class}'>".htmlspecialchars($row['status'])."</td>
            <td>".date("d-m-Y", strtotime($row['applied_date']))."</td>
        </tr>";
    }
}

mysqli_close($conn);
?>
</table>
</div>
</body>
</html>
