<?php

class Shops_Form_Manager_New extends Users_Form_User_Registration
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));

        $this->removeElement('captcha');
        $this->getElement('name')->setLabel(_('Ф.И.О.'));

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel(_('Телефон'))
              ->addFilter('StringTrim')
              ->addFilter('StripTags')
              ->removeDecorator('Label');
        $this->addElement($phone);

        $this->submit->setLabel(_('Добавить'))
            ->setDescription(_('На e-mail менеджера будет выслано письмо, в котором будут находиться данные для авторизации.'));

        $shopId = new Zend_Form_Element_Hidden('shop_id');
        $this->addElement($shopId);

        return $this;
    }

}