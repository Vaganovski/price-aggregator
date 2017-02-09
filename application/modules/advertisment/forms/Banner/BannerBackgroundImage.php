<?php

class Advertisment_Form_Banner_BannerBackgroundImage extends Zend_Form
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
        
        $backgroundImage = new Zend_Form_Element_File('background_image');
        $backgroundImage->setAttrib('style', 'top: 0pt; left: 0pt;')
            ->addValidator('IsImage')
            ->setDestination($this->_serviceLayer->getModel()->getImageAbsoluteUploadPath())
            ->addValidator('Size', false, 1048576)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $uniqOpt = array('targetDir' => $this->_serviceLayer->getModel()->getImageAbsoluteUploadPath(),
                         'nameLength' => 10);
        $backgroundImage->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
        $this->addElement($backgroundImage);


        $this->setEnctype('multipart/form-data');
        

        return $this;
    }



}