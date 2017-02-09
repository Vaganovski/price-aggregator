<?php

class Reviews_Form_Review_New extends Reviews_Form_Review_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Добавить'));
        
        return $this;
    }

}