<?php

abstract class Marketplace_Form_Product_Abstract extends Products_Form_Product_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setEnctype('multipart/form-data');
        $subFormCategory = new Zend_Form_SubForm();
        $category = new Zend_Form_Element_Select('0');
        $category->setLabel(_('Категория'))
                 ->setAttrib('id', 'category_1')
                 ->setAttrib('class', 'width')
                 ->setRequired();
        $subFormCategory->addElement($category);
        $subFormCategory->removeDecorator('Fieldset');
        $subFormCategory->removeDecorator('HtmlTag');
        $subFormCategory->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->addSubForm($subFormCategory, 'category');

        $type = new Zend_Form_Element_Select('type');
        $type->addMultiOptions(array(''=> _('Выберите...'),'buy'=>'куплю', 'sell' => 'продам'))
              ->setLabel(_('Тип объявления'))
              ->setRequired();
        $this->addElement($type);

        $price = new Zend_Form_Element_Text('price');
        $price->setRequired()
              ->setLabel(_('Цена'))
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('Float');
        $this->addElement($price);
        $this->getView()->headScript()->appendFile('/javascripts/jq-cascade.js');
        
        parent::init();


        $this->type->setAttrib('class', 'width');
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->image_filename->setDecorators(array('File'));
        $this->image_filename->setAttrib('class', 'baraholkaMyListItemWidePhotoinputFile')
                             ->setAttrib('onchange', 'document.getElementById(&quot;fileName&quot;).value=this.value')
                             ->setAttrib('size', '33');
        $this->description->setRequired();
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/abstract.phtml'))
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