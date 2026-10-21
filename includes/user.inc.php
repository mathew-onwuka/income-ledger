<?php
require_once __DIR__ . "/user-auth.inc.php";


if($_SERVER["REQUEST_METHOD"]=="POST"){

    $userId = $_SESSION["userid"];

    include_once "../classes/dbh.classes.php";
    include_once "../classes/user.classes.php";
    include_once "../classes/user-view.classes.php";
    include_once "../classes/user-contr.classes.php";
    include_once "../classes/ImageUpload.php";

    // Fetch current user first
    $view = new UserView();
    $currentUser = $view->showProfile($userId);
   
    // Upload image
    $photoName = ImageUpload::uploadProfile(
    $_FILES["profile_photo"],
    $currentUser["profile_photo"]
    );


    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $gender = trim($_POST["gender"]);
    $date_of_birth = $_POST["date_of_birth"];
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $country = trim($_POST["country"]);
    $postal_code = trim($_POST["postal_code"]);


    // Update database
    $user = new UserContr($userId);

    $user->updateUserProfile($firstname, $lastname, $phone, $address, $photoName, $gender, $date_of_birth, $city, $state, $country, $postal_code);
    header("location: ../user.php?updated=1");
    exit(); 

}