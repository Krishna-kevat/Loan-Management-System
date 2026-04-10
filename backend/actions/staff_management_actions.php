<?php
session_start();
require_once '../config.php';

/**
 * Roles Hierarchy:
 * Super Admin -> Manager
 * Manager -> Loan Officer
 * Loan Officer -> Clerk
 */

// Basic security check: user must be logged in as staff or admin
if (!isset($_SESSION['role']) && !isset($_SESSION['super_admin_id'])) {
    die("Unauthorized access.");
}

$requester_role = $_SESSION['role'] ?? 'Super Admin';
$action = $_GET['action'] ?? '';

// Determine target role and redirection page based on requester
$target_role = '';
$redirect_page = '';

if ($requester_role === 'Super Admin') {
    $target_role = 'Manager';
    $redirect_page = '../../frontend/admin_manage_staff.php';
} elseif ($requester_role === 'Manager') {
    $target_role = 'Loan Officer';
    $redirect_page = '../../frontend/manager_manage_officers.php';
} elseif ($requester_role === 'Loan Officer') {
    $target_role = 'Clerk';
    $redirect_page = '../../frontend/officer_manage_clerks.php';
} else {
    die("You do not have permission to perform staff management actions.");
}

if ($action === 'add') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Status depends on who is adding. 
    // Admin adding Manager -> Approved
    // Manager adding Officer -> Approved
    // Officer adding Clerk -> Approved
    $status = 'Approved'; 
    $joining_date = date('Y-m-d');

    // Check if staff_id or email already exists
    $check_sql = "SELECT staff_id FROM staff_registration WHERE staff_id = '$staff_id' OR email = '$email'";
    $check_res = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_res) > 0) {
        header("Location: $redirect_page?error=Staff ID or Email already exists");
        exit();
    }

    $sql = "INSERT INTO staff_registration 
            (fullname, email, password, phone, address, staff_id, role, status, joining_date) 
            VALUES 
            ('$fullname', '$email', '$password', '$phone', '$address', '$staff_id', '$target_role', '$status', '$joining_date')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: $redirect_page?msg=Staff added successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'delete') {
    $staff_id = mysqli_real_escape_string($conn, $_GET['staff_id']);
    
    // Ensure the requester is only deleting their subordinate
    $sql = "DELETE FROM staff_registration WHERE staff_id = '$staff_id' AND role = '$target_role'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: $redirect_page?msg=Staff deleted successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'block') {
    $staff_id = mysqli_real_escape_string($conn, $_GET['staff_id']);
    
    // Toggle block status or just set to Blocked? 
    // Usually "Block" means set to Blocked. To Unblock, they can Edit or we can add an Unblock action.
    // Let's implement toggle if it's already blocked, or just "Block".
    // User said "block only managers" and "this pocess done by manger panel for loan officer" etc.
    
    $check_sql = "SELECT status FROM staff_registration WHERE staff_id = '$staff_id' AND role = '$target_role'";
    $check_res = mysqli_query($conn, $check_sql);
    $row = mysqli_fetch_assoc($check_res);
    
    $new_status = ($row['status'] === 'Blocked') ? 'Approved' : 'Blocked';
    $msg = ($new_status === 'Blocked') ? "Staff blocked successfully" : "Staff unblocked successfully";

    $sql = "UPDATE staff_registration SET status = '$new_status' WHERE staff_id = '$staff_id' AND role = '$target_role'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: $redirect_page?msg=$msg");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'edit') {
    $original_id = mysqli_real_escape_string($conn, $_POST['original_staff_id']);
    $fullname    = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $staff_id    = mysqli_real_escape_string($conn, $_POST['staff_id']);
    
    // Hierarchy check: ensure we are editing the correct role
    $sql = "UPDATE staff_registration 
            SET fullname = '$fullname', 
                email = '$email', 
                staff_id = '$staff_id'
            WHERE staff_id = '$original_id' AND role = '$target_role'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: $redirect_page?msg=Staff updated successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'reset_pwd') {
    $staff_id = mysqli_real_escape_string($conn, $_POST['target_staff_id']);
    $new_pwd  = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $sql = "UPDATE staff_registration SET password = '$new_pwd' WHERE staff_id = '$staff_id' AND role = '$target_role'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: $redirect_page?msg=Password reset successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
