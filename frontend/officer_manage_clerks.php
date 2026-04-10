<?php
session_start();

// 🔒 Ensure only Loan Officer can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Loan Officer') {
    header("Location: ../backend/auth/staff_login.php");
    exit();
}

require_once '../backend/config.php';

// Fetch only Clerks
$sql = "SELECT * FROM staff_registration WHERE role = 'Clerk' ORDER BY fullname ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clerks | Purwase Officer</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/theme-switcher.js"></script>
    <style>
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .role-clerk { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        
        .status-approved { color: #10b981; }
        .status-pending { color: #f59e0b; }
        .status-rejected { color: #ef4444; }
        .status-blocked { color: #64748b; }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background-color: var(--bg-card);
            margin: 5% auto;
            padding: 2rem;
            border: 1px solid var(--border);
            width: 90%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
  <?php include 'includes/sidebar.php'; ?>
  
  <main class="main-content">
  <div class="container" style="max-width: 1600px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2>📝 Clerk Management</h2>
            <p style="color: var(--text-muted);">Loan officers can manage clerks responsible for data entry and customer support.</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('addStaffModal')">+ Add Clerk</button>
    </div>

    <div class="table-container shadow-xl">
        <table>
            <thead>
                <tr>
                    <th>Internal ID</th>
                    <th>Full Name</th>
                    <th>Email / Contact</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!$result || mysqli_num_rows($result) === 0) {
                    echo "<tr><td colspan='6' style='text-align:center; padding:3rem;'>No clerk records found.</td></tr>";
                } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                        $roleClass = "role-" . strtolower(str_replace(' ', '-', $row['role']));
                        $statusClass = "status-" . strtolower($row['status']);
                        $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        
                        echo "<tr>
                            <td style='font-weight:600; color:var(--secondary);'>#{$row['staff_id']}</td>
                            <td>
                                <div style='font-weight:600;'>".htmlspecialchars($row['fullname'])."</div>
                                <div style='font-size:0.75rem; color:var(--text-muted);'>Joined: ".date("d M Y", strtotime($row['joining_date']))."</div>
                            </td>
                            <td>
                                <div>".htmlspecialchars($row['email'])."</div>
                                <div style='font-size:0.75rem; color:var(--text-muted);'>".htmlspecialchars($row['phone'])."</div>
                            </td>
                            <td><span class='badge {$roleClass}'>{$row['role']}</span></td>
                            <td class='{$statusClass}' style='font-weight:600;'>{$row['status']}</td>
                            <td>
                                <div style='display:flex; gap:0.5rem;'>
                                    <button class='btn btn-sm' style='background:rgba(255,255,255,0.05); border:1px solid var(--border);' onclick='editStaff($rowData)'>Edit</button>
                                    <button class='btn btn-sm' style='background:rgba(255,255,255,0.05); border:1px solid var(--border);' onclick='resetPassword(".$row['staff_id'].")'>Reset PWD</button>
                                    <a href='../backend/actions/staff_management_actions.php?action=block&staff_id=".$row['staff_id']."' class='btn btn-sm' style='background:rgba(245, 158, 11, 0.1); color:#f59e0b; border:1px solid rgba(245, 158, 11, 0.2);'>".($row['status'] === 'Blocked' ? 'Unblock' : 'Block')."</a>
                                    <a href='../backend/actions/staff_management_actions.php?action=delete&staff_id=".$row['staff_id']."' class='btn btn-sm' style='background:rgba(239, 68, 68, 0.1); color:#ef4444; border:1px solid rgba(239, 68, 68, 0.2);' onclick='return confirm(\"Are you sure you want to delete this clerk?\")'>Delete</a>
                                </div>
                            </td>
                        </tr>";
                    }

                }
                ?>
            </tbody>
        </table>
    </div>

  </div>

  <!-- Add Staff Modal -->
  <div id="addStaffModal" class="modal">
    <div class="modal-content">
        <h3 style="color: var(--text-main);">Provision Clerk Account</h3>
        <form action="../backend/actions/staff_management_actions.php?action=add" method="POST" style="margin-top: 1.5rem;">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required placeholder="Full Name">
                </div>
                <div class="form-group">
                    <label>Internal Staff ID</label>
                    <input type="text" name="staff_id" required placeholder="Staff ID">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Staff Email">
                </div>
                <div class="form-group">
                    <label>Role (Assigned)</label>
                    <input type="text" value="Clerk" readonly style="background: var(--border); cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label>Initial Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required placeholder="Phone Number">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 1rem;">
                <label>Home Address</label>
                <textarea name="address" required style="width: 100%; min-height: 80px;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addStaffModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Clerk Account</button>
            </div>
        </form>
    </div>
  </div>

  <!-- Edit Staff Modal -->
  <div id="editStaffModal" class="modal">
    <div class="modal-content">
        <h3 style="color: var(--text-main);">Modify Clerk Details</h3>
        <form action="../backend/actions/staff_management_actions.php?action=edit" method="POST" style="margin-top: 1.5rem;">
            <input type="hidden" name="original_staff_id" id="edit_original_id">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" id="edit_fullname" required>
                </div>
                <div class="form-group">
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" id="edit_staff_id" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="Clerk" readonly style="background: var(--border); cursor: not-allowed;">
                </div>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editStaffModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Clerk</button>
            </div>
        </form>
    </div>
  </div>

  <!-- Reset Password Modal -->
  <div id="resetModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <h3 style="color: var(--text-main);">Reset Account Password</h3>
        <form action="../backend/actions/staff_management_actions.php?action=reset_pwd" method="POST" style="margin-top: 1.5rem;">
            <input type="hidden" name="target_staff_id" id="reset_staff_id">
            <div class="form-group">
                <label>New Temporary Password</label>
                <input type="password" name="new_password" required placeholder="••••••••">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:2rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('resetModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>
  </div>

  <script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function resetPassword(id) {
        document.getElementById('reset_staff_id').value = id;
        openModal('resetModal');
    }
    function editStaff(data) {
        document.getElementById('edit_original_id').value = data.staff_id;
        document.getElementById('edit_fullname').value = data.fullname;
        document.getElementById('edit_staff_id').value = data.staff_id;
        document.getElementById('edit_email').value = data.email;
        openModal('editStaffModal');
    }
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
        }
    }
  </script>


  </main>
</div>

</body>
</html>
