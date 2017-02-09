<?php

class Catalog_Form_Brand_Edit extends Catalog_Form_Brand_Abstract
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