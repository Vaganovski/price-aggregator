<?php

abstract class Shops_Form_ShopBase_Abstract extends Zend_Form
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
        $name->setLabel(_('Название'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($name);

        $cities = array();
        foreach ($this->getView()->GetCities() as $city){
            $cities[$city] = $city;
        }
        $city = new Zend_Form_Element_Select('city');
        $city->setLabel(_('Город'))
            ->setRequired()
            ->addMultiOptions($cities)
            ->setValue('Алматы');
        $this->addElement($city);

        $address = new Zend_Form_Element_Text('address');
        $address->setLabel(_('Адрес'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($address);

        $website = new Zend_Form_Element_Text('site');
        $website->setLabel(_('Сайт магазина'))
            ->setDescription(_('Например: http://example.com'))
            ->setErrorMessages(array(_('Адрес сайта указан неверно')))
            ->addFilter('StringTrim')
            ->addValidator('regex', false, array("/^(|http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i"));
        $this->addElement($website);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')
            ->addFilter('StringTrim')
            ->addValidator('EmailAddress', true)
            ->setRequired();
        $this->addElement($email);

        $name = new Zend_Form_Element_Text('phone');
        $name->setLabel(_('Телефон'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($name);

        $userId = new ZFEngine_Form_Element_Value('user_id');
        $userId->setDecorators(array('ViewHelper'));
        $this->addElement($userId);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        return $this;
    }

    /**
     * Set default values for elements
     *
     * Sets values for all elements specified in the array of $defaults.
     *
     * @param  array $defaults
     * @return Zend_Form
     */
    public function setDefaults($defaults = array())
    {
        if (($auth = Zend_Auth::getInstance()) && $auth->hasIdentity()) {
            if (!isset($defaults['user_id'])) {
                $this->getElement('user_id')->setValue($auth->getIdentity()->id);
            }
            if (!isset($defaults['email'])) {
                $this->getElement('email')->setValue($auth->getIdentity()->email);
            }
        }
        return parent::setDefaults($defaults);
    }
}