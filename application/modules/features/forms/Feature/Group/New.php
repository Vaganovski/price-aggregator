<?php

class Features_Form_Feature_Group_New extends Features_Form_Feature_Group_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Сохранить'));
        
        return $this;
    }

}