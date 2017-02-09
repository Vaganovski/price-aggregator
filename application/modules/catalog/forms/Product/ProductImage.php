<?php

class Catalog_Form_Product_ProductImage extends Zend_Form
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

        $this->setAttrib('class', 'adminGoodsCharBigForm');
        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);

        $imageFilename = new ZFEngine_Form_Element_ImageFile('image_filename');
        $imageFilename->setAttrib('style', 'top: 0pt; left: 0pt;')
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

        $this->setEnctype('multipart/form-data');
        
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $imageFilename->setDecorators(array('File'));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/product-image.phtml'))
        ));

        return $this;
    }



}