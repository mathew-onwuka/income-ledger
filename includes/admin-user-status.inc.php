<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("location: ../admin-login.php?error=notloggedin");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: ../admin-user/users.php");
    exit();
}

$adminId = $_SESSION["admin_id"];
$adminRole = $_SESSION["admin_role"];

$userid = (int)($_POST["userid"] ?? 0);
$action = $_POST["action"] ?? "";

if ($userid <= 0) {
    header("location: ../admin-user/users.php?error=invaliduser");
    exit();
}

include "../classes/dbh.classes.php";
include "../classes/admin.classes.php";
include "../classes/admin-contr.classes.php";

$admin = new AdminContr($adminId, $adminRole);

switch ($action) {

    case "activate":
        $updated = $admin->activateUserAccount($userid);
        break;

    case "deactivate":
        $updated = $admin->deactivateUserAccount($userid);
        break;

    case "suspend":
        $updated = $admin->suspendUserAccount($userid);
        break;

    case "delete":
        $updated = $admin->deleteUserAccount($userid);
        break;

    case "restore":
        $updated = $admin->restoreUserAccount($userid);
        break;

    default:
        header("location: ../admin-user/users.php?error=invalidaction");
        exit();
}

if (!$updated) {
    header("location: ../admin-user/users.php?error=invalidtransition");
    exit();
}

header("location: ../admin-user/users.php?error=none");
exit();
