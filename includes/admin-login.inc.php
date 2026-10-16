<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: ../admin-login.php");
    exit();
}

$uid = trim($_POST["admin_uid"]);
$pwd = $_POST["admin_pwd"];

include "../classes/dbh.classes.php";
include "../classes/admin-login.classes.php";
include "../classes/admin-login-contr.classes.php";

$admin = new AdminLoginContr(
    $uid,
    $pwd
);

$admin->loginAdmin();

header("location: ../admin-user/dashboard.php");

exit();