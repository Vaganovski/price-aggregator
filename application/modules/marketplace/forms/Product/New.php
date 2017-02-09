<?php

class Marketplace_Form_Product_New extends Marketplace_Form_Product_Abstract
{
    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));
        
        $user_id = new Zend_Form_Element_Hidden('user_id');
        $this->addElement($user_id);

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
        $captcha->setDecorators(array(new Default_Form_Decorator_Captcha_Word(
                array('placement' => Zend_Form_Decorator_Abstract::PREPEND,
                      'baraholka' => '1',))));
        $this->addElement($captcha);
        
        $this->submit->setLabel(_('Добавить'));
        
        return $this;
    }

}