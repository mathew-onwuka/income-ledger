<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("location: ../admin-login.php?error=notloggedin");
    exit();
}

include "../classes/dbh.classes.php";
include "../classes/admin.classes.php";
include "../classes/admin-view.classes.php";

$adminView = new AdminView();


// --------------------------------------------------
// Pagination
// --------------------------------------------------

$limit = 20;

$page = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$offset = ($page - 1) * $limit;


// --------------------------------------------------
// Search and Filter
// --------------------------------------------------

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');


// --------------------------------------------------
// Fetch Users
// --------------------------------------------------

if ($search !== '') {

    $users = $adminView->showSearchUsers(
        $search,
        $offset,
        $limit
    );

    $totalUsers = $adminView->showTotalSearchUsers($search);

} elseif (
    in_array(
        $status,
        ['active', 'inactive', 'suspended', 'deleted'],
        true
    )
) {

    $users = $adminView->showUsersByStatus(
        $status,
        $offset,
        $limit
    );

    $totalUsers = $adminView->showTotalUsersByStatus($status);

} else {

    $users = $adminView->showUsers(
        $offset,
        $limit
    );

    $totalUsers = $adminView->showTotalUsers();
}


// --------------------------------------------------
// Calculate Pages
// --------------------------------------------------

$totalPages = ceil($totalUsers / $limit);

$statusActionErrors = [
    'invalidtransition' => 'That account status change is not allowed. Refresh the page and try again.',
    'invaliduser' => 'The selected user could not be found.',
    'invalidaction' => 'The requested account status action is invalid.',
    'stmtfailed' => 'The account status could not be updated. Please try again.',
];
$statusActionErrorCode = $_GET['error'] ?? '';
$statusActionError = is_string($statusActionErrorCode)
    ? ($statusActionErrors[$statusActionErrorCode] ?? null)
    : null;

?>



<!-- ==================================================
     SEARCH AND FILTER
================================================== -->

