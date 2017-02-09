<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * User authorize form
 */
class Users_Form_User_Login extends Users_Form_UserBase_Login
{
    public function init()
    {
        $this->setName(strtolower(__CLASS__));
        parent::init();
        
        $this->login->removeValidator('EmailAddress');
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'users/views/scripts/index/forms/login.phtml'))
        ));

        $this->setElementDecorators(array('ViewHelper'));

        return $this;
    }
}