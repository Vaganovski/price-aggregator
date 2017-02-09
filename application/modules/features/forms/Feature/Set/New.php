<?php

class Features_Form_Feature_Set_New extends Features_Form_Feature_Set_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $this->submit->setLabel(_('Сохранить'));


        $groupId = new Zend_Form_Element_Hidden('features_group_id');
        $this->addElement($groupId);
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        return $this;
    }

}