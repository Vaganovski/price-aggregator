<?php

abstract class Catalog_Form_Product_Abstract extends Products_Form_Product_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $subFormCategory = new Zend_Form_SubForm();
        $category = new Zend_Form_Element_Select('0');
        $category->setLabel(_('Категория'))
                 ->setAttrib('id', 'category_1')
                 ->setRequired();
        $subFormCategory->addElement($category);
        $this->addSubForm($subFormCategory, 'category');

        $brand = new Zend_Form_Element_Select('brand_id');
        $brand->addMultiOption('', _('Выберите...'))
              ->setLabel(_('Производитель'))
              ->setRequired();
        $this->addElement($brand);
        
        $this->getView()->headScript()->appendFile('/javascripts/jq-cascade.js');

        parent::init();
        return $this;
    }

    /**
     * Set default values for elements
     *
     * Sets values for all elements specified in the array of $defaults.
     *
     * @param  array $defaults
     * @return Zend_Form
     */
    public function setDefaults($defaults) {
        if (array_key_exists('categories', $defaults)) {
           $this->getSubForm('category')
                ->getElement('0')
                ->addMultiOptions($defaults['categories']->findAll()->toKeyValueArray('id', 'title'));
           unset ($defaults['categories']);
        }
        if (array_key_exists('brands', $defaults)) {
           $this->brand_id->addMultiOptions($defaults['brands']->findAll()->toKeyValueArray('id', 'name'));
           unset ($defaults['brands']);
        }
        return parent::setDefaults($defaults);
    }

}