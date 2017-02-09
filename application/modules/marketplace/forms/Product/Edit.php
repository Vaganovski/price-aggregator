<?php

class Marketplace_Form_Product_Edit extends Marketplace_Form_Product_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);

        $this->getElement('submit')->setLabel(_('Сохранить'));
    
    }


}