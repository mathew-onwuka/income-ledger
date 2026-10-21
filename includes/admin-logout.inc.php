<?php

session_start();

unset(
    $_SESSION["admin_id"],
    $_SESSION["admin_uid"],
    $_SESSION["admin_role"]
);

session_destroy();

header("location: ../admin-login.php?logout=1");

exit();