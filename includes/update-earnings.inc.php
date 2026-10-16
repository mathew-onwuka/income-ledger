<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['userid'])) {
        header("Location: ../login.php");
        exit();
    }

    if (!isset($_POST['earnings_id']) || !is_numeric($_POST['earnings_id'])) {
        header("Location: ../view-earnings.php?error=invalidid");
        exit();
    }

    $userId = $_SESSION["userid"];
    $earnings_id = (int)$_POST['earnings_id'];

    $earnings_description = trim($_POST['earnings_description']);
    $earnings_title = trim($_POST['earnings_title']);
    $earnings_amount = trim($_POST['earnings_amount']);
    $reg_date = trim($_POST['reg_date']);

    include "../classes/dbh.classes.php";
    include "../classes/earnings.classes.php";
    include "../classes/earnings-contr.classes.php";

    $earnings = new EarningsContr($userId, $earnings_id);

    $result = $earnings->updateEarningsData(
        $earnings_description,
        $earnings_title,
        $earnings_amount,
        $reg_date
    );

    if (!$result) {
        header("Location: ../view-earnings.php?error=updatefailed");
        exit();
    }

    header("Location: ../view-earnings.php?error=none");
    exit();
}
else {
    header("Location: ../view-earnings.php");
    exit();
}