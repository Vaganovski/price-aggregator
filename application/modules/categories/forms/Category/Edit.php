<?php

class Categories_Form_Category_Edit extends Categories_Form_Category_Abstract
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

        $this->getElement('submit')->setLabel(_('Save'));
    
    }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        // @todo refact
        $category = Doctrine::getTable($this->_serviceLayer->getModelName()) ->find($data['id']);
        if ($data['alias'] == $category->alias) {
            $this->alias->removeValidator('ZFEngine_Validate_Doctrine_NoRecordExist');
        }

        return parent::isValid($data);
    }

}