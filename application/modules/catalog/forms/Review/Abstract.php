<?php

abstract class Catalog_Form_Review_Abstract extends Reviews_Form_Review_Abstract
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
        $this->setName(strtolower(__CLASS__));
        $product_id = new Zend_Form_Element_Hidden('product_id');
        $this->addElement($product_id);
        $user_id = new Zend_Form_Element_Hidden('user_id');
        $this->addElement($user_id);

        $advantages = new Zend_Form_Element_Textarea('advantages');
        $advantages->setLabel(_('Достоинства'))
            ->setDescription(_('Что вам понравилось в этой модели. Все самые важные плюсы.'))
            ->addFilter('StringTrim');
        $this->addElement($advantages);

        $disadvantages = new Zend_Form_Element_Textarea('disadvantages');
        $disadvantages->setLabel(_('Недостатки'))
            ->setDescription(_('Что хотелось бы отметить как недостаток или ошибку.'))
            ->addFilter('StringTrim');
        $this->addElement($disadvantages);

        $this->getElement('comment')->setRequired(false);

        $this->mark->setAttrib('class', 'width');
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/abstract.phtml'))
        ));

        return $this;
    }

    /**
     * validate hook
     * @param array $data
     * @return  boolean
     */
    public function isValid($data) {
        if (parent::isValid($data)) {
            $advantages = $this->getValue('advantages');
            $disadvantages = $this->getValue('disadvantages');
            $comments = $this->getValue('comments');
            // Проверям есть ли хотя бы одно заполненное поле
            if (!(strlen($advantages) || strlen($disadvantages) || strlen($comments))) {
                $this->addError($this->getView()->translate('Обязательным является заполнение хотя бы одного поля'));
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

}