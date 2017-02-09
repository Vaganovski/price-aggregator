<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * User authorize form
 */
class Users_Form_User_MerchantLogin extends Users_Form_User_Login
{
    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));
        
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'users/views/scripts/index/forms/merchant-login.phtml'))
        ));

        $this->setElementDecorators(array('ViewHelper'));

        return $this;
    }
}