<?php

class Marketplace_Form_Product_Search extends Zend_Form
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
        $this->setMethod('get');


        $keywords = new Zend_Form_Element_Text('keywords');
        $keywords->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($keywords);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Найти'))->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        return $this;
    }
}