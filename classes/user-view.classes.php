<?php

class UserView extends User{

     public function showProfile($userid)
    {
        return $this->getProfile($userid);
    }

}