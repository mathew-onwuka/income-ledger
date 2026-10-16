<?php
session_start();

require_once "../includes/admin-auth.inc.php";


if (!isset($_SESSION["admin_id"])) {
    header("location: ../admin-login.php?error=notloggedin");
    exit();
}

include "../classes/dbh.classes.php";
include "../classes/admin.classes.php";
include "../classes/admin-view.classes.php";

$adminView = new AdminView();

$userStats = $adminView->showUserStats();
$earningsStats = $adminView->showEarningsStats();

$newUsersToday = $adminView->showNewUsersToday();
$todayEarnings = $adminView->showTodayEarnings();
$monthEarnings = $adminView->showThisMonthEarnings();

//Fetch Users Data
$total_users = $userStats['total_users'];
$active_users = $userStats['active_users'];
$inactive_users = $userStats['inactive_users'];
$suspended_users = $userStats['suspended_users'];
$deleted_users = $userStats['deleted_users'];
$newUsersToday;

//Fetch Earnings Data
$total_earnings_records = $earningsStats['total_earnings_records'];
$total_earnings = $earningsStats['total_earnings'];
$average_earning = $earningsStats['average_earning'];
$highest_earning = $earningsStats['highest_earning'];
$lowest_earning = $earningsStats['lowest_earning'];
$todayEarnings;
$monthEarnings;


?>

 <h3>  
     <p>Total Users</p>
     <?php echo number_format($total_users); ?>
 </h3>

<h3>
    <p>Active Users</p>
    <?php echo number_format($active_users); ?>
</h3> 

<h3>
    <p>Total Earnings</p>
    <?php
    echo number_format(
        $earningsStats['total_earnings'],
        2
    );
    ?>
</h3>


<h3>
    <p>Number of Earnings</p>
    <?php
    echo number_format(
        $earningsStats['total_earnings_records']
    );
    ?>
</h3>


<h3>
    <p>Average Earning</p>
    <?php
    echo number_format(
        $earningsStats['average_earning'],
        2
    );
    ?>
</h3>

<h3>
    <p>Highest Earning</p>
    <?php
    echo number_format(
        $earningsStats['highest_earning'],
        2
    );
    ?>
</h3>

<h3>
    <p>Lowest Earning</p>
    <?php
    echo number_format(
        $earningsStats['lowest_earning'],
        2
    );
    ?>
</h3>
