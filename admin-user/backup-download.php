<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: backups.php');
    exit();
}

$submittedToken = $_POST['csrf_token'] ?? '';
$confirmation = $_POST['confirm_sensitive'] ?? '';
if (
    !is_string($submittedToken) ||
    !isset($_SESSION['admin_backup_csrf']) ||
    !hash_equals($_SESSION['admin_backup_csrf'], $submittedToken) ||
    $confirmation !== '1'
) {
    http_response_code(403);
    exit('Backup request could not be verified.');
}

require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/backup-exporter.php';
require_once __DIR__ . '/../classes/activity-log.php';

$temporaryStream = fopen('php://temp/maxmemory:2097152', 'w+b');
if ($temporaryStream === false) {
    header('Location: backups.php?error=backupfailed');
    exit();
}

try {
    $exporter = new BackupExporter();
    $tableCount = $exporter->writeSqlBackup($temporaryStream);
    rewind($temporaryStream);
    $fileSize = fstat($temporaryStream)['size'] ?? 0;
} catch (Throwable $exception) {
    fclose($temporaryStream);
    header('Location: backups.php?error=backupfailed');
    exit();
}

(new AdminAudit())->recordActivity(
    $_SESSION['admin_id'],
    $_SESSION['admin_uid'] ?? 'Administrator',
    'database_backup',
    'Generated an on-demand SQL backup for ' . (int)$tableCount . ' tables.'
);

$filename = 'earn-now-backup-' . date('Y-m-d-His') . '.sql';
header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . (int)$fileSize);
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
fpassthru($temporaryStream);
fclose($temporaryStream);
exit();
