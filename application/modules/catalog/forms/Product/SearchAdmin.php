<?php

class Catalog_Form_Product_SearchAdmin extends Catalog_Form_Product_Search
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

//        $this->setAction($this->getView()->url(array(
//                                        'module' => 'catalog',
//                                        'controller' => 'search',
//                                        'action' => 'admin'),
//                                    'default', true));
//        $cities = array('' => 'Выберите...');
//        foreach ($this->getView()->GetCities() as $city){
//            $cities[$city] = $city;
//        }
//        $city = new Zend_Form_Element_Select('city');
//        $city->setLabel(_('Город'))
//            ->setRequired()
//            ->addMultiOptions($cities);
//        $city->setAttrib('class','width');
//        $this->addElement($city);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/catalog/views/scripts/products/forms/search-admin.phtml'))
        ));
        return $this;
    }
}