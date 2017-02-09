<?php

abstract class Products_Form_Product_Abstract extends Zend_Form
{
    protected $_serviceLayer;
    /**
     * @param object $_serviceLayer
     * @param array $options
     */
    function  __construct($_serviceLayer, $options = null) {
        $this->_serviceLayer = $_serviceLayer;
        parent::__construct($options);
    }

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));
 
        $title = new Zend_Form_Element_Text('name');
        $title->setLabel(_('Название'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($title);

        $imageFilename = new ZFEngine_Form_Element_ImageFile('image_filename');
        $imageFilename->setLabel(_('Изображение'))
            ->addValidator('IsImage')
            ->setDestination($this->_serviceLayer->getModel()->getImageAbsoluteUploadPath())
            ->addValidator('Size', false, 1048576)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $uniqOpt = array('targetDir' => $this->_serviceLayer->getModel()->getImageAbsoluteUploadPath(),
                         'nameLength' => 10);
        $imageFilename->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
        $config['width'] = 430;
        $config['height'] = 430;//Zend_Registry::get('config')->products->images->original;
        $imageFilename->addFilter(new ZFEngine_Filter_File_ImageResize(array('width' => $config['width'], 'height' =>$config['height'], 'saveProportions' =>true )));
        $this->addElement($imageFilename);

//        $shortDescription = new Zend_Form_Element_Textarea('short_description');
//        $shortDescription->setLabel(_('Краткое описание'))
//                         ->addFilter('StringTrim');
//        $this->addElement($shortDescription);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel(_('Описание'))
            ->addFilter('StringTrim');
        $this->addElement($description);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        return $this;
    }
}