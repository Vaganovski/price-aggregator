<?php

class Catalog_Form_Product_SearchByCategory extends Products_Form_Product_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $this->setMethod('get');
        $this->setAction($this->getView()->url(array(
                    'module'=> 'catalog',
                    'controller'=> 'products',
                    'action'=>'list-admin'
                ), 'default', true));

        $subFormCategory = new Zend_Form_SubForm();
        $category = new Zend_Form_Element_Select('0');
        $category->setLabel(_('Категория'))
                 ->setAttrib('id', 'category_1')
                 ->setRequired();
        $subFormCategory->addElement($category);
        $this->addSubForm($subFormCategory, 'category');
        $this->category->removeDecorator('Fieldset');
        $this->category->removeDecorator('HtmlTag');
        $this->category->removeDecorator('DtDdWrapper');
        $this->category->setElementDecorators(array(
            'ViewHelper'
        ));
        
        $this->getView()->headScript()->appendFile('/javascripts/jq-cascade.js');
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/catalog/views/scripts/products/forms/shearch-by-category.phtml'))
        ));
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
        return parent::setDefaults($defaults);
    }

}
