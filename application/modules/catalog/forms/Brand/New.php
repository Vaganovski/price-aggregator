<?php

class Catalog_Form_Brand_New extends Catalog_Form_Brand_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Добавить'));
        
        return $this;
    }

}