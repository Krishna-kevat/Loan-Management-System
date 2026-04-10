<?php
session_start();

// 🔒 Ensure only Admin can access
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: ../backend/auth/admin_login.php");
    exit();
}

require_once '../backend/config.php';

// Fetch all customers with their loan applications
$sql = "SELECT c.customer_id, c.fullname, c.email, c.phone, 
               la.loan_id, la.loan_type, la.amount, la.tenure, la.status, la.total_interest, la.applied_date, la.document
        FROM customer_registration c
        LEFT JOIN loan_application la ON c.customer_id = la.customer_id
        ORDER BY c.customer_id ASC, la.applied_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Loan Report | Purwase Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/theme-switcher.js"></script>
    <style>
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .status-submitted { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-review { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-none { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); }

        .doc-link {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.4rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            text-decoration: none;
            color: var(--secondary);
            font-size: 0.75rem;
            margin-right: 0.3rem;
            margin-bottom: 0.3rem;
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1600px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Global Financial Report</h2>
      <p style="color: var(--text-muted);">Consolidated view of all customer registrations and their historical loan applications.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact Info</th>
                    <th>Loan History</th>
                    <th>Amount & Tenure</th>
                    <th>Interest</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No customer or loan data found.</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusClassRaw = strtolower(str_replace(' ', '', $row['status'] ?? 'none'));
                        if (strpos($statusClassRaw, 'rejected') !== false) $statusClassRaw = 'rejected';
                        if ($statusClassRaw === 'underreviewbymanager') $statusClassRaw = 'review';
                        $statusClass = "status-" . $statusClassRaw;
                        
                        echo "<tr>
                            <td>
                                <div style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</div>
                                <div style='font-size: 0.8rem; color: var(--secondary);'>ID: #{$row['customer_id']}</div>
                            </td>
                            <td>
                                <div style='font-size: 0.9rem;'>".htmlspecialchars($row['email'])."</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted);'>".htmlspecialchars($row['phone'])."</div>
                            </td>
                            <td>";
                                if ($row['loan_id']) {
                                    echo "<div style='font-weight: 600;'>{$row['loan_type']}</div>
                                          <div style='font-size: 0.8rem; color: var(--text-muted);'>#{$row['loan_id']}</div>";
                                } else {
                                    echo "<span style='color: var(--text-muted); font-style: italic;'>No loans</span>";
                                }
                        echo "</td>
                            <td>";
                                if ($row['amount']) {
                                    echo "<div style='font-weight: 600;'>₹".number_format($row['amount'])."</div>
                                          <div style='font-size: 0.8rem; color: var(--text-muted);'>{$row['tenure']} Months</div>";
                                } else {
                                    echo "-";
                                }
                        echo "</td>
                            <td>";
                                if ($row['total_interest']) {
                                    echo "<div style='font-weight: 600; color: var(--accent);'>₹".number_format($row['total_interest'], 2)."</div>";
                                } else {
                                    echo "-";
                                }
                        echo "</td>
                            <td>
                                <span class='badge $statusClass'>".($row['status'] ?? 'No Application')."</span>
                            </td>
                            <td>";
                                $docData = $row['document'];
                                if (!empty($docData)) {
                                    $documents = json_decode($docData, true);
                                    if (!is_array($documents)) {
                                        $documents = ['Document' => $docData];
                                    }
                                    foreach ($documents as $type => $path) {
                                        $docName = (is_numeric($type)) ? "View Doc" : ucfirst($type);
                                        $cleanPath = str_replace(['../../', '../'], '', $path);
                                        $finalUrl = "../" . $cleanPath;
                                        echo "<a href='".htmlspecialchars($finalUrl)."' target='_blank' class='doc-link'>📎 ".htmlspecialchars($docName)."</a>";
                                    }
                                } else {
                                    echo "-";
                                }
                        echo "</td>
                            <td style='white-space: nowrap; color: var(--text-muted); font-size: 0.85rem;'>";
                                if ($row['applied_date']) {
                                    echo date("d M Y", strtotime($row['applied_date']));
                                } else {
                                    echo "-";
                                }
                        echo "</td>
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
    <p>&copy; <?php echo date("Y"); ?> Purwase Company | Global Reporting Centre</p>
  </footer>

  </main>
</div>

</body>
</html>

