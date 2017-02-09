<?php

abstract class Reviews_Form_Review_Abstract extends Zend_Form
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
 
        $mark = new Zend_Form_Element_Select('mark');
        $mark->setLabel(_('Опыт использования'))
            ->setRequired()
            ->setMultiOptions(array(
                1 => $this->getView()->translate('1 месяц'),
                3 => $this->getView()->translate('3 месяцa'),
                6 => $this->getView()->translate('6 месяцев'),
                12 => $this->getView()->translate('1 год'),
                13 => $this->getView()->translate('больше 1 года'),
            ));
        $this->addElement($mark);

        $description = new Zend_Form_Element_Textarea('comment');
        $description->setLabel(_('Комментарий'))
            ->addFilter('StringTrim')->setOrder(99)
            ->setRequired();
        $this->addElement($description);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        return $this;
    }
}