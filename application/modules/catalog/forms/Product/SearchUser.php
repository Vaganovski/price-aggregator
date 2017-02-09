<?php

class Catalog_Form_Product_SearchUser extends Catalog_Form_Product_Search
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
                                        'module' => 'catalog',
                                        'controller' => 'search',
                                        'action' => 'index'),
                                    'default', true));
        $city = new Zend_Form_Element_Hidden('city');
        $city->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setValue(_('Алматы'));
        $this->addElement($city);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/catalog/views/scripts/products/forms/search-user.phtml'))
        ));
        return $this;
    }
}