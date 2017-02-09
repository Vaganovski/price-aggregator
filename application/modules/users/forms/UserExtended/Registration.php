<?php

class Users_Form_UserExtended_Registration extends Zend_Form
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel(_('Email'))
              ->setRequired(true)
              ->addFilter('StringTrim')
              ->addValidator('EmailAddress', true);
        $this->addElement($email);

        $email = new Zend_Form_Element_Text('login');
        $email->setLabel(_('Login'))
              ->setRequired(true)
              ->addFilter('StringTrim')
                // @todo add regexp
              ->addValidator(new ZFEngine_Validate_Doctrine_NoRecordExist(
                      'Users_Model_User', 'login',
                      _('Пользователь с таким логином уже зарегистрирован'))
                  );
        $this->addElement($email);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel(_('Пароль'))
                 ->setRequired(true)
                 ->setValue(null)
                 ->addValidator('StringLength', false,
                                array(Users_Model_User::MIN_PASSWORD_LENGTH));
        $passwordConfirm = new Zend_Form_Element_Password('password_confirm');
        $passwordConfirm->setLabel(_('Подтверждение пароля:'))
                        ->setValue(null);
        $password->addValidator(new ZFEngine_Validate_InputEquals($passwordConfirm->getName(),
                                                             _('Пароль и подтверждение пароля не совпадают')));
        $this->addElements(array($password, $passwordConfirm));

        $role = new Zend_Form_Element_Hidden('role');
        $role->setValue('member');
        $this->addElement($role);
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Зарегистрироваться'))
            ->setIgnore(true)
            ->setOrder(100); // В самый конец формы
        $this->addElement($submit);


        return $this;
    }

}