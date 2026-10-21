<?php

class User extends Dbh{

    protected function getAccountStatus($userid)
    {
        $stmt = $this->connect()->prepare("SELECT account_status FROM users WHERE users_id = ?");

        if (!$stmt->execute([$userid])) {
            $stmt = null;
            throw new RuntimeException("Unable to retrieve user account status.");
        }

        $status = $stmt->fetchColumn();
        $stmt = null;

        return $status === false ? null : $status;
    }

    protected function getProfile($userid)
    {
        $stmt = $this->connect()->prepare("SELECT firstname, lastname, phone, user_address, profile_photo, gender, date_of_birth, city, user_state, country, postal_code, users_email FROM users WHERE users_id = ?");

        if(!$stmt->execute([$userid])){
            header("location: view-user.php?error=stmtfailed");
            exit();
        }
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;
        return $profile;
    }

   protected function updateProfile($firstname, $lastname, $phone, $address, $profile_photo, $gender, $date_of_birth, $city, $state, $country,$postal_code, $userid)
   {
        $stmt = $this->connect()->prepare("UPDATE users SET firstname=?, lastname=?, phone=?, user_address=?, profile_photo=?, gender=?,  date_of_birth=?, city=?, user_state=?, country=?, postal_code=? WHERE users_id=?");

        if(!$stmt->execute([$firstname, $lastname, $phone, $address, $profile_photo, $gender, $date_of_birth, $city, $state, $country, $postal_code, $userid])){

        header("location: view-user.php?error=stmtfailed");
        exit();
        }
        $result = $stmt->rowCount() > 0;
        $stmt = null;
        return $result;

    } 

    protected function updatePassword($userid, $newPassword)
    {
        $stmt = $this->connect()->prepare("UPDATE users SET users_pwd = ? WHERE users_id = ?");

        if(!$stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userid]))
        {
            header("location: change-password.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
        return true;
    }

    protected function getPassword($userid)
    {
        $stmt = $this->connect()->prepare("SELECT users_pwd FROM users WHERE users_id = ?");

        $stmt->execute([$userid]);
        $password = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = null;

        return $password;
    }


    protected function deleteAccount($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users SET account_status='deleted', deleted_at=NOW() WHERE users_id=?");

        if(!$stmt->execute([$userid])){
            header("location:user.php?error=stmtfailed");
            exit();
        }

        return $stmt->rowCount() > 0;
    }

    
    protected function suspendUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users SET account_status='suspended', suspended_at=NOW() WHERE users_id=?");

        $stmt->execute([$userid]);

        return $stmt->rowCount()>0;
    }


    protected function activateUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users SET account_status='active', activated_at=NOW() WHERE users_id=?");

        $stmt->execute([$userid]);

        return $stmt->rowCount()>0;
    }

    protected function deactivateUser($userid)
    {
        $stmt = $this->connect()->prepare("UPDATE users SET account_status='inactive' WHERE users_id=?");

        $stmt->execute([$userid]);

        return $stmt->rowCount() > 0;
    }
        
}