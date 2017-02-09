<?php

class Categories_Form_Category_New extends Categories_Form_Category_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Create'));
        
        return $this;
    }

}