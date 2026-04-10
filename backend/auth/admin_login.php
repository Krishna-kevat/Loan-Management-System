<?php
session_start();

require_once '../config.php';

$error = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ✅ Check if Super Admin exists
    $query = "SELECT * FROM super_admin WHERE username='$username' AND password='$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Set session for Super Admin
        $_SESSION['super_admin_id'] = $row['admin_id'];
        $_SESSION['role'] = "Super Admin";
        $_SESSION['username'] = $row['username'];

        // Redirect to Super Admin Dashboard
        header("Location: ../../frontend/admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login | Purwase</title>
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
            <h2 style="color: var(--primary); font-size: 2rem; margin-bottom: 0.5rem;">Admin Access</h2>
            <p style="color: var(--text-muted);">Enter credentials for global system oversight.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; border: 1px solid rgba(239, 68, 68, 0.2);">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Super admin username" required>
            </div>
            <div class="form-group" style="margin-bottom: 2.5rem;">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">Authorize Login</button>
        </form>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="../../frontend/index.html" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">&larr; Return to Public Portal</a>
        </div>
    </div>

</main>
</body>
</html>

