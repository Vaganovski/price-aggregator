<?php

class Shops_Form_Shop_Edit extends Shops_Form_Shop_Abstract
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

        $id = new ZFEngine_Form_Element_Value('id');
        $id->setDecorators(array('ViewHelper'));
        $this->addElement($id);

        $this->getElement('submit')->setLabel(_('Сохранить'));
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
        $shop = Doctrine::getTable($this->_serviceLayer->getModelName()) ->find($data['id']);
        if ($data['email'] == $shop->email) {
            $this->email->removeValidator('ZFEngine_Validate_Doctrine_NoRecordExist');
        }

        return parent::isValid($data);
    }
}