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

    // Handle rejected accounts
    if ($row['status'] === 'Rejected') {
        $login_error = "Your account has been rejected. Please contact Admin/Manager.";
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
<title>Purwase Staff Login</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f7fa;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .login-container {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        width: 350px;
    }
    h2 {
        text-align: center;
        color: #34495e;
        margin-bottom: 20px;
    }
    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }
    input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }
    button {
        margin-top: 15px;
        padding: 10px;
        width: 100%;
        background: #2980b9;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }
    button:hover {
        background: #1c5d8a;
    }
    .error-msg {
        color: red;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
</head>
<body>
<div class="login-container">
    <h2>Purwase Staff Login</h2>

    <?php
    if (!empty($login_error)) {
        echo "<p class='error-msg'>".$login_error."</p>";
    }
    ?>

    <form id="loginForm" method="POST" action="" novalidate>
    <label>Email:</label>
    <input type="email" id="email" name="email" required>
    <small id="emailError" style="color:red; display:block; font-size:13px;"></small>

    <label>Password:</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>

<script>
document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault(); // stop browser default validation first!

    const emailField = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    const email = emailField.value.trim();

    // reset previous validation style
    emailField.style.border = "";
    emailError.textContent = "";

    // Email pattern validation
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;

    if (!regex.test(email)) {
        emailError.textContent = "⚠ Please enter a valid email address!";
        emailField.style.border = "2px solid red";
    } else {
        this.submit(); // submit only when email is valid ✔
    }
});
</script>

</div>
</body>
</html>
