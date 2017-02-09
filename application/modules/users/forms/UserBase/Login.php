<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * User authorize form
 */
class Users_Form_UserBase_Login extends Zend_Form
{
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $this->setMethod('post');
        $this->setAction($this->getView()->url(array(
                'module' => 'users',
                'controller' => 'index',
                'action' => 'login'),
            'default', true));
        
        $login = new Zend_Form_Element_Text('login');
        $login->setLabel(_('Логин или Email'))
                 ->setRequired(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('EmailAddress', true);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel(_('Пароль'))
                 ->setRequired(true)
                 ->setValue(null)
                 ->addValidator('StringLength', false,
                                array(Users_Model_User::MIN_PASSWORD_LENGTH));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Войти'));

        $this->addElements(array($login, $password, $submit));

        return $this;
    }
}