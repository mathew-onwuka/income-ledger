<?php

if(isset($_POST["submit"])){

    //Collect data from form
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];


    //Instantiate Logincontroller class
    include "../classes/dbh.classes.php";
    include "../classes/login.classes.php";
    include "../classes/login-contr.classes.php";
    $login = new LoginContr($uid, $pwd);

    //Running error handlers and user login
    $login->loginUser();

    //return to Dashboard page
    header("location: ../index.php?error=none");
}