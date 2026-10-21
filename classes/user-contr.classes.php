<?php

class UserContr extends User{

    private $userid;
    public function __construct($userid)
    {
        $this->userid = $userid;
    }

    public function updateUserProfile($firstname, $lastname, $phone, $address, $profile_photo, $gender, $date_of_birth, $city, $state, $country, $postal_code)
    {
        if($this->emptyInput($firstname, $lastname, $phone, $address, $gender, $date_of_birth, $city, $state, $country, $postal_code))
        {
            header("location: view_user.php?error=emptyinput");
            exit();

        }
        return $this->updateProfile($firstname, $lastname, $phone, $address, $profile_photo, $gender, $date_of_birth, $city, $state, $country, $postal_code, $this->userid);
    }

    private function emptyInput($firstname, $lastname, $phone, $address, $gender, $date_of_birth, $city, $state, $country, $postal_code)
    {
        return
        empty($firstname) || empty($lastname) || empty($phone) || empty($address) || empty($gender) || empty($date_of_birth) || empty($city) || empty($state) || empty($country) || empty($postal_code);
    }


    public function changePassword($currentPassword, $newPassword, $confirmPassword)
    {

        if(
            empty($currentPassword) ||
            empty($newPassword) ||
            empty($confirmPassword)
        ){
            header("location: change-password.php?error=emptyinput");
            exit();
        }

        if($newPassword !== $confirmPassword){
            header("location: change-password.php?error=passwordmismatch");
            exit();
        }

        $user = $this->getPassword($this->userid);

        if(
            !password_verify(
                $currentPassword,
                $user["users_password"]
            )
        ){
            header("location: change-password.php?error=wrongpassword");
            exit();
        }

        return $this->updatePassword(
            $this->userid,
            $newPassword
        );
    }

    public function deleteUserAccount($password)
    {
        $user = $this->getPassword($this->userid);

        if(!password_verify($password, $user["users_pwd"]))
        {
            header("location:view-user.php?error=wrongpassword");
            exit();
        }

        return $this->deleteAccount($this->userid);
    }
}