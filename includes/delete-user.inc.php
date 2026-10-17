<?php

require_once __DIR__ . "/user-auth.inc.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit();
}

include_once "../classes/dbh.classes.php";
include_once "../classes/user.classes.php";
include_once "../classes/user-contr.classes.php";

$user = new UserContr($_SESSION["userid"]);

$user->deleteUserAccount(
    $_POST["password"]
);

session_unset();

session_destroy();

header("location: ../index.php?accountdeleted=1");

exit();