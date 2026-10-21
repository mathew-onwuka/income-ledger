<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/activity-log.php';

$typeInput = $_GET['type'] ?? '';
$resultInput = $_GET['result'] ?? '';
$type = is_string($typeInput) && in_array($typeInput, ['', 'admin', 'user'], true) ? $typeInput : '';
$result = is_string($resultInput) && in_array($resultInput, ['', 'success', 'failed'], true) ? $resultInput : '';
$limit = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$tableReady = true;
$history = [];
$totalEntries = 0;

try {
    $audit = new AdminAudit();
    $totalEntries = $audit->countLoginHistory($type, $result);
    $history = $audit->getLoginHistory($type, $result, $offset, $limit);
} catch (PDOException $exception) {
    $tableReady = false;
}

$totalPages = max(1, (int)ceil($totalEntries / $limit));
$page_title = 'Login history';
$page_description = 'Review successful and unsuccessful sign-in attempts for user and admin accounts.';
require __DIR__ . '/layout-header.php';
?>

<?php if (!$tableReady): ?>
    <div class="alert alert-warning" role="alert">
        Login history needs its one-time database migration. Run <strong>sql/admin-features.sql</strong> against the app database, then reload this page.
    </div>
<?php else: ?>
    <div class="admin-section-heading">
        <div>
            <h2>Sign-in attempts</h2>
            <p><?php echo number_format($totalEntries); ?> attempts match the selected filters. Passwords are never recorded.</p>
        </div>
    </div>

    <form method="GET" action="login-history.php" class="admin-filterbar">
        <label for="type">Account</label>
        <select id="type" name="type">
            <option value="" <?php echo $type === '' ? 'selected' : ''; ?>>All accounts</option>
            <option value="admin" <?php echo $type === 'admin' ? 'selected' : ''; ?>>Admins</option>
            <option value="user" <?php echo $type === 'user' ? 'selected' : ''; ?>>Users</option>
        </select>
        <label for="result">Result</label>
        <select id="result" name="result">
            <option value="" <?php echo $result === '' ? 'selected' : ''; ?>>All results</option>
            <option value="success" <?php echo $result === 'success' ? 'selected' : ''; ?>>Successful</option>
            <option value="failed" <?php echo $result === 'failed' ? 'selected' : ''; ?>>Unsuccessful</option>
        </select>
        <button type="submit"><i class="ti-filter"></i>&nbsp; Apply</button>
        <?php if ($type !== '' || $result !== ''): ?><a class="admin-button admin-button--secondary" href="login-history.php">Clear</a><?php endif; ?>
    </form>

    <div class="admin-table-wrap">
        <table class="table">
            <thead>
                <tr><th>When</th><th>Account type</th><th>Account</th><th>Result</th><th>IP address</th><th>Browser / device</th></tr>
            </thead>
            <tbody>
            <?php if ($history): ?>
                <?php foreach ($history as $entry): ?>
                    <?php $wasSuccessful = (int)$entry['success'] === 1; ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string)$entry['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst((string)$entry['account_type']), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$entry['account_identifier'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="admin-status-pill admin-status-pill--<?php echo $wasSuccessful ? 'active' : 'failed'; ?>"><?php echo $wasSuccessful ? 'Success' : 'Failed'; ?></span></td>
                        <td><?php echo htmlspecialchars((string)($entry['ip_address'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="admin-user-agent"><?php echo htmlspecialchars((string)($entry['user_agent'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No sign-in attempts match these filters yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Login history pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php echo htmlspecialchars(http_build_query(['type' => $type, 'result' => $result, 'page' => $i]), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $i === $page ? 'aria-current="page"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
