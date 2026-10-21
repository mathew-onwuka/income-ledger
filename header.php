<?php
require_once __DIR__ . "/includes/user-auth.inc.php";
$userId = $_SESSION["userid"];
$userUid = $_SESSION["useruid"];

//Users  
include_once "classes/user.classes.php";
include_once "classes/user-view.classes.php";

$viewUser = new UserView();
$user = $viewUser->showProfile($userId);


// Earnings   
include_once "classes/dbh.classes.php";
include_once "classes/earnings.classes.php";
include_once "classes/earnings-view.classes.php";
include_once "classes/settings.php";
$appSettings = (new Settings())->getAppSettings();
$appName = $appSettings['values']['app_name'];

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
	<link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title><?php echo htmlspecialchars((string)$page_title, ENT_QUOTES, 'UTF-8'); ?></title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Paper Dashboard core CSS    -->
    <link href="assets/css/paper-dashboard.css" rel="stylesheet"/>


    <!--  Fonts and icons     -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
    <link href="assets/css/themify-icons.css" rel="stylesheet">

    <!--  Premium "Vault" theme — visual overrides only  -->
    <link href="assets/css/premium-theme.css" rel="stylesheet">

   

</head>
<body>

<div class="wrapper">
    <div class="sidebar" data-background-color="black" data-active-color="danger">

    <!--
		Tip 1: you can change the color of the sidebar's background using: data-background-color="white | black"
		Tip 2: you can change the color of the active button using the data-active-color="primary | info | success | warning | danger"
	-->

    	<div class="sidebar-wrapper">
            <div class="logo">
                <a href="#" class="simple-text">
                    <?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </div>
            <div class="logo">
                <a href="#" class="simple-text">
                    Switch Mode: Light | Dark 
                </a>
            </div>
            <ul class="nav">
                <li>
                    <a href="#">
                        <i class="ti-panel"></i>
                        <img src="uploads/profile/<?=htmlspecialchars($user["profile_photo"]);?>" width="45" height="45" class="rounded-circle">
                        <p>Welcome, <?php echo $userUid; ?></p>
                    </a>
                </li>
                <li class="active">
                    <a href="index.php">
                        <i class="ti-panel"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li>
                    <a href="add-earnings.php">
                        <i class="ti-view-list-alt"></i>
                        <p>Add Earnings</p>
                    </a>
                </li>
                <li>
                    <a href="earnings-list.php">
                        <i class="ti-view-list-alt"></i>
                        <p>Earnings List</p>
                    </a>
                </li>
                <li>
                    <a href="search-earnings.php">
                        <i class="ti-view-list-alt"></i>
                        <p>Filter Earnings</p>
                    </a>
                </li>
                <li>
                    <a href="view-user.php">
                        <i class="ti-user"></i>
                        <p>User Profile</p>
                    </a>
                </li>
                <li>
                    <a href="change-password.php">
                        <i class="ti-view-list-alt"></i>
                        <p>Change Password</p>
                    </a>
                </li>
                <li>
                    <a href="delete-user.php">
                        <i class="ti-view-list-alt"></i>
                        <p>Terminate Account</p>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="ti-view-list-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
    	</div>
    </div>
    <div class="main-panel">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar bar1"></span>
                        <span class="icon-bar bar2"></span>
                        <span class="icon-bar bar3"></span>
                    </button>
                    <a class="navbar-brand" href="#">Dashboard</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="logout.php" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="ti-panel"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                        <li>
                            <a href="change-password.php">
                                <i class="ti-settings"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
