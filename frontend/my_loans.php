<?php
session_start();

// 🔒 Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.html");
    exit();
}

require_once '../backend/config.php';

$customer_id = $_SESSION['customer_id'];

// Fetch loan applications of this customer
$sql = "SELECT loan_id, loan_type, amount, tenure, purpose, income, status, applied_date, document, interest_rate, total_interest
        FROM loan_application 
        WHERE customer_id = '$customer_id' 
        ORDER BY applied_date DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("❌ Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purwase My Loan Applications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #34495e;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        th {
            background: #34495e;
            color: #fff;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        a {
            text-decoration: none;
            color: #3498db;
        }
        .doc-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .doc-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<h2>📑 Purwase My Loan Applications</h2>

<?php if (mysqli_num_rows($result) > 0) { ?>
    <table>
        <tr>
            <th>Loan ID</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Tenure</th>
            <th>Purpose</th>
            <th>Income</th>
            <th>Status</th>
            <th>Interest Rate (%)</th>
            <th>Total Interest</th>
            <th>Applied Date</th>
            <th>Documents</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['loan_id']; ?></td>
            <td><?php echo htmlspecialchars($row['loan_type']); ?></td>
            <td>₹<?php echo number_format($row['amount']); ?></td>
            <td><?php echo $row['tenure']; ?> months</td>
            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
            <td>₹<?php echo number_format($row['income']); ?></td>
            <td class="status-<?php echo strtolower($row['status']); ?>">
                <?php echo $row['status']; ?>
            </td>
            <td><?php echo !empty($row['interest_rate']) ? $row['interest_rate'].'%' : '-'; ?></td>
            <td><?php echo !empty($row['total_interest']) ? '₹ '.number_format($row['total_interest'], 2) : '-'; ?></td>
            <td><?php echo date("d-m-Y", strtotime($row['applied_date'])); ?></td>
            <td>
                <?php 
                    $docs = json_decode($row['document'], true);
                    if ($docs && is_array($docs)) {
                        echo "<ul class='doc-list'>";
                        foreach ($docs as $docName => $docPath) {
                            echo "<li><a href='" . htmlspecialchars($docPath) . "' target='_blank'>📎 " . ucfirst($docName) . "</a></li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "-";
                    }
                ?>
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <p style="text-align:center; color:red;">❌ You have not applied for any loans yet.</p>
<?php } ?>

</body>
</html>

<?php mysqli_close($conn); ?>
