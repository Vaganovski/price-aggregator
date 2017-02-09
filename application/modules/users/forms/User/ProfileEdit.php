<?php

class Users_Form_User_ProfileEdit extends Users_Form_User_Edit
{
    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));
        $this->removeElement('login');
        $this->removeElement('password');
        $this->removeElement('password_confirm');
        $this->removeElement('captcha');
        $this->name->setLabel(_('Имя'));
        $this->removeElement('role');

        $this->city->setAttrib('class','width');

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => '/forms/profile-edit.phtml'))
        ));
        
        $this->getElement('submit')->setOrder(100)
                                   ->setLabel(_('Сохранить'))
                                   ->removeDecorator('Label');

        $id = new ZFEngine_Form_Element_Value('id');
        $this->addElement($id);
    }
}