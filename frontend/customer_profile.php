<?php
session_start();

// 🔒 Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.html");
    exit();
}

require_once '../backend/config.php';

// Fetch customer details
$customer_id = $_SESSION['customer_id'];

$sql = "SELECT fullname, email, phone, dob, gender, address, aadhaar, pan, income, employment 
        FROM customer_registration 
        WHERE customer_id = '$customer_id' LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("❌ Customer profile not found.");
}

$customer = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | Purwase</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/theme-switcher.js"></script>
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

  <div class="container" style="max-width: 900px; margin-top: 4rem; margin-bottom: 4rem;">
    <div class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin: 0;">My Profile</h2>
        <a href="customer_dashboard.php" class="btn btn-secondary">⬅ Back</a>
      </div>

      <div class="table-container">
        <table>
          <tbody>
            <tr>
              <th>Full Name</th>
              <td style="font-weight: 600; color: var(--secondary);"><?php echo htmlspecialchars($customer['fullname']); ?></td>
            </tr>
            <tr>
              <th>Email Address</th>
              <td><?php echo htmlspecialchars($customer['email']); ?></td>
            </tr>
            <tr>
              <th>Phone Number</th>
              <td><?php echo htmlspecialchars($customer['phone']); ?></td>
            </tr>
            <tr>
              <th>Date of Birth</th>
              <td><?php echo htmlspecialchars($customer['dob']); ?></td>
            </tr>
            <tr>
              <th>Gender</th>
              <td><?php echo htmlspecialchars($customer['gender']); ?></td>
            </tr>
            <tr>
              <th>Residential Address</th>
              <td><?php echo htmlspecialchars($customer['address']); ?></td>
            </tr>
            <tr>
              <th>Aadhaar Number</th>
              <td><?php echo htmlspecialchars($customer['aadhaar']); ?></td>
            </tr>
            <tr>
              <th>PAN Number</th>
              <td><?php echo htmlspecialchars($customer['pan']); ?></td>
            </tr>
            <tr>
              <th>Annual Income</th>
              <td style="font-weight: 600;">₹<?php echo number_format($customer['income']); ?></td>
            </tr>
            <tr>
              <th>Employment Type</th>
              <td style="text-transform: capitalize;"><?php echo htmlspecialchars($customer['employment']); ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div style="margin-top: 2rem; text-align: center;">
        <p style="color: var(--text-muted); font-size: 0.875rem;">To update your profile information, please contact our support team.</p>
        <a href="customer_support.html" class="btn btn-primary" style="margin-top: 1rem;">Contact Support</a>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 Purwase Company | Customer Profile</p>
  </footer>

</body>
</html>

