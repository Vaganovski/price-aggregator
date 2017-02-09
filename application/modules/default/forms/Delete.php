<?php

/**
 * Default delete form
 */
abstract class Default_Form_Delete extends Zend_Form {

    public function init()
    {
        $this->setName('form_delete');

        $submitOk = new Zend_Form_Element_Submit('submit_ok');
        $submitOk->setLabel(_('Да'));

        $submitNo = new Zend_Form_Element_Submit('submit_no');
        $submitNo->setLabel(_('Нет'));

        $hash = new Zend_Form_Element_Hash('hash');

        $this->addElements(array($submitOk, $submitNo, $hash));

        $this->setElementDecorators(array('ViewHelper'));

        return $this;
    }

}