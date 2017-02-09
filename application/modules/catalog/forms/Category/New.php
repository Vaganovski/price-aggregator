<?php

class Catalog_Form_Category_New extends Catalog_Form_Category_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Create'));
        
        return $this;
    }

}