<?php if ($statusActionError !== null): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($statusActionError, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div>

    <!-- Search User -->

    <form method="GET" action="users.php">

        <input
            type="text"
            name="search"
            placeholder="Search users..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            Search
        </button>

    </form>


    <!-- Status Filter -->

    <form method="GET" action="users.php">

        <select name="status">

            <option value="">
                All Users
            </option>

            <option
                value="active"
                <?php echo $status === 'active' ? 'selected' : ''; ?>
            >
                Active
            </option>

            <option
                value="inactive"
                <?php echo $status === 'inactive' ? 'selected' : ''; ?>
            >
                Inactive
            </option>

            <option
                value="suspended"
                <?php echo $status === 'suspended' ? 'selected' : ''; ?>
            >
                Suspended
            </option>

            <option
                value="deleted"
                <?php echo $status === 'deleted' ? 'selected' : ''; ?>
            >
                Deleted
            </option>

        </select>

        <button type="submit">
            Filter
        </button>

    </form>

</div>



<!-- ==================================================
     USERS TABLE
================================================== -->

<table>

    <thead>

        <tr>

            <th>ID</th>

            <th>Name</th>

            <th>Username</th>

            <th>Email</th>

            <th>Phone</th>

            <th>Country</th>

            <th>Status</th>

            <th>Registered</th>

            <th>View</th>

            <th>Account Action</th>

        </tr>

    </thead>


    <tbody>

        <?php if (!empty($users)): ?>

            <?php foreach ($users as $user): ?>

                <tr>

                    <!-- User ID -->

                    <td>
                        <?php echo $user['users_id']; ?>
                    </td>


                    <!-- Name -->

                    <td>

                        <?php

                        $fullname = trim(
                            ($user['firstname'] ?? '') . ' ' .
                            ($user['lastname'] ?? '')
                        );

                        echo htmlspecialchars(
                            $fullname ?: 'No Name'
                        );

                        ?>

                    </td>


                    <!-- Username -->

                    <td>

                        <?php echo htmlspecialchars(
                            $user['users_uid']
                        ); ?>

                    </td>


                    <!-- Email -->

                    <td>

                        <?php echo htmlspecialchars(
                            $user['users_email']
                        ); ?>

                    </td>


                    <!-- Phone -->

                    <td>

                        <?php echo htmlspecialchars(
                            $user['phone'] ?? ''
                        ); ?>

                    </td>


                    <!-- Country -->

                    <td>

                        <?php echo htmlspecialchars(
                            $user['country'] ?? ''
                        ); ?>

                    </td>


                    <!-- Account Status -->

                    <td>

                        <?php echo htmlspecialchars(
                            $user['account_status']
                        ); ?>

                    </td>


                    <!-- Registration Date -->

                    <td>

                        <?php echo htmlspecialchars(
                            $user['created_at']
                        ); ?>

                    </td>


                    <!-- View User -->

                    <td>

                        <a
                            href="view-user.php?id=<?php echo $user['users_id']; ?>"
                        >
                            View
                        </a>

                    </td>


                    <!-- Account Actions -->

                    <td>


                        <?php
                        /*
                        |--------------------------------------------------------------------------
                        | ACTIVE USER
                        |--------------------------------------------------------------------------
                        */
                        ?>

                        <?php if ($user['account_status'] === 'active'): ?>

                            <!-- Suspend -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="suspend"
                                >

                                <button type="submit">
                                    Suspend
                                </button>

                            </form>


                            <!-- Deactivate -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="deactivate"
                                >

                                <button type="submit">
                                    Deactivate
                                </button>

                            </form>


                        <?php
                        /*
                        |--------------------------------------------------------------------------
                        | INACTIVE USER
                        |--------------------------------------------------------------------------
                        */
                        ?>

                        <?php elseif ($user['account_status'] === 'inactive'): ?>

                            <!-- Activate -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="activate"
                                >

                                <button type="submit">
                                    Activate
                                </button>

                            </form>


                            <!-- Suspend -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="suspend"
                                >

                                <button type="submit">
                                    Suspend
                                </button>

                            </form>


                        <?php
                        /*
                        |--------------------------------------------------------------------------
                        | SUSPENDED USER
                        |--------------------------------------------------------------------------
                        */
                        ?>

                        <?php elseif ($user['account_status'] === 'suspended'): ?>

                            <!-- Activate -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="activate"
                                >

                                <button type="submit">
                                    Activate
                                </button>

                            </form>


                            <!-- Deactivate -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="deactivate"
                                >

                                <button type="submit">
                                    Deactivate
                                </button>

                            </form>


                        <?php
                        /*
                        |--------------------------------------------------------------------------
                        | DELETED USER
                        |--------------------------------------------------------------------------
                        */
                        ?>

                        <?php elseif ($user['account_status'] === 'deleted'): ?>

                            <!-- Restore -->

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="restore"
                                >

                                <button type="submit">
                                    Restore
                                </button>

                            </form>

                        <?php endif; ?>


                        <!-- Delete -->

                        <?php if ($user['account_status'] !== 'deleted'): ?>

                            <form
                                action="../includes/admin-user-status.inc.php"
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="userid"
                                    value="<?php echo $user['users_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <button type="submit">
                                    Delete
                                </button>

                            </form>

                        <?php endif; ?>


                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td colspan="10">
                    No users found.
                </td>

            </tr>

        <?php endif; ?>

    </tbody>

</table>



<!-- ==================================================
     PAGINATION
================================================== -->

<?php if ($totalPages > 1): ?>

    <div>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <?php

            $query = http_build_query([
                'page' => $i,
                'search' => $search,
                'status' => $status
            ]);

            ?>

            <a href="?<?php echo $query; ?>">

                <?php echo $i; ?>

            </a>

        <?php endfor; ?>

    </div>

<?php endif; ?>