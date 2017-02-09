<?php

abstract class Marketplace_Form_Category_Abstract extends Categories_Form_Category_Abstract
{
  

   /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->getElement('short_description')->setLabel(_('Ключевые слова'));
        $this->removeElement('description');
        $this->title->setLabel(_('Название категории'));

        $this->category->removeDecorator('Fieldset');
        $this->category->removeDecorator('HtmlTag');
        $this->category->removeDecorator('DtDdWrapper');
        $this->category->setElementDecorators(array(
            'ViewHelper'
        ));

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/marketplace/views/scripts/categories/forms/abstract.phtml'))
        ));

        return $this;
    }

}