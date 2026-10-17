<?php

require_once __DIR__ . "/user-auth.inc.php";

include_once "../classes/dbh.classes.php";
include_once "../classes/user.classes.php";
include_once "../classes/user-contr.classes.php";

$user = new UserContr($_SESSION["userid"]);

$user->changePassword(
    $_POST["current_password"],
    $_POST["new_password"],
    $_POST["confirm_password"]
);

header("location: ../change-password.php?success=1");

exit();