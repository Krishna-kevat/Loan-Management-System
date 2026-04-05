<?php
session_start();

// 🔒 Ensure Clerk is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Clerk') {
    header("Location: staff_login.html");
    exit();
}

require_once '../backend/config.php';

// ✅ Handle interest rate update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['loan_id']) && isset($_POST['interest_rate'])) {
    $loan_id = intval($_POST['loan_id']);
    $interest_rate = floatval($_POST['interest_rate']);

    // 🔹 Fetch loan details
    $loan_query = mysqli_query($conn, "SELECT amount, tenure FROM loan_application WHERE loan_id=$loan_id");
    $loan = mysqli_fetch_assoc($loan_query);

    if ($loan) {
        $amount = $loan['amount'];
        $tenure = $loan['tenure'];

        // 🔹 Simple Interest Formula: (P × R × T) / 100
        // tenure is in months → convert to years = tenure/12
        $total_interest = ($amount * $interest_rate * ($tenure / 12)) / 100;

        // 🔹 Update DB with interest_rate & total_interest
        $sql = "UPDATE loan_application 
                SET interest_rate='$interest_rate', total_interest='$total_interest' 
                WHERE loan_id=$loan_id";

        if (!mysqli_query($conn, $sql)) {
            die("❌ Update Failed: " . mysqli_error($conn));
        }
    }

    header("Location: clerk_data_entry.php");
    exit();
}

// ✅ Fetch all submitted loan applications
$sql = "SELECT la.loan_id, la.customer_id, la.loan_type, la.amount, la.tenure, 
               la.purpose, la.status, la.interest_rate, la.total_interest, c.fullname, c.email 
        FROM loan_application la
        JOIN customer_registration c ON la.customer_id = c.customer_id
        WHERE la.status='Submitted' OR la.status='Approved'
        ORDER BY la.loan_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purwase Clerk - Loan Data Entry</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f7fa; margin:0; padding:20px; }
        .container { max-width:1200px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#2c3e50; }
        table { width:100%; border-collapse:collapse; margin-top:20px; font-size:14px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#34495e; color:white; }
        form { display:flex; justify-content:center; align-items:center; gap:6px; }
        input[type="number"] { width:80px; padding:4px; }
        button { padding:6px 12px; border:none; border-radius:4px; background:blue; color:white; cursor:pointer; }
        button:hover { opacity:0.8; }
    </style>
</head>
<body>
<div class="container">
    <h2>Purwase Clerk - Loan Data Entry</h2>

    <table>
        <tr>
            <th>Loan ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Loan Type</th>
            <th>Amount</th>
            <th>Tenure (Months)</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Interest Rate (%)</th>
            <th>Total Interest</th>
            <th>Action</th>
        </tr>

        <?php
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='11' style='text-align:center;'>No Loan Applications Found</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['loan_id']}</td>
                    <td>".htmlspecialchars($row['fullname'])."</td>
                    <td>".htmlspecialchars($row['email'])."</td>
                    <td>{$row['loan_type']}</td>
                    <td>₹ ".number_format($row['amount'], 2)."</td>
                    <td>{$row['tenure']}</td>
                    <td>{$row['purpose']}</td>
                    <td>{$row['status']}</td>
                    <td>".(!empty($row['interest_rate']) ? $row['interest_rate'].'%' : "-")."</td>
                    <td>".(!empty($row['total_interest']) ? "₹ ".number_format($row['total_interest'], 2) : "-")."</td>
                    <td>
                        <form method='post' action='clerk_data_entry.php'>
                            <input type='hidden' name='loan_id' value='{$row['loan_id']}'>
                            <input type='number' name='interest_rate' step='0.01' min='0' placeholder='Rate' required>
                            <button type='submit'>Apply</button>
                        </form>
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
