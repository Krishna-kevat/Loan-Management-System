<?php
/**
 * Sidebar Component
 * Robustly handle variable initialization from session
 */
$current_page = basename($_SERVER['PHP_SELF']);
$role = $role ?? $_SESSION['role'] ?? 'System';
$fullname = $fullname ?? $_SESSION['fullname'] ?? null;
$username = $username ?? $_SESSION['username'] ?? null;

function isActive($page, $current_page) {
    return ($page === $current_page) ? 'active' : '';
}
?>


<aside class="sidebar">
    <div class="sidebar-header">
        <h1>Purwase</h1>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Management Portal</p>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <!-- Administration Portal -->
            <?php if ($role === 'Super Admin'): ?>
                <li>
                    <a href="admin_dashboard.php" class="nav-link <?php echo isActive('admin_dashboard.php', $current_page); ?>">
                        <i>🏠</i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="admin_manage_staff.php" class="nav-link <?php echo isActive('admin_manage_staff.php', $current_page); ?>">
                        <i>👥</i> Staff Management
                    </a>
                </li>

                <li>
                    <a href="manage_customers.php" class="nav-link <?php echo isActive('manage_customers.php', $current_page); ?>">
                        <i>👤</i> Customers
                    </a>
                </li>
                <li>
                    <a href="admin_view_report.php" class="nav-link <?php echo isActive('admin_view_report.php', $current_page); ?>">
                        <i>📊</i> Global Reports
                    </a>
                </li>
                <li>
                    <a href="../backend/logout/admin_logout.php" class="nav-link" style="color: var(--accent);">
                        <i>🚪</i> Logout
                    </a>
                </li>
            <?php else: ?>

                <!-- Staff Roles -->
                <li>
                    <a href="staff_dashboard.php" class="nav-link <?php echo isActive('staff_dashboard.php', $current_page); ?>">
                        <i>🏠</i> Dashboard
                    </a>
                </li>

                <?php if ($role === 'Manager'): ?>
                    <li>
                        <a href="manager_loan_approval.php" class="nav-link <?php echo isActive('manager_loan_approval.php', $current_page); ?>">
                            <i>💰</i> Loan Approvals
                        </a>
                    </li>

                    <li>
                        <a href="manager_reports.php" class="nav-link <?php echo isActive('manager_reports.php', $current_page); ?>">
                            <i>📊</i> Reports
                        </a>
                    </li>
                    <li>
                        <a href="manager_manage_officers.php" class="nav-link <?php echo isActive('manager_manage_officers.php', $current_page); ?>">
                            <i>👥</i> Manage Officers
                        </a>
                    </li>
                    <li>
                        <a href="../backend/logout/manager_logout.php" class="nav-link" style="color: var(--accent);">
                            <i>🚪</i> Logout
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'Loan Officer'): ?>
                    <li>
                        <a href="loan_officer_review.php" class="nav-link <?php echo isActive('loan_officer_review.php', $current_page); ?>">
                            <i>🔍</i> Review Apps
                        </a>
                    </li>
                    <li>
                        <a href="loanofficer_customer_loan.php" class="nav-link <?php echo isActive('loanofficer_customer_loan.php', $current_page); ?>">
                            <i>📜</i> Loan History
                        </a>
                    </li>
                    <li>
                        <a href="officer_manage_clerks.php" class="nav-link <?php echo isActive('officer_manage_clerks.php', $current_page); ?>">
                            <i>👥</i> Manage Clerks
                        </a>
                    </li>
                    <li>
                        <a href="../backend/logout/loanofficer_logout.php" class="nav-link" style="color: var(--accent);">
                            <i>🚪</i> Logout
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'Clerk'): ?>
                    <li>
                        <a href="clerk_data_entry.php" class="nav-link <?php echo isActive('clerk_data_entry.php', $current_page); ?>">
                            <i>📝</i> Data Entry
                        </a>
                    </li>
                    <li>
                        <a href="clerk_manage_customerrequest.php" class="nav-link <?php echo isActive('clerk_manage_customerrequest.php', $current_page); ?>">
                            <i>🎧</i> Support Tickets
                        </a>
                    </li>
                    <li>
                        <a href="../backend/logout/clerk_logout.php" class="nav-link" style="color: var(--accent);">
                            <i>🚪</i> Logout
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1rem;">
            <div style="width: 32px; height: 32px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: white;">
                <?php echo strtoupper(substr($fullname ?? $username ?? 'U', 0, 1)); ?>
            </div>
            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <p style="font-size: 0.85rem; font-weight: 600;"><?php echo htmlspecialchars($fullname ?? $username ?? 'User'); ?></p>
                <p style="font-size: 0.7rem; color: var(--text-muted);"><?php echo htmlspecialchars($role ?? 'Admin'); ?></p>
            </div>
        </div>
        
        <button class="theme-toggle" aria-label="Toggle Theme" style="width: 100%; border-radius: 0.75rem; justify-content: center; gap: 10px;">
            <span class="sun">☀️ Light Mode</span>
            <span class="moon">🌙 Dark Mode</span>
        </button>
    </div>
</aside>
