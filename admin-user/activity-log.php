<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/activity-log.php';

$searchValue = $_GET['search'] ?? '';
$search = is_string($searchValue) ? trim($searchValue) : '';
$limit = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$tableReady = true;
$logs = [];
$totalLogs = 0;

try {
    $audit = new AdminAudit();
    $totalLogs = $audit->countActivityLogs($search);
    $logs = $audit->getActivityLogs($search, $offset, $limit);
} catch (PDOException $exception) {
    $tableReady = false;
}

$totalPages = max(1, (int)ceil($totalLogs / $limit));
$page_title = 'Activity log';
$page_description = 'Review administrator actions recorded in the application.';
require __DIR__ . '/layout-header.php';
?>

<?php if (!$tableReady): ?>
    <div class="alert alert-warning" role="alert">
        Activity logging needs its one-time database migration. Run <strong>sql/admin-features.sql</strong> against the app database, then reload this page.
    </div>
<?php else: ?>
    <div class="admin-section-heading">
        <div>
            <h2>Recent activity</h2>
            <p><?php echo number_format($totalLogs); ?> logged administrator actions.</p>
        </div>
    </div>

    <div class="admin-filterbar">
        <form method="GET" action="activity-log.php" role="search">
            <input type="search" name="search" aria-label="Search activity" placeholder="Search admin, action, or details" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit"><i class="ti-search"></i>&nbsp; Search</button>
            <?php if ($search !== ''): ?><a class="admin-button admin-button--secondary" href="activity-log.php">Clear</a><?php endif; ?>
        </form>
    </div>

    <div class="admin-table-wrap">
        <table class="table">
            <thead>
                <tr><th>When</th><th>Administrator</th><th>Action</th><th>Details</th><th>Target</th><th>IP address</th></tr>
            </thead>
            <tbody>
            <?php if ($logs): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string)$log['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$log['admin_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="admin-status-pill"><?php echo htmlspecialchars(str_replace('_', ' ', (string)$log['action']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo htmlspecialchars((string)$log['details'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if (!empty($log['target_user_id'])): ?>
                                <a href="view-user.php?id=<?php echo (int)$log['target_user_id']; ?>">User #<?php echo (int)$log['target_user_id']; ?></a>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars((string)($log['ip_address'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No activity has been recorded for this search.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Activity pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php echo htmlspecialchars(http_build_query(['search' => $search, 'page' => $i]), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $i === $page ? 'aria-current="page"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
