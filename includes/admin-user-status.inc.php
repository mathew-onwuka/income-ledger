<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("location: ../admin-login.php?error=notloggedin");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: ../admin/users.php");
    exit();
}

$adminId = $_SESSION["admin_id"];
$adminRole = $_SESSION["admin_role"];

$userid = (int)($_POST["userid"] ?? 0);
$action = $_POST["action"] ?? "";

if ($userid <= 0) {
    header("location: ../admin/users.php?error=invaliduser");
    exit();
}

include "../classes/dbh.classes.php";
include "../classes/admin.classes.php";
include "../classes/admin-contr.classes.php";

$admin = new AdminContr($adminId, $adminRole);

switch ($action) {

    case "activate":
        $admin->activateUserAccount($userid);
        break;

    case "deactivate":
        $admin->deactivateUserAccount($userid);
        break;

    case "suspend":
        $admin->suspendUserAccount($userid);
        break;

    case "delete":
        $admin->deleteUserAccount($userid);
        break;

    case "restore":
        $admin->restoreUserAccount($userid);
        break;

    default:
        header("location: ../admin/users.php?error=invalidaction");
        exit();
}

header("location: ../admin/users.php?error=none");
exit();