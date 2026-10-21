<?php

if(isset($_POST["submit"])){

    require_once __DIR__ . "/../classes/dbh.classes.php";
    require_once __DIR__ . "/../classes/settings.php";
    $registration = (new Settings())->getRegistrationStatus();
    if ($registration['available'] && $registration['status'] !== 'active') {
        header("location: ../signup.php?error=registrationclosed");
        exit();
    }

    //Collect data from form
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdrepeat"];
    $email = $_POST["email"];

    //Instantiate Signupcontroller class
    require_once __DIR__ . "/../classes/signup.classes.php";
    require_once __DIR__ . "/../classes/signup-contr.classes.php";
    $signup = new SignupContr($uid, $pwd, $pwdRepeat, $email);

    //Running error handlers and user signup
    $signup->signupUser();

  //return to Dashboard page
    header("location: ../login.php?error=none");
}