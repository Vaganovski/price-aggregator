<?php

class Users_Form_User_PasswordRestore extends Users_Form_UserBase_PasswordRestore
{

    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));

       $captcha = new Zend_Form_Element_Captcha('captcha', array(
            'label' => _("Число на картинке"),
            'captcha' => array(
                'captcha'       => 'Image',
                'wordLen'       => 5,
                'timeout'       => 300,
                'font'          => realpath(APPLICATION_PATH. '/../resources/fonts/Bullpen3D.ttf'),
                'startImage'    => realpath(APPLICATION_PATH. '/../public/images/start-image.png'),
                'imgDir'        => realpath(APPLICATION_PATH. '/../public/upload/images/captcha'),
                'imgUrl'        => '/upload/images/captcha',
                'lineNoiseLevel' => 1,
                'dotNoiseLevel' => 5,
                'fontSize'      => 20,
                'width'         => 120,
                'height'        => 60
            ),
        ));
        $this->addElement($captcha);

        $this->removeElement('foo');
        
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'users/views/scripts/index/forms/password-restore.phtml'))
        ));
        $this->captcha->setDecorators(array(new Default_Form_Decorator_Captcha_Word(array('placement' => Zend_Form_Decorator_Abstract::PREPEND))));
    }
}