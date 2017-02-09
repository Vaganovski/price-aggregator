<?php

class Features_Form_Feature_Set_Edit extends Features_Form_Feature_Set_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->getElement('submit')->setLabel(_('Save'));
    
    }


}