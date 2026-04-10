<?php
session_start();

// Database connection
require_once '../config.php';

$login_error = ""; // store error message locally

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM staff_registration WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    // Handle rejected or blocked accounts
    if ($row['status'] === 'Rejected' || $row['status'] === 'Blocked') {
        $login_error = "Your account is " . strtolower($row['status']) . ". Please contact your supervisor.";
    }
    // Handle pending accounts
    elseif ($row['status'] === 'Pending') {
        if ($row['role'] === 'Manager') {
            $login_error = "Your account is not approved yet. Please wait for Admin approval.";
        } else {
            $login_error = "Your account is not approved yet. Please wait for Manager approval.";
        }
    }
    // Handle approved accounts with correct password
    elseif ($row['status'] === 'Approved' && password_verify($password, $row['password'])) {
        $_SESSION['staff_id']  = $row['staff_id']; 
        $_SESSION['fullname']  = $row['fullname'];
        $_SESSION['email']     = $row['email'];
        $_SESSION['role']      = $row['role'];

        header("Location: ../../frontend/staff_dashboard.php");
        exit();
    } 
    else {
        $login_error = "Incorrect password! Please try again.";
    }
} else {
    $login_error = "No staff account found with this email.";
}

}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Purwase</title>
    <link rel="stylesheet" href="../../frontend/css/style.css">
    <script src="../../frontend/js/theme-switcher.js"></script>
</head>
<body class="auth-page" style="min-height: 100vh; display: flex; flex-direction: column;">

  <header style="justify-content: flex-end; padding: 1rem 2rem; background: transparent; border: none; backdrop-filter: none;">
    <button class="theme-toggle" aria-label="Toggle Theme">
      <span class="sun">☀️</span>
      <span class="moon">🌙</span>
    </button>
  </header>

  <main style="flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem;">

  <div class="card shadow-2xl" style="width: 100%; max-width: 450px; padding: 3rem;">
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <h2 style="color: var(--primary); font-size: 2rem; margin-bottom: 0.5rem;">Staff Access</h2>
        <p style="color: var(--text-muted);">Enter your credentials to access the management portal.</p>
    </div>

    <?php if ($login_error): ?>
        <div class="alert alert-error" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; border: 1px solid rgba(239, 68, 68, 0.2);">
            <?php echo $login_error; ?>
        </div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="" novalidate>
      <div class="form-group">
        <label for="email">Staff Email</label>
        <input type="email" id="email" name="email" placeholder="staff@purwase.com" required>
        <small id="emailError" style="color: var(--accent); font-size: 0.8rem; display:block; margin-top: 0.5rem;"></small>
      </div>

      <div class="form-group" style="margin-bottom: 2.5rem;">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Login to Dashboard</button>
    </form>

    <div style="margin-top: 2rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
      <p><a href="../../frontend/index.html" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Home</a></p>
    </div>
  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", function (event) {
      const emailField = document.getElementById("email");
      const emailError = document.getElementById("emailError");
      const email = emailField.value.trim();
      const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;

      if (!regex.test(email)) {
        event.preventDefault();
        emailError.textContent = "⚠ Please enter a valid email address!";
        emailField.style.borderColor = "var(--accent)";
      }
    });
  </script>

  </main>
</body>
</html>

