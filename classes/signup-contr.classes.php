<?php
//Signup controller

class SignupContr extends Signup {

    private $uid;
    private $pwd;
    private $pwdRepeat;
    private $email;

    public function __construct($uid, $pwd, $pwdRepeat, $email) {
        $this->uid = $uid;
        $this->pwd = $pwd;
        $this->pwdRepeat = $pwdRepeat;
        $this->email = $email;
    }

    //Signup user by checking methods set below
    public function signupUser() {
        if($this->emptyInput() == false){
            //echo "Empty Input!";
            header("location: ../signup.php?error=emptyinput");
            exit();
        }
        if($this->invalidUid() == false){
            //echo "Invalid Username!";
            header("location: ../signup.php?error=invalidusername");
            exit();
        }
        if($this->invalidEmail() == false){
            //echo "Invalid Email!";
            header("location: ../signup.php?error=invalidemail");
            exit();
        }
        if($this->pwdMatch() == false){
            //echo "Passwords don't match!";
            header("location: ../signup.php?error=passwordmismatch");
            exit();
        }
        if($this->uidTakenCheck() == false){
            //echo "Username or email Taken!";
            header("location: ../signup.php?error=usernameoremailtaken");
            exit();
        }

        $this->setUser($this->uid, $this->pwd, $this->email);
    }

    //Create the methods below first..
    //Check if input is empty
    private function emptyInput() {
        $result;
        if(empty($this->uid) || empty($this->pwd) || empty($this->pwdRepeat) || empty($this->email)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    //Check if Username Choice is valid via reg expression
    private function invalidUid() {
        $result;
        if(!preg_match("/^[a-zA-Z0-9]*$/", $this->uid)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    //Check if email is valid
       private function invalidEmail() {
        $result;
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    //Check if the two passwords match
       private function pwdMatch() {
        $result;
        if($this->pwd !== $this->pwdRepeat) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

      //Check if the username is taken
       private function uidTakenCheck() {
        $result;
        if (!$this->checkUser($this->uid, $this->email)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }
}