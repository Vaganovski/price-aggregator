<?php

class Shops_Form_Shop_SearchAdmin extends Catalog_Form_Product_Search
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
            array('ViewScript', array('viewScript' => '/shops/views/scripts/index/forms/search-admin.phtml'))
        ));
        return $this;
    }
}