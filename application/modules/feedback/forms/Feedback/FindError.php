<?php

class Feedback_Form_Feedback_FindError extends Feedback_Form_Feedback_New
{

    public function init()
    {
        parent::init();
        $this->setName(__CLASS__);

        $subject = new Zend_Form_Element_Hidden('product_id');
        $subject->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($subject);

        $this->message->setLabel(_('Опишите найденную вами ошибку в описании:'));
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/feedback/views/scripts/index/forms/find-error.phtml'))
        ));

        $this->captcha->setDecorators(array(new Default_Form_Decorator_Captcha_Word(
                    array('placement' => Zend_Form_Decorator_Abstract::PREPEND,
                          'find-error' => '1',))));
        return $this;
    }

}