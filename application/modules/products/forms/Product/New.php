<?php

class Products_Form_Product_New extends Products_Form_Product_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Добавить'));
        
        return $this;
    }

}