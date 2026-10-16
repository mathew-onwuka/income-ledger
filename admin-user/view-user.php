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
| Get User ID
|--------------------------------------------------------------------------
*/

$userid = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;

if ($userid <= 0) {
    header("location: users.php?error=invaliduser");
    exit();
}


/*
|--------------------------------------------------------------------------
| Get User
|--------------------------------------------------------------------------
*/

$user = $adminView->showUserById($userid);

if (!$user) {
    header("location: users.php?error=usernotfound");
    exit();
}


/*
|--------------------------------------------------------------------------
| Profile Photo
|--------------------------------------------------------------------------
*/

$profilePhoto = !empty($user['profile_photo'])
    ? $user['profile_photo']
    : 'default.png';


/*
|--------------------------------------------------------------------------
| Earnings Pagination
|--------------------------------------------------------------------------
*/

$limit = 20;

$page = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$offset = ($page - 1) * $limit;


/*
|--------------------------------------------------------------------------
| Earnings Date Filter
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
| Validate Custom Date Range
|--------------------------------------------------------------------------
*/

if (
    $filter === 'custom' &&
    !empty($startDate) &&
    !empty($endDate) &&
    $startDate > $endDate
) {
    $temp = $startDate;

    $startDate = $endDate;
    $endDate = $temp;
}


/*
|--------------------------------------------------------------------------
| Get Date Range
|--------------------------------------------------------------------------
*/

$dateRange = $adminContr->getDateRange(
    $filter,
    $startDate,
    $endDate
);


/*
|--------------------------------------------------------------------------
| Invalid Custom Date Range
|--------------------------------------------------------------------------
*/

if (
    $filter === 'custom' &&
    $dateRange === null
) {
    $filter = 'all';

    $startDate = null;
    $endDate = null;

    $dateRange = null;
}


/*
|--------------------------------------------------------------------------
| Get Earnings
|--------------------------------------------------------------------------
*/

if ($dateRange === null) {

    $earnings = $adminView->showUserEarnings(
        $userid,
        $offset,
        $limit
    );

    $totalEarningsRecords =
        $adminView->showTotalUserEarnings($userid);

} else {

    $earnings = $adminView->showUserEarningsByDate(
        $userid,
        $dateRange['start'],
        $dateRange['end'],
        $offset,
        $limit
    );

    $totalEarningsRecords =
        $adminView->showTotalUserEarningsByDate(
            $userid,
            $dateRange['start'],
            $dateRange['end']
        );
}


/*
|--------------------------------------------------------------------------
| Earnings Pagination
|--------------------------------------------------------------------------
*/

$totalPages = (int)ceil(
    $totalEarningsRecords / $limit
);


/*
|--------------------------------------------------------------------------
| User Earnings Statistics
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| User Earnings Statistics
|--------------------------------------------------------------------------
*/

if ($dateRange === null) {

    // Lifetime statistics
    $earningsStats =
        $adminView->showUserEarningsStats($userid);

} else {

    // Statistics for the selected date range
    $earningsStats =
        $adminView->showUserEarningsStatsByDate(
            $userid,
            $dateRange['start'],
            $dateRange['end']
        );
}

?>
<h2>User's fullname:
    <?php
    echo htmlspecialchars(
        trim(
            ($user['firstname'] ?? '') . ' ' .
            ($user['lastname'] ?? '')
        )
    );
    ?>
</h2>
<img
    src="../uploads/profile/<?php echo htmlspecialchars($profilePhoto); ?>"
    alt="Profile Photo"
>
<p>
    Username:
    <?php echo htmlspecialchars($user['users_uid']); ?>
</p>

<p>
    Email:
    <?php echo htmlspecialchars($user['users_email']); ?>
</p>

<p>
    Phone:
    <?php echo htmlspecialchars($user['phone'] ?? ''); ?>
</p>

<p>
    Gender:
    <?php echo htmlspecialchars($user['gender'] ?? ''); ?>
</p>

<p>
    Date of Birth:
    <?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>
</p>

<p>
    Address:
    <?php echo htmlspecialchars($user['address'] ?? ''); ?>
</p>

<p>
    City:
    <?php echo htmlspecialchars($user['city'] ?? ''); ?>
</p>

<p>
    State:
    <?php echo htmlspecialchars($user['state'] ?? ''); ?>
</p>

<p>
    Country:
    <?php echo htmlspecialchars($user['country'] ?? ''); ?>
</p>

<p>
    Postal Code:
    <?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>
</p>

<p>
    Account Status:
    <?php echo htmlspecialchars($user['account_status']); ?>
</p>

<p>
    Registered:
    <?php echo htmlspecialchars($user['created_at']); ?>
</p>

<!-- Date Range Filter  -->

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


<h2>User's Earnings</h2>

<form method="GET" action="view-user.php">

    <input
        type="hidden"
        name="id"
        value="<?php echo $userid; ?>"
    >

    <label for="filter">Filter Earnings</label>

    <select name="filter" id="filter">

        <option
            value="all"
            <?php echo $filter === 'all' ? 'selected' : ''; ?>
        >
            All Earnings
        </option>

        <option
            value="today"
            <?php echo $filter === 'today' ? 'selected' : ''; ?>
        >
            Today
        </option>

        <option
            value="yesterday"
            <?php echo $filter === 'yesterday' ? 'selected' : ''; ?>
        >
            Yesterday
        </option>

        <option
            value="week"
            <?php echo $filter === 'week' ? 'selected' : ''; ?>
        >
            This Week
        </option>

        <option
            value="month"
            <?php echo $filter === 'month' ? 'selected' : ''; ?>
        >
            This Month
        </option>

        <option
            value="year"
            <?php echo $filter === 'year' ? 'selected' : ''; ?>
        >
            This Year
        </option>

        <option
            value="custom"
            <?php echo $filter === 'custom' ? 'selected' : ''; ?>
        >
            Custom Date Range
        </option>

    </select>


    <div>

        <label for="start_date">
            Start Date
        </label>

        <input
            type="date"
            name="start_date"
            id="start_date"
            value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>"
        >


        <label for="end_date">
            End Date
        </label>

        <input
            type="date"
            name="end_date"
            id="end_date"
            value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>"
        >

    </div>


    <button type="submit">
        Apply Filter
    </button>

    <a href="view-user.php?id=<?php echo $userid; ?>">
        Clear Filter
    </a>

</form>
<table>
    <thead>

        <tr>

            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>

        </tr>

    </thead>

    <tbody>

        <?php if (!empty($earnings)): ?>

            <?php foreach ($earnings as $earning): ?>

                <tr>

                    <td>
                        <?php
                        echo $earning['earnings_id'];
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
                            $earning['earnings_amount'],
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

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="5">
                    No earnings records found.
                </td>

            </tr>

        <?php endif; ?>

    </tbody>

</table>
<?php if ($totalPages > 1): ?>

    <div>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <a href="?id=<?php echo $userid; ?>&filter=<?php echo urlencode($filter); ?>&start_date=<?php echo urlencode($startDate ?? ''); ?>&end_date=<?php echo urlencode($endDate ?? ''); ?>&page=<?php echo $i; ?>">

                <?php echo $i; ?>

            </a>

        <?php endfor; ?>

    </div>

<?php endif; ?>