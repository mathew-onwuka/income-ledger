<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';

if (!isset($_SESSION['admin_backup_csrf'])) {
    $_SESSION['admin_backup_csrf'] = bin2hex(random_bytes(32));
}

$page_title = 'Backups';
$page_description = 'Create a point-in-time SQL export of the configured application database.';
require __DIR__ . '/layout-header.php';
?>

<?php if (($_GET['error'] ?? '') === 'backupfailed'): ?>
    <div class="alert alert-danger" role="alert">The database export failed. Confirm the database is available and check the PHP error log for details.</div>
<?php endif; ?>

<section class="admin-panel admin-backup-panel">
    <div class="admin-panel-heading">Create database backup</div>
    <p>This creates a downloadable SQL file containing the database tables and their data. The export is generated on demand and is not retained on the server.</p>
    <div class="admin-backup-warning">
        <i class="ti-alert"></i>
        <span>The file includes personal account data and password hashes. Store it securely. Restoring a dump can replace data in the destination database.</span>
    </div>

    <form method="POST" action="backup-download.php" class="admin-backup-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_backup_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
        <label class="admin-backup-confirm">
            <input type="checkbox" name="confirm_sensitive" value="1" required>
            I understand this export contains sensitive database data.
        </label>
        <button type="submit"><i class="ti-download"></i>&nbsp; Download SQL backup</button>
    </form>
</section>

<?php require __DIR__ . '/layout-footer.php'; ?>
