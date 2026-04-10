<?php
session_start();

// 🔒 Only allow Manager access
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager') {
    die("<h2 style='text-align:center; color:var(--accent); margin-top: 4rem;'>Access Denied. Please login as Manager.</h2>");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Reports | Purwase Manager</title>
    <link rel="stylesheet" href="css/style.css">
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
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">

  <div class="container" style="max-width: 1600px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Comprehensive Loan Reports</h2>
      <p style="color: var(--text-muted);">View all loan applications, their current status, and financial details.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Customer Details</th>
                    <th>Loan Details</th>
                    <th>Financials</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No loan applications found</td></tr>";
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusClassRaw = strtolower(str_replace(' ', '', $row['status']));
                        if (strpos($statusClassRaw, 'rejected') !== false) $statusClassRaw = 'rejected';
                        if ($statusClassRaw === 'underreviewbymanager') $statusClassRaw = 'review';
                        $statusClass = "status-" . $statusClassRaw;
                        
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
                                <div style='font-weight: 600;'>Inc: ₹".number_format($row['income'])."</div>";
                                if (!empty($row['interest_rate'])) {
                                    echo "<div style='font-size: 0.8rem; color: var(--secondary);'>Rate: {$row['interest_rate']}%</div>";
                                }
                        echo "</td>
                            <td>
                                <span class='badge $statusClass'>{$row['status']}</span>
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
    <p>&copy; 2025 Purwase Company | Managerial Reporting Portal</p>
  </footer>

  </main>
</div>

</body>
</html>

