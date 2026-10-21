<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/admin-directory.php';

$searchInput = $_GET['search'] ?? '';
$search = is_string($searchInput) ? trim($searchInput) : '';
$statusInput = $_GET['status'] ?? '';
$status = is_string($statusInput) && in_array($statusInput, ['', 'active', 'inactive', 'suspended'], true)
    ? $statusInput
    : '';
$limit = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$admins = [];
$totalAdmins = 0;
$loadError = false;

try {
    $directory = new AdminDirectory();
    $totalAdmins = $directory->countAdmins($search, $status);
    $admins = $directory->getAdmins($search, $status, $offset, $limit);
} catch (PDOException $exception) {
    $loadError = true;
}

$totalPages = max(1, (int)ceil($totalAdmins / $limit));
$page_title = 'Admin accounts';
$page_description = 'Review administrator access and assigned roles.';
require __DIR__ . '/layout-header.php';
?>

<?php if ($loadError): ?>
    <div class="alert alert-danger" role="alert">The admin directory could not be loaded. Check that the admins table uses the account fields configured for this application.</div>
<?php else: ?>
    <div class="admin-section-heading">
        <div><h2>Administrator directory</h2><p><?php echo number_format($totalAdmins); ?> accounts match the selected filters.</p></div>
    </div>

    <div class="admin-filterbar">
        <form method="GET" action="admins.php" role="search">
            <input type="search" name="search" aria-label="Search admin accounts" placeholder="Search name, username, or email" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <select name="status" aria-label="Filter by status">
                <option value="" <?php echo $status === '' ? 'selected' : ''; ?>>All statuses</option>
                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
            </select>
            <button type="submit"><i class="ti-search"></i>&nbsp; Search</button>
            <?php if ($search !== '' || $status !== ''): ?><a class="admin-button admin-button--secondary" href="admins.php">Clear</a><?php endif; ?>
        </form>
    </div>

    <div class="admin-table-wrap">
        <table class="table">
            <thead><tr><th>Admin ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr></thead>
            <tbody>
            <?php if ($admins): ?>
                <?php foreach ($admins as $admin): ?>
                    <?php
                    $adminStatus = (string)($admin['account_status'] ?? '');
                    $statusClass = preg_replace('/[^a-z0-9_-]/i', '', strtolower($adminStatus));
                    ?>
                    <tr>
                        <td><?php echo (int)$admin['admin_id']; ?></td>
                        <td><?php echo htmlspecialchars(trim(($admin['admin_firstname'] ?? '') . ' ' . ($admin['admin_lastname'] ?? '')) ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$admin['admin_uid'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$admin['admin_email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$admin['admin_role'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="admin-status-pill admin-status-pill--<?php echo htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($adminStatus, ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo htmlspecialchars((string)$admin['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No administrator accounts match these filters.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Administrator pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php echo htmlspecialchars(http_build_query(['search' => $search, 'status' => $status, 'page' => $i]), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $i === $page ? 'aria-current="page"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>

    <p class="admin-note">This screen is read-only; changing administrator roles or disabling privileged accounts needs a defined role policy.</p>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
