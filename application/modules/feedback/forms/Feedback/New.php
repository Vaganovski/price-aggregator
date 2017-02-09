<?php

class Feedback_Form_Feedback_New extends Zend_Form
{

    public function init()
    {
        $this->setName('form_feedback_new');

        $userAuth = Zend_Auth::getInstance();
        if (!$userAuth->hasIdentity()) {
            $email = new Zend_Form_Element_Text('email');
            $email->setLabel('Email')
                ->addFilter('StringTrim')
                ->addFilter('StripTags')
                ->addValidator('EmailAddress', true)
                ->setRequired();
            $this->addElement($email);
        }

        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel(_('Нужна помощь? Отправьте сообщение нашей техподдержке заполнив форму:'));
        $message->setRequired(true)
            ->addFilter(new Zend_Filter_StripTags())
            ->addFilter(new Zend_Filter_StringTrim())
            ->addFilter(new Zend_Filter_HtmlEntities());      
        $this->addElement($message);

       $captcha = new Zend_Form_Element_Captcha('captcha', array(
            'label' => _("Число на картинке:"),
            'captcha' => array(
                'captcha' => 'Image',
                'wordLen' => 5,
                'timeout' => 300,
                'font'      => APPLICATION_PATH. '/../public/fonts/Bullpen3D.ttf',
                'startImage'      => APPLICATION_PATH. '/../public/images/start-image.png',
                'imgDir'        => realpath(APPLICATION_PATH. '/../public/upload/images/captcha'),
                'imgUrl'        => '/upload/images/captcha',
                'lineNoiseLevel' => 1,
                'dotNoiseLevel' => 5,
                'fontSize' => 20,
                'width' => 120,
                'height' => 60
            ),
        ));
        $this->addElement($captcha);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Отправить'));
        $this->addElement($submit);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/feedback/views/scripts/index/forms/new.phtml'))
        ));

        $this->captcha->setDecorators(array(new Default_Form_Decorator_Captcha_Word(array('placement' => Zend_Form_Decorator_Abstract::PREPEND))));
        return $this;
    }
    
}