<?php

class Advertisment_Form_Banner_BannerImage extends Zend_Form
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

        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);
        
        $type = new Zend_Form_Element_Hidden('type');
        $this->addElement($type);

        $imageFilename = new Zend_Form_Element_File('image_filename');
        $imageFilename->setAttrib('style', 'top: 0pt; left: 0pt;')
            ->addValidator('IsImage')
            ->setDestination(Advertisment_Model_BannerImage::getImageAbsoluteUploadPath())
            ->addValidator('Size', false, 1048576)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $uniqOpt = array('targetDir' => Advertisment_Model_BannerImage::getImageAbsoluteUploadPath(),
                         'nameLength' => 10);
        $imageFilename->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
//        $config['width'] = $this->_serviceLayer->bannerWidth;
//        $config['height'] = $this->_serviceLayer->bannerHeight;//Zend_Registry::get('config')->products->images->original;
//        $imageFilename->addFilter(new ZFEngine_Filter_File_ImageResize(array('width' => $config['width'], 'height' =>$config['height'])));
        $this->addElement($imageFilename);

        $this->setEnctype('multipart/form-data');
        

        return $this;
    }



}