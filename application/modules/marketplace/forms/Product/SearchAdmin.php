<?php

class Marketplace_Form_Product_SearchAdmin extends Marketplace_Form_Product_Search
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));


        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/marketplace/views/scripts/products/forms/search-admin.phtml'))
        ));
        return $this;
    }
}