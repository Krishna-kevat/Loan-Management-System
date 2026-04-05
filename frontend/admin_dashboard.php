<?php
session_start();

// 🔒 Security check
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    header("Location: admin_login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purwase Super Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; margin:0; padding:0; }
    body { font-family: 'Roboto', sans-serif; background: #f0f2f5; color: #333; min-height: 100vh; }

    /* Navbar */
    .navbar {
        background: linear-gradient(90deg, #34495e, #2c3e50);
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }
    .navbar h2 { font-weight: 700; }
    .navbar a {
        color: white;
        margin-left: 20px;
        text-decoration: none;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 4px;
        background: #e74c3c;
        transition: 0.3s;
    }
    .navbar a:hover { background: #c0392b; }

    /* Container */
    .container { max-width: 1600px; margin: 30px auto; padding: 20px; min-height: calc(100vh - 100px); }

    /* Welcome message */
    .welcome {
        font-size: 20px;
        margin-bottom: 30px;
        text-align: center;
        color: #2c3e50;
    }

    /* Cards grid */
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        align-items: stretch;
    }

    /* Individual card */
    .card {
        background: #fff;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .card h4 {
        font-size: 24px;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .card a {
        display: inline-block;
        margin-top: auto;
        padding: 12px 24px;
        background: #3498db;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 16px;
        transition: 0.3s;
    }
    .card a:hover { background: #2980b9; }

    /* Footer info */
    .footer {
        text-align: center;
        margin-top: 50px;
        font-size: 14px;
        color: #777;
    }

    @media(max-width: 600px){
        .navbar { flex-direction: column; align-items: flex-start; }
        .navbar div { margin-top: 10px; }
    }
</style>
</head>
<body>

<div class="navbar">
    <h2>Purwase Super Admin Dashboard</h2>
    <div>
        Welcome, <strong><?php echo htmlspecialchars($username); ?></strong>
        <a href="../backend/logout/admin_logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="welcome">
        Manage your system effectively. Click on any card to view or manage corresponding modules.
    </div>

    <div class="cards">
        <div class="card">
            <h4>Manage Managers</h4>
            <a href="manage_managers.php">Go</a>
        </div>

        <div class="card">
            <h4>Manage Loan Officers</h4>
            <a href="manage_officers.php">Go</a>
        </div>

        <div class="card">
            <h4>View Reports</h4>
            <a href="admin_view_report.php">Go</a>
        </div>
    </div>
</div>

<div class="footer">
    &copy; <?php echo date("Y"); ?> Purwase Company. All rights reserved.
</div>

</body>
</html>
