<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once __DIR__ . "/user-auth.inc.php";

    if (!isset($_POST['earnings_id']) || !is_numeric($_POST['earnings_id'])) {
        header("Location: ../earnings-list.php?error=invalidid");
        exit();
    }

    $userId = $_SESSION['userid'];
    $earnings_id = (int)$_POST['earnings_id'];

    include_once "../classes/dbh.classes.php";
    include "../classes/earnings.classes.php";
    include "../classes/earnings-contr.classes.php";

    $earnings = new EarningsContr($userId, $earnings_id);

    $result = $earnings->deleteEarningsData();

    if (!$result) {
        header("Location: ../earnings-list.php?error=deletefailed");
        exit();
    }

    header("Location: ../earnings-list.php?error=none");
    exit();
}
else {
    header("Location: ../earnings-list.php");
    exit();
}