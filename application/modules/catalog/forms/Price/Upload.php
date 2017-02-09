<?php

class Catalog_Form_Price_Upload extends Zend_Form
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
        $shopId = new Zend_Form_Element_Hidden('shop_id');
        $this->addElement($shopId);

        $priceFilename = new Zend_Form_Element_File('price_filename');
        $priceFilename->setAttrib('class', 'baraholkaMyListItemWidePhotoinputFile')
            ->setAttrib('accept', 'application/x-excel, text/csv')
            ->setAttrib('onchange', 'setPriceFile(this)')
            ->setAttrib('size', '33')
            ->setDestination($this->_serviceLayer->getModel()->getPriceAbsoluteUploadPath())
            ->addValidator('Size', false, 131457280)
            ->addValidator('Extension', false, 'xls,csv')
            ->setRequired();
        $uniqOpt = array('targetDir' => $this->_serviceLayer->getModel()->getPriceAbsoluteUploadPath(),
                         'nameLength' => 10);
        $priceFilename->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
        $this->addElement($priceFilename);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Загрузить'))->setIgnore(true)->setOrder(100)->setAttrib('disabled', true)->setAttrib('id', 'fileSubmit');
        $this->addElement($submit);


        $this->setEnctype('multipart/form-data');

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $priceFilename->setDecorators(array('File'));

        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/catalog/views/scripts/prices/forms/upload.phtml'))
        ));





        return $this;
    }
}
