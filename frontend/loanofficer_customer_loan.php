<?php
session_start();

// 🔒 Allow only Loan Officer
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Loan Officer') {
    die("<h2 style='text-align:center; color:red; margin-top: 4rem;'>Access Denied. Please login as Loan Officer.</h2>");
}

require_once '../backend/config.php';

// Fetch loans approved by Manager
$result = mysqli_query($conn, "SELECT la.*, c.fullname, c.email 
    FROM loan_application la
    JOIN customer_registration c ON la.customer_id = c.customer_id
    WHERE la.status='Approved'
    ORDER BY la.applied_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Loans | Purwase Loan Officer</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
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

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1400px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Finalized Customer Loans</h2>
      <p style="color: var(--text-muted);">Review history of all loans approved by management.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Customer Details</th>
                    <th>Loan Details</th>
                    <th>Financials</th>
                    <th>Documents</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No approved loans found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td style='font-weight: 600; color: var(--secondary);'>#{$row['Loan_id']}</td>
                            <td>
                                <div style='font-weight: 600;'>".htmlspecialchars($row['fullname'])."</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted);'>".htmlspecialchars($row['email'])."</div>
                            </td>
                            <td>
                                <div style='font-weight: 600;'>".htmlspecialchars($row['loan_type'])."</div>
                                <div style='font-size: 0.8rem; color: var(--text-muted);'>₹".number_format($row['amount'])." | {$row['tenure']} Mo</div>
                            </td>
                            <td>
                                <div style='font-weight: 600;'>Income: ₹".number_format($row['income'])."</div>";
                                if (!empty($row['interest_rate'])) {
                                    echo "<div style='font-size: 0.8rem; color: var(--secondary);'>Rate: {$row['interest_rate']}%</div>";
                                }
                        echo "</td>
                            <td>";
                                
                                $docData = $row['document'];
                                if (!empty($docData)) {
                                    $documents = json_decode($docData, true);
                                    if (!is_array($documents)) {
                                        $documents = ['Document' => $docData];
                                    }

                                    foreach ($documents as $type => $path) {
                                        $docName = (is_numeric($type)) ? "View Doc" : ucfirst($type);
                                        // Standardize path: remove existing traversals and point to root /uploads/
                                        $cleanPath = str_replace(['../../', '../'], '', $path);
                                        $finalUrl = "../" . $cleanPath;
                                        
                                        echo "<a href='".htmlspecialchars($finalUrl)."' target='_blank' class='doc-link'>📎 ".htmlspecialchars($docName)."</a>";
                                    }
                                } else {
                                    echo "<span style='color:var(--text-muted);'>-</span>";
                                }
                                
                        echo "</td>
                            <td style='white-space: nowrap; color: var(--text-muted); font-size: 0.85rem;'>
                                ".date("d M Y", strtotime($row['applied_date']))."
                            </td>
                            <td>
                                <span style='font-weight: 600; color: #4ade80;'>{$row['status']}</span>
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
    <p>&copy; 2025 Purwase Company | Loan Monitoring Portal</p>
  </footer>

  </main>
</div>

</body>
</html>

