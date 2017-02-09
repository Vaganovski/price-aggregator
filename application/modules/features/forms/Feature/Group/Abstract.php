<?php

abstract class Features_Form_Feature_Group_Abstract extends Zend_Form
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
        $name->setLabel(_('Название шаблона'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($name);

        // 
        $setSubFormWrapper = new Zend_Form_SubForm();
        $setSubForm = new Zend_Form_SubForm();
        $setName = new Zend_Form_Element_Text('title');
        $setName->setLabel(_('Название набора'))
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $setSubForm->addElement($setName);
        $setId = new Zend_Form_Element_Hidden('id');
        $setSubForm->addElement($setId);

        $setSubForm->removeDecorator('DtDdWrapper');
        $setSubFormWrapper->addSubForm($setSubForm,'1');

        $setSubFormWrapper->removeDecorator('Fieldset');
        $setSubFormWrapper->removeDecorator('HtmlTag');
        $setSubFormWrapper->removeDecorator('DtDdWrapper');
        $this->addSubForm($setSubFormWrapper, 'sets');

     
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/group/abstract.phtml'))
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
        // данные о наборах с формы
        if (array_key_exists('sets', $values)) {
            foreach ($values['sets'] as $key => $set) {
                $sets[] = array('title' => $set['set_title'],
                                'id' => $set['set_id'],
                                'form_name' => $key);
            }
            $this->_populateSets($sets);
            //unset($values['sets']);
        } elseif (array_key_exists('Sets', $values)) { // данные с БД
            $this->_populateSets($values['Sets']);
            //unset($values['Sets']);
            $values['sets'] = $values['Sets'];
            unset ($values['Sets']);
        }
        array_unshift($values['sets'], 0);
        unset($values['sets'][0]);
       return parent::populate($values);
    }

    /**
     * Создаем елементы для наборов и запоняем их значениями
     *
     * @param  array $values
     * @return void
     */
    protected function _populateSets($values) {
        $count = 1;
        $setWrapper = $this->getSubForm('sets');
        // заготовка формы
        $setSubFormTemplate = $setWrapper->getSubForm('1');
        $setWrapper->removeSubForm('1');
        foreach ($values as $value) {
            // клонируем из заготовки и заносим свои данные
            $setSubForm = clone $setSubFormTemplate;
            $setSubForm->title->setValue($value['title']);
            $setSubForm->id->setValue($value['id']);
            // добавляем в форму
            if (isset($value['form_name'])) {
                $setWrapper->addSubForm($setSubForm, $value['form_name']);
            } else {
                $setWrapper->addSubForm($setSubForm, (string)$count);
            }
            
            $count++;
        }

    }
}