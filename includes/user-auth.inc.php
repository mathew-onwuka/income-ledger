<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$scriptDirectory = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? ""));
if (basename($scriptDirectory) === "includes") {
    $scriptDirectory = dirname($scriptDirectory);
}
$loginPath = rtrim($scriptDirectory, "/") . "/login.php";

if (!isset($_SESSION["userid"])) {
    header("Location: " . $loginPath . "?error=notloggedin");
    exit();
}

require_once __DIR__ . "/../classes/dbh.classes.php";
require_once __DIR__ . "/../classes/user.classes.php";
require_once __DIR__ . "/../classes/user-view.classes.php";

$userView = new UserView();
$accountStatus = $userView->showAccountStatus($_SESSION["userid"]);

$statusErrors = [
    "inactive" => "accountinactive",
    "suspended" => "accountsuspended",
    "deleted" => "accountdeleted",
];

if ($accountStatus !== "active") {
    $error = is_string($accountStatus)
        ? ($statusErrors[$accountStatus] ?? "invalidaccountstatus")
        : "invalidaccountstatus";

    unset(
        $_SESSION["userid"],
        $_SESSION["useruid"],
        $_SESSION["useremail"],
        $_SESSION["firstname"],
        $_SESSION["lastname"],
        $_SESSION["profile_photo"]
    );
    session_destroy();

    header("Location: " . $loginPath . "?error=" . $error);
    exit();
}
