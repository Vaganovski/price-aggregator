<?php

abstract class Catalog_Form_Category_Abstract extends Categories_Form_Category_Abstract
{
  

   /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->setEnctype('multipart/form-data');
        
        $subFormCategoryAccessoriesWrapper = new Zend_Form_SubForm();
        $subFormCategoryAccessories = new Zend_Form_SubForm();
        $accesories = new Zend_Form_Element_Select('0');
        $accesories->setLabel(_('Категории аксессуаров'));
        $accesories->setAttrib('id', 'accessory_0_1');
        $subFormCategoryAccessories->addElement($accesories);
        $subFormCategoryAccessoriesWrapper->addSubForm($subFormCategoryAccessories, '0');
        $this->addSubForm($subFormCategoryAccessoriesWrapper, 'accessory');

        $features = new Zend_Form_Element_Select('features_group_id');
        $features->setLabel(_('Шаблон характеристик'));
        $this->addElement($features);

        $imageFilename = new ZFEngine_Form_Element_ImageFile('image_filename');
        $imageFilename->setLabel(_('Иконка категории'))
            ->addValidator('IsImage')
            ->setDestination($this->_serviceLayer->getModel()->getImageAbsoluteUploadPath())
            ->addValidator('Size', false, 1048576)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $uniqOpt = array('targetDir' => $this->_serviceLayer->getModel()->getImageAbsoluteUploadPath(),
                         'nameLength' => 10);
        $imageFilename->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
        $config['width'] = 32;
        $config['height'] = 32;//Zend_Registry::get('config')->products->images->original;
        $this->addElement($imageFilename);

        $this->getElement('short_description')->setLabel(_('Ключевые слова'));
        $this->removeElement('description');
        $this->title->setLabel(_('Название категории'));

        $this->category->removeDecorator('Fieldset');
        $this->category->removeDecorator('HtmlTag');
        $this->category->removeDecorator('DtDdWrapper');
        $this->category->setElementDecorators(array(
            'ViewHelper'
        ));

        $this->setAttrib('dataFromModel', array(
            'image_url' => $this->_serviceLayer->getModel()->image_url
        ));
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $imageFilename->setDecorators(array('File'));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/catalog/views/scripts/categories/forms/abstract.phtml'))
        ));
        return $this;
    }

    public function setDefaults(array $defaults) {
        if (array_key_exists('features_group_id', $defaults) && is_array($defaults['features_group_id'])) {
            $this->features_group_id->addMultiOptions($defaults['features_group_id']);
        }
        parent::setDefaults($defaults);
    }
}