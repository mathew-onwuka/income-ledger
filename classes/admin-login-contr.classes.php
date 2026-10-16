<?php

class AdminLoginContr extends AdminLogin
{
    private $uid;
    private $pwd;

    public function __construct($uid, $pwd)
    {
        $this->uid = $uid;
        $this->pwd = $pwd;
    }

    public function loginAdmin()
    {
        if (
            empty($this->uid) ||
            empty($this->pwd)
        ) {
            header("location: ../admin-login.php?error=emptyinput");
            exit();
        }

        $this->getAdmin(
            $this->uid,
            $this->pwd
        );
    }
}