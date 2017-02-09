<?php

class Catalog_Form_Product_EditFeatures extends Zend_Form
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
        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);

     
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setLabel(_('Сохранить'))->setOrder(100);
        $this->addElement($submit);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->loadDefaultDecorators();
        $this->removeDecorator('HtmlTag');

        return $this;
    }


    /**
     * Генерирует элементы формы по наборам характеристик
     *
     * @param  array $data
     * @return void
     */
    public function generateForm(array $data)
    {
        if (count($data)) {
            $count = 0;$count1 = 0;
            $wrapperFieldSubForm = new Zend_Form_SubForm();
            foreach($data as $set) {
                // под-форма для набора характеристик
                $fieldSubForm = new Zend_Form_SubForm();
                $fieldSubForm->setLegend($set['title']);
                // перебор всех характеристик из набора
                foreach ($set['Fields'] as $field) {
                    if ($field['type'] == 'select') {
                        // формирование выпадающего списка с возможными значениями характеристики
                        $valueField = new Zend_Form_Element_Select($field['id']);
                        $valueField->setLabel($field['title'])
                            ->setRequired();
                        $options = array();
                        foreach ($field['Values'] as $value) {
                           $options[$value['id']] = $value['title'];
                        }
                        $valueField->addMultiOptions($options);
                    } else {
                        // вставляется поле для заполнения значения характеристики
                        $hiddenField = new Zend_Form_Element_Hidden('text' . $field['id']);
                        $hiddenField->removeDecorator('Label');
                        $hiddenField->removeDecorator('HtmlTag');
                        $fieldSubForm->addElement($hiddenField);
                        $valueField = new Zend_Form_Element_Text($field['id']);
                        $valueField->setLabel($field['title'])
                            ->setValue($field['Values'][0]['title']) // @todo Refact
                            ->setRequired();
                    }
                    $fieldSubForm->addElement($valueField);
                }
                $fieldSubForm->removeDecorator('HtmlTag');
                $fieldSubForm->removeDecorator('DtDdWrapper');
                $wrapperFieldSubForm->addSubForm($fieldSubForm, 's'. $set['id']);
            }
            $wrapperFieldSubForm->removeDecorator('Fieldset');
            $wrapperFieldSubForm->removeDecorator('HtmlTag');
            $wrapperFieldSubForm->removeDecorator('DtDdWrapper');
            $this->addSubForm($wrapperFieldSubForm, 'fields');

        }
    }

}