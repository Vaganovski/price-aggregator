<?php

class Marketplace_Form_Product_SearchUser extends Marketplace_Form_Product_Search
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
        $this->setAction($this->getView()->url(array(
                                        'module' => 'marketplace',
                                        'controller' => 'search',
                                        'action' => 'index'),
                                    'default', true));

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/marketplace/views/scripts/products/forms/search-user.phtml'))
        ));
        return $this;
    }
}