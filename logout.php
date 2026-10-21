<?php
session_start();
session_unset();
session_destroy();

//Go back to Login page
header("location: login.php?error=none");