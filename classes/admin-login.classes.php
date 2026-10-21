<?php
require_once __DIR__ . '/activity-log.php';

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
            (new AdminAudit())->recordLogin('admin', null, $uid, false);
            header("location: ../admin-login.php?error=adminnotfound");
            exit();
        }

        if ($admin["account_status"] !== "active") {
            (new AdminAudit())->recordLogin(
                'admin',
                $admin['admin_id'],
                $admin['admin_uid'],
                false
            );
            header("location: ../admin-login.php?error=accountinactive");
            exit();
        }

        if (!password_verify($pwd, $admin["admin_pwd"])) {
            (new AdminAudit())->recordLogin(
                'admin',
                $admin['admin_id'],
                $admin['admin_uid'],
                false
            );
            header("location: ../admin-login.php?error=wrongpassword");
            exit();
        }

        session_start();
        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["admin_id"];
        $_SESSION["admin_uid"] = $admin["admin_uid"];
        $_SESSION["admin_role"] = $admin["admin_role"];

        $audit = new AdminAudit();
        $audit->recordLogin('admin', $admin['admin_id'], $admin['admin_uid'], true);
        $audit->recordActivity(
            $admin['admin_id'],
            $admin['admin_uid'],
            'admin_login',
            'Administrator signed in.'
        );

        $stmt = null;
    }
}