<?php

class Users_Form_UserBase_PasswordRestore extends Zend_Form
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $login = new Zend_Form_Element_Text('login');
        $login->setLabel(_('Введите свой логин'))
              ->setRequired(true)
              ->addFilter('StringTrim');
        $this->addElement($login);

        $captcha = new Zend_Form_Element_Captcha('foo', array(
            'label' => _("Введите текст, расположенный ниже"),
            'captcha' => array(
                'captcha' => 'Figlet',
                'wordLen' => 4,
                'timeout' => 300,
            ),
        ));

        $this->addElement($captcha);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Отправить'))
            ->setIgnore(true);
        $this->addElement($submit);
    }
}