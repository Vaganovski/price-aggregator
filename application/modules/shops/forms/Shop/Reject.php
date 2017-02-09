<?php

class Shops_Form_Shop_Reject extends Default_Form_Delete
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
        parent::init();
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
                ->setOrder(0)
                ->setAttrib('readonly', true)
                ->addFilter('StringTrim')
                ->addValidator('EmailAddress', true);
        $this->addElement($email);
        
        $reason = new Zend_Form_Element_Textarea('reason');
        $reason->setLabel(_('Причина отказа'))
                    ->setOrder(1)
                    ->setAttrib('cols', 80)
                    ->setAttrib('rows', 20)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim');
        $this->addElement($reason);
        

        return $this;
    }
}