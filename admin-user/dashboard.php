<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';

require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/admin.classes.php';
require_once __DIR__ . '/../classes/admin-view.classes.php';

$adminView = new AdminView();
$userStats = $adminView->showUserStats();
$earningsStats = $adminView->showEarningsStats();
$newUsersToday = $adminView->showNewUsersToday();
$todayEarnings = $adminView->showTodayEarnings();
$monthEarnings = $adminView->showThisMonthEarnings();

$total_users = $userStats['total_users'] ?? 0;
$active_users = $userStats['active_users'] ?? 0;
$inactive_users = $userStats['inactive_users'] ?? 0;
$suspended_users = $userStats['suspended_users'] ?? 0;
$deleted_users = $userStats['deleted_users'] ?? 0;
$total_earnings_records = $earningsStats['total_earnings_records'] ?? 0;
$total_earnings = $earningsStats['total_earnings'] ?? 0;
$average_earning = $earningsStats['average_earning'] ?? 0;
$highest_earning = $earningsStats['highest_earning'] ?? 0;
$lowest_earning = $earningsStats['lowest_earning'] ?? 0;

$page_title = 'Admin dashboard';
$page_description = 'A live overview of user accounts and earnings across EARN-NOW.';
require __DIR__ . '/layout-header.php';
?>

<section class="admin-section">
    <div class="admin-section-heading">
        <div>
            <h2>Platform overview</h2>
            <p>Current totals from registered accounts and recorded income.</p>
        </div>
        <a class="admin-button admin-button--secondary" href="users.php"><i class="ti-user"></i>&nbsp; Manage users</a>
    </div>

    <div class="admin-stat-grid">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="ti-user"></i></span>
            <div class="admin-stat-copy">
                <p>Total users</p>
                <strong><?php echo number_format((int)$total_users); ?></strong>
                <small><?php echo number_format((int)$newUsersToday); ?> joined today</small>
            </div>
        </article>
        <article class="admin-stat-card admin-stat-card--green">
            <span class="admin-stat-icon"><i class="ti-check-box"></i></span>
            <div class="admin-stat-copy">
                <p>Active users</p>
                <strong><?php echo number_format((int)$active_users); ?></strong>
                <small>Accounts currently active</small>
            </div>
        </article>
        <article class="admin-stat-card admin-stat-card--blue">
            <span class="admin-stat-icon"><i class="ti-wallet"></i></span>
            <div class="admin-stat-copy">
                <p>Total earnings</p>
                <strong><?php echo number_format((float)$total_earnings, 2); ?></strong>
                <small>All recorded entries</small>
            </div>
        </article>
        <article class="admin-stat-card admin-stat-card--rose">
            <span class="admin-stat-icon"><i class="ti-receipt"></i></span>
            <div class="admin-stat-copy">
                <p>Earning records</p>
                <strong><?php echo number_format((int)$total_earnings_records); ?></strong>
                <small>Entries across all accounts</small>
            </div>
        </article>
    </div>
</section>

<section class="admin-section">
    <div class="admin-section-heading">
        <div>
            <h2>Account health</h2>
            <p>Distribution of account statuses.</p>
        </div>
    </div>
    <div class="admin-panel">
        <div class="admin-status-grid">
            <div class="admin-status-item"><span>Active</span><strong><?php echo number_format((int)$active_users); ?></strong></div>
            <div class="admin-status-item"><span>Inactive</span><strong><?php echo number_format((int)$inactive_users); ?></strong></div>
            <div class="admin-status-item"><span>Suspended</span><strong><?php echo number_format((int)$suspended_users); ?></strong></div>
            <div class="admin-status-item"><span>Deleted</span><strong><?php echo number_format((int)$deleted_users); ?></strong></div>
            <div class="admin-status-item"><span>Added today</span><strong><?php echo number_format((int)$newUsersToday); ?></strong></div>
        </div>
    </div>
</section>

<section class="admin-section">
    <div class="admin-section-heading">
        <div>
            <h2>Earnings performance</h2>
            <p>Summary of recorded income and current-period totals.</p>
        </div>
        <a class="admin-button admin-button--secondary" href="earnings.php"><i class="ti-view-list-alt"></i>&nbsp; View earnings</a>
    </div>
    <div class="admin-stat-grid admin-stat-grid--earnings">
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="ti-calendar"></i></span>
            <div class="admin-stat-copy">
                <p>Today</p>
                <strong><?php echo number_format((float)$todayEarnings, 2); ?></strong>
                <small>Recorded today</small>
            </div>
        </article>
        <article class="admin-stat-card admin-stat-card--green">
            <span class="admin-stat-icon"><i class="ti-stats-up"></i></span>
            <div class="admin-stat-copy">
                <p>This month</p>
                <strong><?php echo number_format((float)$monthEarnings, 2); ?></strong>
                <small>Month-to-date total</small>
            </div>
        </article>
        <article class="admin-stat-card admin-stat-card--blue">
            <span class="admin-stat-icon"><i class="ti-bar-chart"></i></span>
            <div class="admin-stat-copy">
                <p>Average earning</p>
                <strong><?php echo number_format((float)$average_earning, 2); ?></strong>
                <small>Average per entry</small>
            </div>
        </article>
        <article class="admin-stat-card admin-stat-card--rose">
            <span class="admin-stat-icon"><i class="ti-cup"></i></span>
            <div class="admin-stat-copy">
                <p>Highest earning</p>
                <strong><?php echo number_format((float)$highest_earning, 2); ?></strong>
                <small>Largest recorded entry</small>
            </div>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-icon"><i class="ti-angle-double-down"></i></span>
            <div class="admin-stat-copy">
                <p>Lowest earning</p>
                <strong><?php echo number_format((float)$lowest_earning, 2); ?></strong>
                <small>Smallest recorded entry</small>
            </div>
        </article>
    </div>
    <div class="admin-quick-links">
        <a class="admin-button" href="users.php">Open user directory</a>
        <a class="admin-button admin-button--secondary" href="earnings.php">Review earnings</a>
    </div>
</section>

<?php require __DIR__ . '/layout-footer.php'; ?>
