<?php

class Shops_Form_Shop_EditImage extends Zend_Form
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

        $imageFilename = new Zend_Form_Element_File('image_filename');
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

        $id = new ZFEngine_Form_Element_Value('id');
        $id->setDecorators(array("ViewHelper"));
        $this->addElement($id);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setLabel(_('Сохранить'))
               ->setOrder(100);
        $this->addElement($submit);
        
    }

    /**
     * Populate form
     *
     * Proxies to {@link setDefaults()}
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate(array $values) {
        if (array_key_exists('image_filename', $values)) {
            $this->getElement('image_filename')->setAttrib('old_image', $values['image_preview_url']);
            unset($values['image_filename']);
        }
        parent::populate($values);
    }
}