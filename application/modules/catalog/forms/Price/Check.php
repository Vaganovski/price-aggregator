<?php

class Catalog_Form_Price_Check extends Zend_Form
{

    protected $_serviceLayer;

    /**
     * @param object $_serviceLayer
     * @param array $options
     */
    function __construct($_serviceLayer, $options = null)
    {
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

        $category = new Zend_Form_Element_Text('category');
        $category->setRequired()
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->addElement($category);

        $brand = new Zend_Form_Element_Text('brand');
        $brand->setRequired()
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->addElement($brand);

        $model = new Zend_Form_Element_Text('model');
        $model->setRequired()
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->addElement($model);

        $description = new Zend_Form_Element_Text('description');
        $description->addFilter('StripTags', array(array('allowTags' => array('div', 'br', 'li', 'ul', 'ol', 'p'))))
                ->addFilter(new Catalog_Form_Price_Filter_HTMLPurifier())
                ->addFilter('StringTrim');
        $this->addElement($description);

        $price = new Zend_Form_Element_Text('price');
        $price->setRequired()
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->addElement($price);

        $available = new Zend_Form_Element_Text('available');
        $available->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->addElement($available);

        $url = new Zend_Form_Element_Text('url');
        $url->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('regex', false, array("/^(|http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i"));
        $this->addElement($url);

        $photo = new Zend_Form_Element_Text('photo');
        $photo->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('regex', false, array("/^(|http(s)?:\/\/)(([^\/]+\.)+)\w{2,}(\/)?.*$/i"));
        $this->addElement($photo);


        $shopId = new Zend_Form_Element_Text('shop_id');
        $shopId->setRequired()
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->addElement($shopId);

        return $this;
    }
}