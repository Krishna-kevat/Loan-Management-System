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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Data Entry | Purwase Clerk</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .input-mini {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
            padding: 0.5rem;
            border-radius: 4px;
            width: 80px;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        .input-mini:focus {
            border-color: var(--secondary);
            outline: none;
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1400px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Loan Data Entry</h2>
      <p style="color: var(--text-muted);">Process interest rates for pending and approved loan applications.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Customer Details</th>
                    <th>Loan Details</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Financials</th>
                    <th>Interest Rate (%)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='8' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No Loan Applications Found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusClass = strtolower($row['status']);
                        echo "<tr>
                            <td style='font-weight: 600; color: var(--secondary);'>#{$row['loan_id']}</td>
                            <td>
                                <div style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted);'>".htmlspecialchars($row['email'])."</div>
                            </td>
                            <td>
                                <div style='font-weight: 600;'>{$row['loan_type']}</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted);'>₹".number_format($row['amount'], 2)." | {$row['tenure']} Mo</div>
                            </td>
                            <td style='max-width: 200px; font-size: 0.85rem; color: var(--text-muted);'>".htmlspecialchars($row['purpose'])."</td>
                            <td>
                                <span style='font-weight: 600; color: ".($statusClass == 'approved' ? '#4ade80' : 'var(--secondary)').";'>
                                    {$row['status']}
                                </span>
                            </td>
                            <td>";
                                if (!empty($row['interest_rate'])) {
                                    echo "<div style='color: var(--text-main);'>{$row['interest_rate']}%</div>
                                          <div style='font-size: 0.8rem; color: var(--text-muted);'>Int: ₹".number_format($row['total_interest'], 2)."</div>";
                                } else {
                                    echo "<span style='color: var(--text-muted);'>Not Set</span>";
                                }
                            echo "</td>
                            <td>
                                <form method='post' action='clerk_data_entry.php' style='display: flex; gap: 0.5rem; justify-content: center;'>
                                    <input type='hidden' name='loan_id' value='{$row['loan_id']}'>
                                    <input type='number' name='interest_rate' step='0.01' min='0' placeholder='Rate' required class='input-mini'>
                            </td>
                            <td>
                                    <button type='submit' class='btn btn-secondary' style='padding: 0.5rem 1rem; border-radius: 4px;'>Apply</button>
                                </form>
                            </td>
                        </tr>";
                    }
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 Purwase Company | Clerk Management Portal</p>
  </footer>

</body>
</html>

</div>
  </main>
</div>

</body>
</html>

