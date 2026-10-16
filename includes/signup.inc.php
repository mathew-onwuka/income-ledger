<?php

if(isset($_POST["submit"])){

    //Collect data from form
    $uid = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdrepeat"];
    $email = $_POST["email"];

    //Instantiate Signupcontroller class
    include "../classes/dbh.classes.php";
    include "../classes/signup.classes.php";
    include "../classes/signup-contr.classes.php";
    $signup = new SignupContr($uid, $pwd, $pwdRepeat, $email);

    //Running error handlers and user signup
    $signup->signupUser();

  //return to Dashboard page
    header("location: ../login.php?error=none");
}