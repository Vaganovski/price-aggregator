<?php

class Users_Form_UserBase_Edit extends Zend_Form
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel(_('Email'))
              ->setAttrib('disable', true)
              ->setIgnore(true);
        $this->addElement($email);

        $email = new Zend_Form_Element_Text('login');
        $email->setLabel(_('Логин'))
              ->setAttrib('disable', true)
              ->setIgnore(true);
        $this->addElement($email);

        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true);
        $submit->setLabel(_('Сохранить'));

        $this->addElement($submit);

    }
    
}