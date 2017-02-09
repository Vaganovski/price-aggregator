<?php

class Default_Form_Settings_Edit extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('form_settings_edit')
             ->setMethod('post');

        $settings = Zend_Registry::get('settings');

        foreach ($settings->getValues() as $element){
            if('boolean' == $element['type']) {
                $element['key'] = new Zend_Form_Element_Checkbox($element['key']);
                $element['key']->setLabel($element['description'])
                    ->setValue($element['value']);
                $this->addElement($element['key']);
                $element['key']->addDecorator('Label',
                        array('tag'=>'dt','placement' => 'APPEND'));
            }
        }

        $submit = new Zend_Form_Element_Submit('submit');
        $this->addElement($submit);
        $submit->setLabel(_('Сохранить'));
        return $this;
    }
}