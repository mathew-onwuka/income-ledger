<?php
require_once __DIR__ . '/../includes/admin-auth.inc.php';
require_once __DIR__ . '/../classes/dbh.classes.php';
require_once __DIR__ . '/../classes/reports.php';

$allowedPeriods = [3, 6, 12, 24];
$periodInput = filter_var($_GET['months'] ?? 12, FILTER_VALIDATE_INT);
$months = in_array($periodInput, $allowedPeriods, true) ? $periodInput : 12;
$summary = [];
$topUsers = [];
$monthlyRows = [];
$loadError = false;

try {
    $reports = new Reports();
    $summary = $reports->getSummary($months);
    $topUsers = $reports->getTopUsers($months, 10);
    $monthlyRows = $reports->getMonthlyTotals($months);
} catch (PDOException $exception) {
    $loadError = true;
}

$chartLabels = [];
$chartValues = [];
$firstMonth = new DateTimeImmutable('first day of this month');
for ($i = $months - 1; $i >= 0; $i--) {
    $month = $firstMonth->modify('-' . $i . ' months');
    $key = $month->format('Y-m');
    $chartLabels[] = $month->format('M Y');
    $chartValues[] = $monthlyRows[$key]['total'] ?? 0;
}

$page_title = 'Reports';
$page_description = 'Explore earnings trends and the users with the highest recorded totals.';
require __DIR__ . '/layout-header.php';
?>

<form method="GET" action="reports.php" class="admin-filterbar">
    <label for="months">Reporting period</label>
    <select name="months" id="months">
        <?php foreach ([3 => 'Last 3 months', 6 => 'Last 6 months', 12 => 'Last 12 months', 24 => 'Last 24 months'] as $value => $label): ?>
            <option value="<?php echo $value; ?>" <?php echo $months === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit"><i class="ti-filter"></i>&nbsp; Update report</button>
    <a class="admin-button admin-button--secondary" href="earnings.php">Open full earnings ledger</a>
</form>

<?php if ($loadError): ?>
    <div class="alert alert-danger" role="alert">The report could not be loaded. Confirm the users and earnings tables are available.</div>
<?php else: ?>
    <div class="admin-section-heading">
        <div><h2>Earnings overview</h2><p>Totals include entries from the selected rolling month range.</p></div>
    </div>
    <div class="admin-summary-grid">
        <p>Recorded earnings<strong><?php echo number_format((int)($summary['records'] ?? 0)); ?></strong></p>
        <p>Total amount<strong><?php echo number_format((float)($summary['total'] ?? 0), 2); ?></strong></p>
        <p>Average entry<strong><?php echo number_format((float)($summary['average'] ?? 0), 2); ?></strong></p>
    </div>

    <section class="admin-panel">
        <div class="admin-panel-heading">Monthly earnings trend</div>
        <div class="admin-chart-container"><canvas id="earningsTrend" role="img" aria-label="Monthly earnings trend"></canvas></div>
    </section>

    <div class="admin-section-heading">
        <div><h2>Top users</h2><p>Ranked by total earnings recorded in the selected period.</p></div>
    </div>
    <div class="admin-table-wrap">
        <table class="table">
            <thead><tr><th>Rank</th><th>User</th><th>Email</th><th>Entries</th><th>Total earnings</th><th>Profile</th></tr></thead>
            <tbody>
            <?php if ($topUsers): ?>
                <?php foreach ($topUsers as $rank => $user): ?>
                    <?php
                    $name = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
                    if ($name === '') {
                        $name = $user['users_uid'] ?? 'User';
                    }
                    ?>
                    <tr>
                        <td><?php echo $rank + 1; ?></td>
                        <td><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string)$user['users_email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo number_format((int)$user['records']); ?></td>
                        <td><?php echo number_format((float)$user['total'], 2); ?></td>
                        <td><a href="view-user.php?id=<?php echo (int)$user['users_id']; ?>">View user</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No earnings were recorded during this period.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    (function () {
        var canvas = document.getElementById('earningsTrend');
        if (!canvas || typeof Chart === 'undefined') return;
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
                datasets: [{
                    label: 'Earnings',
                    data: <?php echo json_encode($chartValues, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
                    borderColor: '#d4af7a',
                    backgroundColor: 'rgba(212, 175, 122, .12)',
                    fill: true,
                    tension: .32,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#9aa0b4' } } },
                scales: {
                    x: { ticks: { color: '#9aa0b4' }, grid: { color: 'rgba(255,255,255,.06)' } },
                    y: { beginAtZero: true, ticks: { color: '#9aa0b4' }, grid: { color: 'rgba(255,255,255,.06)' } }
                }
            }
        });
    })();
    </script>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
