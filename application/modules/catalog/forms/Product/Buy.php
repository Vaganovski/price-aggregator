<?php

class Catalog_Form_Product_Buy extends Zend_Form
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

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel(_('Ваше имя'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($name);


        $cities = array();
        foreach ($this->getView()->GetCities() as $city){
            $cities[$city] = $city;
        }
        $city = new Zend_Form_Element_Select('city');
        $city->setAttrib('class', 'width')
            ->setLabel(_('Город'))
            ->setRequired()
            ->addMultiOptions($cities)
            ->setValue('Алматы');
        $this->addElement($city);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel(_('Телефон'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($phone);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
            ->setRequired()
            ->addFilter('StringTrim')
            ->addValidator('EmailAddress', true)
            ->setRequired();
        $this->addElement($email);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel(_('Примечание (время звонка, вопрос о товаре и т.д.):'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($description);

        $productId = new Zend_Form_Element_Hidden('product_id');
        $this->addElement($productId);
        $shopId = new Zend_Form_Element_Hidden('price_id');
        $this->addElement($shopId);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Отправить'))->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => 'forms/buy.phtml'))
        ));
        return $this;
    }
}