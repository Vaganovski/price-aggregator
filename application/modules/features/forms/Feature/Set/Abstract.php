<?php

abstract class Features_Form_Feature_Set_Abstract extends Zend_Form
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

        $name = new Zend_Form_Element_Text('title');
        $name->setLabel(_('Название набора'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($name);

        // 
        $fieldSubFormWrapper = new Zend_Form_SubForm();
        $fieldSubForm = new Zend_Form_SubForm();

//        $setId = new Zend_Form_Element_Hidden('id');
//        $fieldSubForm->addElement($setId);
        $setName = new Zend_Form_Element_Text('title');
        $setName->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $fieldSubForm->addElement($setName);

        $slider = new Zend_Form_Element_Checkbox('slider');
        $slider->setDescription('ползунок')->setOptions(array('checked' => false));
        $slider->setDecorators(array(
            'ViewHelper',
            'DtDdWrapper',
            'Errors',
            array('Description', array('tag' => 'span')),
        ));
        $fieldSubForm->addElement($slider);

        $unit = new Zend_Form_Element_Text('unit');
        $unit->setLabel(_('единица'))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        $fieldSubForm->addElement($unit);

        $type = new Zend_Form_Element_Select('type');
        $type->setLabel(_('Тип'))
            ->setRequired()
            ->addMultiOptions(array('select' => 'Выпадающий список', 'text' => 'Текст'));
        $fieldSubForm->addElement($type);
        
        $fieldId = new Zend_Form_Element_Hidden('id');
        $fieldSubForm->addElement($fieldId);

        $valueWrapperSubForm = new Zend_Form_SubForm();
        $valueSubForm = new Zend_Form_SubForm();
        $valueName = new Zend_Form_Element_Text('title');
        $valueName->addFilter('StripTags')
            ->addFilter('StringTrim');
        $valueSubForm->addElement($valueName);

        $valueId = new Zend_Form_Element_Hidden('id');
        $valueSubForm->addElement($valueId);
//        $valueSubForm->removeDecorator('Fieldset');
//        $valueSubForm->removeDecorator('HtmlTag');
        $valueSubForm->removeDecorator('DtDdWrapper');

        $valueWrapperSubForm->removeDecorator('HtmlTag');
        $valueWrapperSubForm->removeDecorator('DtDdWrapper');
        $valueWrapperSubForm->addSubForm($valueSubForm, 'v1');

        $fieldSubForm->addSubForm($valueWrapperSubForm, 'values');

        $fieldSubFormWrapper->addSubForm($fieldSubForm,'1');


        $fieldSubForm->removeDecorator('DtDdWrapper');

        $fieldSubFormWrapper->addSubForm($fieldSubForm,'1');


        $fieldSubFormWrapper->removeDecorator('Fieldset');
        $fieldSubFormWrapper->removeDecorator('HtmlTag');
        $fieldSubFormWrapper->removeDecorator('DtDdWrapper');
        $this->addSubForm($fieldSubFormWrapper, 'fields');

     
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/set/abstract.phtml'))
        ));

        return $this;
    }

    /**
     * Populate form
     *
     * Proxies to {@link setDefaults()}
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate(array $values)
    {
        if (array_key_exists('fields', $values)) {
            $this->_populateFields($values['fields']);
        } elseif (array_key_exists('Fields', $values)) {
            $this->_populateFields($values['Fields']);
            $values['fields'] = $values['Fields'];
            unset ($values['Fields']);
        }
        if (!array_key_exists('fields', $values)) {
            $this->removeSubForm('fields');
        } else {
            array_unshift($values['fields'], 0);
            unset($values['fields'][0]);
        }
       return parent::populate($values);
    }

    /**
     * Set default values for elements
     *
     * Sets values for all elements specified in the array of $defaults.
     *
     * @param  array $defaults
     * @return Zend_Form
     */
    public function setDefaults(array $defaults)
    {
        // заносим данные с БД о характеристиках и значениях
        if (array_key_exists('Fields', $defaults) && count($defaults['Fields'])) {
            $this->_populateFields($defaults['Fields']);
            foreach ($defaults['Fields'] as $key => $field) {
                $defaults['Fields'][$key]['values'] = $field['Values'];
                unset($defaults['Fields'][$key]['Values']);
            }
            $defaults['fields'] = $defaults['Fields'];
            unset ($defaults['Fields']);
            array_unshift($defaults['fields'], 0);
            unset($defaults['fields'][0]);
        } else {
           $defaults['fields'][1] = array ('title'=>'',
                                           'id'=>'');
        }
       return parent::setDefaults($defaults);
    }

    /**
     * Создаем елементы для характеристик и запоняем их значениями
     *
     * @param  array $values
     * @return void
     */
    protected function _populateFields($values) {
        $count = 1;
        $fieldWrapper = $this->getSubForm('fields');
        // заготовка формы для характеристик
        $fieldSubFormTemplate = $fieldWrapper->getSubForm('1');
        $fieldWrapper->removeSubForm('1');
        foreach ($values as $key => $value) {
            // клонируем из заготовки и заносим свои данные
            $fieldSubForm = clone $fieldSubFormTemplate;
            $fieldSubForm->title->setValue($value['title']);
            $fieldSubForm->unit->setValue($value['unit']);
            $fieldSubForm->type->setValue($value['type']);
            $fieldSubForm->id->setValue($value['id']);

            // клонируем форму-врапер для значений
            $valuesWrapperSubForm = clone $fieldSubFormTemplate->getSubForm('values');
            $fieldSubForm->removeSubForm('values');
            // заготовка формы для значений характеристик
            $valuesSubFormTemplate = $valuesWrapperSubForm->getSubForm('v1');
            $valuesWrapperSubForm->removeSubForm('v1');
            if (isset($value['Values'])) {
                $value['values'] = $value['Values'];
                unset($value['Values']);
                $key = $count;
            }
            // клонируем форму со значениями и заносим свои данные
            foreach ($value['values'] as $keyVal => $valueVal) {
                $valuesSubForm = clone $valuesSubFormTemplate;
                $valuesSubForm->title->setValue($valueVal['title']);
                $valuesSubForm->id->setValue($valueVal['id']);
                if (is_numeric($keyVal)) {
                    $keyVal = 'v' . $keyVal;
                }
                $valuesWrapperSubForm->addSubForm($valuesSubForm, $keyVal);
            }
            // добавляем форму значений в форму характеристик
            $fieldSubForm->addSubForm($valuesWrapperSubForm, 'values');
            // добавляем форму характеристик в форму-врапер
            $fieldWrapper->addSubForm($fieldSubForm, $key);
            $count++;
        }

    }
}