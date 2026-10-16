
<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("location: ../admin-login.php?error=notloggedin");
    exit();
}

include "../classes/dbh.classes.php";
include "../classes/admin.classes.php";
include "../classes/admin-view.classes.php";
include "../classes/admin-contr.classes.php";

$adminView = new AdminView();

$adminContr = new AdminContr(
    $_SESSION["admin_id"],
    $_SESSION["admin_role"]
);


/*
|--------------------------------------------------------------------------
| Validate Filter
|--------------------------------------------------------------------------
*/

$allowedFilters = [
    'all',
    'today',
    'yesterday',
    'week',
    'month',
    'year',
    'custom'
];

$filter = $_GET['filter'] ?? 'all';

if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'all';
}

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;


/*
|--------------------------------------------------------------------------
| Validate Custom Dates
|--------------------------------------------------------------------------
*/

if ($filter === 'custom') {

    $validStartDate = is_string($startDate)
        && DateTime::createFromFormat('!Y-m-d', $startDate)
        && DateTime::createFromFormat('!Y-m-d', $startDate)->format('Y-m-d') === $startDate;

    $validEndDate = is_string($endDate)
        && DateTime::createFromFormat('!Y-m-d', $endDate)
        && DateTime::createFromFormat('!Y-m-d', $endDate)->format('Y-m-d') === $endDate;

    if (!$validStartDate || !$validEndDate) {
        $filter = 'all';
        $startDate = null;
        $endDate = null;
    } elseif ($startDate > $endDate) {
        $temp = $startDate;
        $startDate = $endDate;
        $endDate = $temp;
    }
}

$searchInput = $_GET['search'] ?? '';

$search = is_string($searchInput)
    ? trim($searchInput)
    : '';

/*
|--------------------------------------------------------------------------
| Calculate Date Range
|--------------------------------------------------------------------------
*/

$dateRange = $adminContr->getDateRange(
    $filter,
    $startDate,
    $endDate
);


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$limit = 20;

$page = max(1, (int)($_GET['page'] ?? 1));

$offset = ($page - 1) * $limit;



/*
|--------------------------------------------------------------------------
| Fetch Earnings, Counts and Statistics
|--------------------------------------------------------------------------
*/

if ($dateRange === null) {

    $earnings = $adminView->showAllEarnings(
        null,
        null,
        $offset,
        $limit,
        $search
    );

    $totalRecords = $adminView->showTotalAllEarnings(
        null,
        null,
        $search
    );

    $earningsStats = $adminView->showAllEarningsStats(
        null,
        null,
        $search
    );

} else {

    $earnings = $adminView->showAllEarnings(
        $dateRange['start'],
        $dateRange['end'],
        $offset,
        $limit,
        $search
    );

    $totalRecords = $adminView->showTotalAllEarnings(
        $dateRange['start'],
        $dateRange['end'],
        $search
    );

    $earningsStats = $adminView->showAllEarningsStats(
        $dateRange['start'],
        $dateRange['end'],
        $search
    );
}


// Per-user earnings report pagination
$userLimit = 20;

$userPage = filter_var(
    $_GET['user_page'] ?? 1,
    FILTER_VALIDATE_INT
);

$userPage = ($userPage !== false && $userPage > 0)
    ? $userPage
    : 1;

$userOffset = ($userPage - 1) * $userLimit;

// Load grouped report using the active date filter and search
if ($dateRange === null) {
    $userEarnings = $adminView->showEarningsByUser(
        null,
        null,
        $userOffset,
        $userLimit,
        $search
    );

    $totalEarningsUsers = $adminView->showTotalEarningsUsers(
        null,
        null,
        $search
    );
} else {
    $userEarnings = $adminView->showEarningsByUser(
        $dateRange['start'],
        $dateRange['end'],
        $userOffset,
        $userLimit,
        $search
    );

    $totalEarningsUsers = $adminView->showTotalEarningsUsers(
        $dateRange['start'],
        $dateRange['end'],
        $search
    );
}

$totalUserPages = (int) ceil(
    $totalEarningsUsers / $userLimit
);
/*
|--------------------------------------------------------------------------
| Export Filtered Earnings to CSV
|--------------------------------------------------------------------------
*/

