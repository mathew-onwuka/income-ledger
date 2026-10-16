<?php

class AdminLogin extends Dbh
{
    protected function getAdmin($uid, $pwd)
    {
        $stmt = $this->connect()->prepare("SELECT * FROM admins WHERE (admin_uid = ? OR admin_email = ?) LIMIT 1");

        if (!$stmt->execute([$uid, $uid])) {
            header("location: ../admin-login.php?error=stmtfailed");
            exit();
        }

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            header("location: ../admin-login.php?error=adminnotfound");
            exit();
        }

        if ($admin["account_status"] !== "active") {
            header("location: ../admin-login.php?error=accountinactive");
            exit();
        }

        if (!password_verify($pwd, $admin["admin_pwd"])) {
            header("location: ../admin-login.php?error=wrongpassword");
            exit();
        }

        session_start();

        $_SESSION["admin_id"] = $admin["admin_id"];
        $_SESSION["admin_uid"] = $admin["admin_uid"];
        $_SESSION["admin_role"] = $admin["admin_role"];

        $stmt = null;
    }
}