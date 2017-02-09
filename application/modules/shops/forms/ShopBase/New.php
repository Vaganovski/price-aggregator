<?php

class Shops_Form_ShopBase_New extends Shops_Form_ShopBase_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Добавить'));
        
        return $this;
    }

}