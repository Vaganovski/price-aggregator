<?php

class Catalog_Form_Review_New extends Catalog_Form_Review_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Оценить'));
        
        return $this;
    }

}