<?php

class Marketplace_Form_Category_New extends Marketplace_Form_Category_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Создать'));
        
        return $this;
    }

}