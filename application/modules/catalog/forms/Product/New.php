<?php

class Catalog_Form_Product_New extends Catalog_Form_Product_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Добавить'));

        $image = new Zend_Form_Element_Hidden('image_to_download');
        $this->addElement($image);
        
        return $this;
    }

}