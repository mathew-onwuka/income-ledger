<?php

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . "/user-auth.inc.php";

    $userId = $_SESSION["userid"];
    $earnings_description = trim($_POST['earnings_description']);
    $earnings_title = trim($_POST['earnings_title']);
    $earnings_amount = trim($_POST['earnings_amount']);
    $reg_date = trim($_POST['reg_date']);

    //Instantiate earnings classes
    include_once "../classes/dbh.classes.php";
    include "../classes/earnings.classes.php";
    include "../classes/earnings-contr.classes.php";

    ////Running error handlers and user signup
    $earnings = new EarningsContr($userId);
    $earnings->addEarnings($earnings_description, $earnings_title, $earnings_amount, $reg_date);

      //return to Dashboard page
    header("location: ../add-earnings.php?error=none");
    exit();
    
} else {
    header("Location: ../add-earnings.php");
    exit();
}