if (($_GET['export'] ?? '') === 'csv') {

    if ($dateRange === null) {

        $exportRecords = $adminView->exportAllEarnings(
            null,
            null,
            $search
        );

    } else {

        $exportRecords = $adminView->exportAllEarnings(
            $dateRange['start'],
            $dateRange['end'],
            $search
        );
    }

    // Prevent CSV values from being interpreted as spreadsheet formulas.
    $safeCsvValue = function ($value) {
        $value = (string)($value ?? '');

       if (preg_match('/^[\s]*[=+\-@]/u', $value)) {
            return "'" . $value;
        }

        return $value;
    };

    // Remove any buffered output before sending download headers.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $filename = 'earnings-report-' . date('Y-m-d') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header(
        'Content-Disposition: attachment; filename="' .
        $filename . '"'
    );
    header('Cache-Control: no-store, no-cache, must-revalidate');

    // UTF-8 BOM helps spreadsheet programs recognise the encoding.
    echo "\xEF\xBB\xBF";

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'Earning ID',
        'User ID',
        'Username',
        'Full Name',
        'Email',
        'Title',
        'Description',
        'Amount',
        'Date'
    ]);

    foreach ($exportRecords as $record) {

        $fullName = trim(
            ($record['firstname'] ?? '') . ' ' .
            ($record['lastname'] ?? '')
        );

        fputcsv($output, [
            $safeCsvValue($record['earnings_id']),
            $safeCsvValue($record['users_id']),
            $safeCsvValue($record['users_uid']),
            $safeCsvValue($fullName),
            $safeCsvValue($record['users_email']),
            $safeCsvValue($record['earnings_title']),
            $safeCsvValue($record['earnings_description']),
            $safeCsvValue($record['earnings_amount']),
            $safeCsvValue($record['reg_date'])
        ]);
    }

    fclose($output);
    exit();
}

/*
|--------------------------------------------------------------------------
| Pagination Calculations
|--------------------------------------------------------------------------
*/

$totalPages = (int) ceil($totalRecords / $limit);

if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;

    $redirectParams = [
        'filter' => $filter,
        'page' => $page
    ];

    if ($search !== '') {
        $redirectParams['search'] = $search;
    }

    if ($filter === 'custom') {
        $redirectParams['start_date'] = $startDate;
        $redirectParams['end_date'] = $endDate;
    }

    header(
        "Location: earnings.php?" .
        http_build_query($redirectParams)
    );

    exit();
}


/*
|--------------------------------------------------------------------------
| Pagination Query Parameters
|--------------------------------------------------------------------------
*/

$queryParams = [
    'filter' => $filter
];

if ($search !== '') {
    $queryParams['search'] = $search;
}

if ($filter === 'custom') {
    $queryParams['start_date'] = $startDate;
    $queryParams['end_date'] = $endDate;
}

?>

<h1>Global Earnings</h1>

<h2>Earnings Summary</h2>

<p>
    Total Earnings:
    <?php
    echo number_format(
        (float)$earningsStats['total_earnings'],
        2
    );
    ?>
</p>

<p>
    Number of Records:
    <?php
    echo (int)$earningsStats['total_earnings_records'];
    ?>
</p>

<p>
    Average Earning:
    <?php
    echo number_format(
        (float)$earningsStats['average_earning'],
        2
    );
    ?>
</p>

<p>
    Highest Earning:
    <?php
    echo number_format(
        (float)$earningsStats['highest_earning'],
        2
    );
    ?>
</p>

<p>
    Lowest Earning:
    <?php
    echo number_format(
        (float)$earningsStats['lowest_earning'],
        2
    );
    ?>
</p>

<h2>Filter Earnings</h2>

<form method="GET" action="earnings.php">

    <label for="search">Search User</label>

    <input
        type="search"
        name="search"
        id="search"
        placeholder="Username, name or email"
        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
    >

    <label for="filter">Date Filter</label>

    <select name="filter" id="filter">

        <option value="all"
            <?php echo $filter === 'all' ? 'selected' : ''; ?>>
            All Earnings
        </option>

        <option value="today"
            <?php echo $filter === 'today' ? 'selected' : ''; ?>>
            Today
        </option>

        <option value="yesterday"
            <?php echo $filter === 'yesterday' ? 'selected' : ''; ?>>
            Yesterday
        </option>

        <option value="week"
            <?php echo $filter === 'week' ? 'selected' : ''; ?>>
            This Week
        </option>

        <option value="month"
            <?php echo $filter === 'month' ? 'selected' : ''; ?>>
            This Month
        </option>

        <option value="year"
            <?php echo $filter === 'year' ? 'selected' : ''; ?>>
            This Year
        </option>

        <option value="custom"
            <?php echo $filter === 'custom' ? 'selected' : ''; ?>>
            Custom Date Range
        </option>

    </select>

    <div>
        <label for="start_date">Start Date</label>

        <input
            type="date"
            name="start_date"
            id="start_date"
            value="<?php echo htmlspecialchars($startDate ?? ''); ?>"
        >

        <label for="end_date">End Date</label>

        <input
            type="date"
            name="end_date"
            id="end_date"
            value="<?php echo htmlspecialchars($endDate ?? ''); ?>"
        >
    </div>

    <button type="submit">Search / Apply Filter</button>

    <a href="earnings.php">Clear Filter</a>

</form>


<h2>Earnings by User</h2>

<p>
    Users with earnings matching the selected filters:
    <?php echo number_format($totalEarningsUsers); ?>
