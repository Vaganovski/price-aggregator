<?php

class Users_Form_User_CreateUser extends Users_Form_User_Registration
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        $this->removeElement('captcha');

        $roles = Users_Model_User::getLocalizedRoles();
        unset($roles['guest']);
        $role = new Zend_Form_Element_Select('role');
        $role->setRequired()
             ->setLabel('Роль')
             ->setOrder(0)
             ->addMultiOptions($roles);
        $this->addElement($role);

        $this->getElement('name')->setLabel(_('Имя'));

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel(_('Телефон'))
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->removeDecorator('Label');
        $this->addElement($phone);
       

        $this->submit->setLabel(_('Создать'))
            ->setDescription(_('На e-mail пользователя будет отправлено письмо, в котором будут находиться данные для авторизации.'));

        $this->clearDecorators();
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            'FormErrors',
            'HtmlTag',
            'Label',
            'DtDdWrapper',
        ));

        return $this;
    }

}