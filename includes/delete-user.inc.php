<?php

session_start();

include "../classes/dbh.classes.php";
include "../classes/user.classes.php";
include "../classes/user-contr.classes.php";

$user = new UserContr($_SESSION["userid"]);

$user->deleteUserAccount(
    $_POST["password"]
);

session_unset();

session_destroy();

header("location: ../index.php?accountdeleted=1");

exit();