</p>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>User</th>
            <th>Email</th>
            <th>Entries</th>
            <th>Total Earnings</th>
            <th>Average</th>
            <th>Highest</th>
            <th>Lowest</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php if (!empty($userEarnings)): ?>

            <?php foreach ($userEarnings as $earningUser): ?>

                <?php
                $fullName = trim(
                    ($earningUser['firstname'] ?? '') . ' ' .
                    ($earningUser['lastname'] ?? '')
                );

                if ($fullName === '') {
                    $fullName = $earningUser['users_uid'] ?? 'User';
                }
                ?>

                <tr>
                    <td>
                        <?php echo htmlspecialchars(
                            $fullName,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars(
                            $earningUser['users_email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </td>

                    <td>
                        <?php echo number_format(
                            (int) $earningUser['total_entries']
                        ); ?>
                    </td>

                    <td>
                        <?php echo number_format(
                            (float) $earningUser['total_earnings'],
                            2
                        ); ?>
                    </td>

                    <td>
                        <?php echo number_format(
                            (float) $earningUser['average_earning'],
                            2
                        ); ?>
                    </td>

                    <td>
                        <?php echo number_format(
                            (float) $earningUser['highest_earning'],
                            2
                        ); ?>
                    </td>

                    <td>
                        <?php echo number_format(
                            (float) $earningUser['lowest_earning'],
                            2
                        ); ?>
                    </td>

                    <td>
                        <a href="view-user.php?id=<?php
                            echo (int) $earningUser['users_id'];
                        ?>">
                            View User
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="8">
                    No earnings found for these filters.
                </td>
            </tr>

        <?php endif; ?>
    </tbody>
</table>

<?php if ($totalUserPages > 1): ?>

    <nav aria-label="User earnings pagination">

        <?php
        $userQueryParams = $queryParams;
        ?>

        <?php if ($userPage > 1): ?>
            <?php
            $userQueryParams['user_page'] = $userPage - 1;
            ?>
            <a href="earnings.php?<?php
                echo htmlspecialchars(
                    http_build_query($userQueryParams),
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>">Previous</a>
        <?php endif; ?>

        <span>
            Page <?php echo $userPage; ?>
            of <?php echo $totalUserPages; ?>
        </span>

        <?php if ($userPage < $totalUserPages): ?>
            <?php
            $userQueryParams['user_page'] = $userPage + 1;
            ?>
            <a href="earnings.php?<?php
                echo htmlspecialchars(
                    http_build_query($userQueryParams),
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>">Next</a>
        <?php endif; ?>

    </nav>

<?php endif; ?>

<?php if ($dateRange !== null): ?>

    <p>
        Showing earnings from
        <strong>
            <?php echo htmlspecialchars($dateRange['start']); ?>
        </strong>
        to
        <strong>
            <?php echo htmlspecialchars($dateRange['end']); ?>
        </strong>
    </p>

<?php else: ?>



<?php if ($dateRange !== null): ?>

    <p>
        Showing earnings from
        <strong>
            <?php echo htmlspecialchars(
                $dateRange['start'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </strong>
        to
        <strong>
            <?php echo htmlspecialchars(
                $dateRange['end'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </strong>
    </p>

<?php else: ?>

    <p>Showing all earnings.</p>

<?php endif; ?>

<?php
$exportParams = $queryParams;
$exportParams['export'] = 'csv';

$exportUrl = 'earnings.php?' . http_build_query($exportParams);
?>

<p>
    <a href="<?php echo htmlspecialchars(
        $exportUrl,
        ENT_QUOTES,
        'UTF-8'
    ); ?>">
        Export Filtered Earnings to CSV
    </a>
</p>




    <p>Showing all earnings.</p>

<?php endif; ?>

<h2>All Earnings Records</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Title</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php if (!empty($earnings)): ?>

            <?php foreach ($earnings as $earning): ?>

                <tr>
                    <td>
                        <?php echo (int)$earning['earnings_id']; ?>
                    </td>

                    <td>
                        <?php
                        $fullName = trim(
                            ($earning['firstname'] ?? '') . ' ' .
                            ($earning['lastname'] ?? '')
                        );

                        echo htmlspecialchars(
                            $fullName !== ''
                                ? $fullName
                                : $earning['users_uid']
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $earning['users_email']
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $earning['earnings_title']
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $earning['earnings_description']
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo number_format(
                            (float)$earning['earnings_amount'],
                            2
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $earning['reg_date']
                        );
                        ?>
                    </td>

                    <td>
                        <a href="view-user.php?id=<?php
                            echo (int)$earning['users_id'];
                        ?>">
                            View User
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="8">
                    No earnings records found for this period.
                </td>
            </tr>

        <?php endif; ?>
    </tbody>
</table>

<?php if ($totalPages > 1): ?>

    <nav aria-label="Earnings pagination">

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <?php
            $pageParams = $queryParams;
            $pageParams['page'] = $i;

            $pageUrl = 'earnings.php?' .
                http_build_query($pageParams);
            ?>

            <a
                href="<?php echo htmlspecialchars($pageUrl); ?>"
                <?php echo $page === $i
                    ? 'aria-current="page"'
                    : ''; ?>
            >
                <?php echo $i; ?>
            </a>

        <?php endfor; ?>

    </nav>

<?php endif; ?>

