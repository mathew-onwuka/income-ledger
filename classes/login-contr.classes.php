<?php

class LoginContr extends Login {

    private $uid;
    private $pwd;

    public function __construct($uid, $pwd) {
        $this->uid = $uid;
        $this->pwd = $pwd;
    }

    //login user by checking methods set below
    public function loginUser() {
        if($this->emptyInput() == false){
            //echo "Empty Input!";
            header("location: ../login.php?error=emptyinput");
            exit();
        }

        $this->getUser($this->uid, $this->pwd);
    }

    //Create the methods below first..
    //Check if input is empty
    private function emptyInput() {
        $result;
        if(empty($this->uid) || empty($this->pwd)) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

}