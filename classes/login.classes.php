<?php
// Login Model
class Login extends Dbh {

    protected function getUser($uid, $pwd)
    {
        // Fetch the entire user record in one query
        $stmt = $this->connect()->prepare("SELECT * FROM users WHERE users_uid = ? OR users_email = ? LIMIT 1");

        $stmt->execute([$uid, $uid]);

        // Fetch single user
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // User not found
        if(!$user){
            header("location: ../login.php?error=usernotfound");
            exit();
        }

        //User Status Check
        switch($user["account_status"])
        {
            case "inactive":
                header("location: ../login.php?error=accountinactive");
                exit();

            case "suspended":
                header("location: ../login.php?error=accountsuspended");
                exit();

            case "deleted":
                header("location: ../login.php?error=accountdeleted");
                exit();

            case "active":
                break;
        }

        // Verify password
        if (!password_verify($pwd, $user["users_pwd"])) {
            $stmt = null;
            header("location: ../login.php?error=wrongpassword");
            exit();
        }

        // Password is correct
        session_start();

        $_SESSION["userid"] = $user["users_id"];
        $_SESSION["useruid"] = $user["users_uid"];

        // Optional (recommended)
        $_SESSION["useremail"] = $user["users_email"];
        $_SESSION["firstname"] = $user["firstname"] ?? "";
        $_SESSION["lastname"] = $user["lastname"] ?? "";
        $_SESSION["profile_photo"] = $user["profile_photo"] ?? "default.png";

        $stmt = null;
    }
}