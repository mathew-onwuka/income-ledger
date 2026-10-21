<?php
//Signup Model

class Signup extends Dbh{

     //Create user account
    protected function setUser($uid, $pwd, $email) {
        $stmt = $this->connect()->prepare("INSERT INTO users (users_uid, users_pwd, users_email, account_status, activated_at) VALUES (?, ?, ?, 'active', NOW());");
        
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
        
        if(!$stmt->execute(array($uid, $hashedPwd, $email))) {
            $stmt = null;
            header("location: ../signup.php?error=stmtfailed");
            exit(); 
        }
        $stmt = null;
    }

    //Check if the username or email has been used..
    protected function checkUser($uid, $email) {
        $stmt = $this->connect()->prepare('SELECT users_uid FROM users WHERE users_uid = ? OR users_email = ?;');
        
        if(!$stmt->execute(array($uid, $email))) {
            $stmt = null;
            header("location: ../signup.php?error=stmtfailed");
            exit();
        }
        
        $resultCheck;
        if($stmt->rowCount() > 0) {
            $resultCheck = false;
        } else {
            $resultCheck = true;
        }
        return $resultCheck;
    }
}