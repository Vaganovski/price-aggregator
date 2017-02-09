<?php

abstract class Shops_Form_ChainShop_Abstract extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel(_('Название торговой сети'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($name);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);
    }
   
}