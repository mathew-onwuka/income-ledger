<?php

class UserView extends User{

    public function showAccountStatus($userid)
    {
        return $this->getAccountStatus($userid);
    }

     public function showProfile($userid)
    {
        return $this->getProfile($userid);
    }

}