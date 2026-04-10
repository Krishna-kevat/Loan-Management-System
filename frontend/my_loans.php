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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Loan Status | Purwase</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/theme-switcher.js"></script>
    <style>
        .status-pending { color: var(--secondary); font-weight: 600; }
        .status-approved { color: #4ade80; font-weight: 600; }
        .status-rejected { color: var(--accent); font-weight: 600; }
        .doc-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            text-decoration: none;
            color: var(--secondary);
            font-size: 0.8rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }
        .doc-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>

  <header>
    <h1>Purwase</h1>
    <nav>
      <ul>
        <li><a href="customer_dashboard.php">Dashboard</a></li>
        <li><a href="apply_loan.html">Apply Loan</a></li>
        <li><a href="my_loans.php">My Loans</a></li>
        <li><a href="customer_profile.php">Profile</a></li>
        <li><a href="customer_support.html">Support</a></li>
        <li><a href="../backend/logout/customer_logout.php" style="color: var(--accent);">Logout</a></li>
        <li>
          <button class="theme-toggle" aria-label="Toggle Theme">
            <span class="sun">☀️</span>
            <span class="moon">🌙</span>
          </button>
        </li>
      </ul>
    </nav>
  </header>

  <div class="container" style="max-width: 1200px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>My Loan Applications</h2>
      <p style="color: var(--text-muted);">Track the status of your current and past loan applications.</p>
    </div>

    <?php if (mysqli_num_rows($result) > 0) { ?>
        <div class="table-container shadow-xl">
            <table>
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Tenure</th>
                        <th>Interest</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Documents</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--secondary);"><?php echo $row['loan_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['loan_type']); ?></td>
                        <td style="font-weight: 600;">₹<?php echo number_format($row['amount']); ?></td>
                        <td><?php echo $row['tenure']; ?> months</td>
                        <td>
                            <?php if (!empty($row['interest_rate'])) { ?>
                                <div style="color: var(--text-main);"><?php echo $row['interest_rate']; ?>%</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Int: ₹<?php echo number_format($row['total_interest'], 2); ?></div>
                            <?php } else { echo "-"; } ?>
                        </td>
                        <td style="color: var(--text-muted);"><?php echo date("d M Y", strtotime($row['applied_date'])); ?></td>
                        <td>
                            <span class="status-<?php echo strtolower($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                $docs = json_decode($row['document'], true);
                                if ($docs && is_array($docs)) {
                                    foreach ($docs as $docName => $docPath) {
                                        // Robust path correction
                                        $actualDoc = (strpos($docPath, 'uploads/') === 0) ? '../' . $docPath : $docPath;
                                        $actualDoc = str_replace('../../', '../', $actualDoc);
                                        
                                        echo "<a href='" . htmlspecialchars($actualDoc) . "' target='_blank' class='doc-link'>📎 " . ucfirst($docName) . "</a>";
                                    }
                                } else {
                                    echo "-";
                                }
                            ?>

                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="card" style="text-align: center; padding: 4rem;">
            <p style="color: var(--accent); font-size: 1.25rem;">❌ You have not applied for any loans yet.</p>
            <a href="apply_loan.html" class="btn btn-primary" style="margin-top: 2rem;">Apply Now</a>
        </div>
    <?php } ?>
  </div>

  <footer>
    <p>&copy; 2025 Purwase Company | Customer Dashboard</p>
  </footer>

</body>
</html>

<?php mysqli_close($conn); ?>

