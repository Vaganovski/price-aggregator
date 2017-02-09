<?php

class Users_Form_UserBase_PasswordReset extends Zend_Form
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel(_('Новый пароль'))
                 ->setRequired(true)
                 ->setValue(null)
                 ->addValidator('StringLength', false,
                                array(Users_Model_User::MIN_PASSWORD_LENGTH));
        $passwordConfirm = new Zend_Form_Element_Password('password_confirm');
        $passwordConfirm->setLabel(_('Потверждение пароля'))
                     ->setRequired(true)
                        ->setValue(null);
        $password->addValidator(new ZFEngine_Validate_InputEquals($passwordConfirm->getName(),
                                                             _('Новый пароль и подтверждение пароля не совпадают')));
        $this->addElements(array($password, $passwordConfirm));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Оправить'))
            ->setIgnore(true);
        $this->addElement($submit);
    }
}