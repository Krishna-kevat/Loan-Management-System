<?php
session_start();

// 🔒 Only allow Loan Officer access
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Loan Officer') {
    die("<h2 style='text-align:center; color:red; margin-top: 4rem;'>Access Denied. Please login as Loan Officer.</h2>");
}

require_once '../backend/config.php';

// Handle actions
if (isset($_GET['action']) && isset($_GET['Loan_id'])) {
    $loan_id = intval($_GET['Loan_id']); 

    if ($_GET['action'] === 'send') {
        mysqli_query($conn, "UPDATE loan_application SET status='Under Review by Manager' WHERE Loan_id=$loan_id");
    } elseif ($_GET['action'] === 'reject') {
        mysqli_query($conn, "UPDATE loan_application SET status='Rejected by Officer' WHERE Loan_id=$loan_id");
    }

    header("Location: loan_officer_review.php");
    exit();
}

// Fetch submitted applications
$result = mysqli_query($conn, "SELECT la.*, c.fullname, c.email 
    FROM loan_application la 
    JOIN customer_registration c ON la.customer_id = c.customer_id 
    WHERE la.status='Submitted' 
    ORDER BY la.applied_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Applications | Purwase Loan Officer</title>
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
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
        }
        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #ef4444; color: white; }
        .action-btn:hover { filter: brightness(1.1); }
    </style>
    <script src="js/theme-switcher.js"></script>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">


  <div class="container" style="max-width: 1600px;">
    <div class="hero" style="padding: 2rem 0; text-align: left;">
      <h2>Review Loan Applications</h2>
      <p style="color: var(--text-muted);">Perform initial vetting on incoming loan applications before manager approval.</p>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Details</th>
                    <th>Loan Details</th>
                    <th>Purpose</th>
                    <th>Income</th>
                    <th>Documents</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='8' style='text-align:center; padding: 4rem; color: var(--text-muted);'>No applications pending review</td></tr>";
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
                            <td style='max-width: 200px; font-size: 0.85rem; color: var(--text-muted);'>".htmlspecialchars($row['purpose'])."</td>
                            <td style='font-weight: 600;'>₹".number_format($row['income'])."</td>
                            <td>";
                        
                        $documents = json_decode($row['document'], true);
                        if (is_array($documents)) {
                            foreach ($documents as $type => $doc) {
                                $docName = ucfirst($type);
                                // Robust path correction
                                $actualDoc = (strpos($doc, 'uploads/') === 0) ? '../' . $doc : $doc;
                                $actualDoc = str_replace('../../', '../', $actualDoc);
                                
                                echo "<a href='{$actualDoc}' target='_blank' class='doc-link'>📎 {$docName}</a>";
                            }
                        } else {
                            if (!empty($row['document'])) {
                                $docName = "View Doc";
                                $actualDoc = (strpos($row['document'], 'uploads/') === 0) ? '../' . $row['document'] : $row['document'];
                                $actualDoc = str_replace('../../', '../', $actualDoc);
                                
                                echo "<a href='{$actualDoc}' target='_blank' class='doc-link'>📎 {$docName}</a>";
                            } else {
                                echo "<span style='color:var(--text-muted);'>-</span>";
                            }
                        }


                        echo "</td>
                            <td style='white-space: nowrap; color: var(--text-muted); font-size: 0.85rem;'>
                                ".date("d M Y", strtotime($row['applied_date']))."
                            </td>
                            <td>
                                <div style='display:flex; gap:0.5rem;'>
                                    <a class='action-btn btn-approve' href='loan_officer_review.php?action=send&Loan_id={$row['Loan_id']}'>To Manager</a>
                                    <a class='action-btn btn-reject' href='loan_officer_review.php?action=reject&Loan_id={$row['Loan_id']}'>Reject</a>
                                </div>
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
    <p>&copy; 2025 Purwase Company | Loan Review Portal</p>
  </footer>

  </main>
</div>

</body>
</html>

