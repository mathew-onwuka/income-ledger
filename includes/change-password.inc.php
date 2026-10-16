<?php

session_start();

if(!isset($_SESSION["userid"])){
    header("location: ../login.php");
    exit();
}

include "../classes/dbh.classes.php";
include "../classes/user.classes.php";
include "../classes/user-contr.classes.php";

$user = new UserContr($_SESSION["userid"]);

$user->changePassword(
    $_POST["current_password"],
    $_POST["new_password"],
    $_POST["confirm_password"]
);

header("location: ../change-password.php?success=1");

exit();