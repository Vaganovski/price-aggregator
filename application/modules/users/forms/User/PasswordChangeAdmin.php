<?php

class Users_Form_User_PasswordChangeAdmin extends Users_Form_UserBase_PasswordReset
{

    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));
    }
}