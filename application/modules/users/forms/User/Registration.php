<?php

class Users_Form_User_Registration extends Users_Form_UserExtended_Registration
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));
        parent::init();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel(_('Ваше имя'))
              ->setRequired(true)
              ->addFilter('StringTrim')
              ->addFilter('StripTags');
        $this->addElement($name);

        $cities = array();
        foreach ($this->getView()->GetCities() as $city){
            $cities[$city] = $city;
        }
        $city = new Zend_Form_Element_Select('city');
        $city->setLabel(_('Город'))
            ->setRequired()
            ->addMultiOptions($cities)
            ->setValue('Алматы');
        $city->setAttrib('class','width');
        $this->addElement($city);

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

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'users/views/scripts/index/forms/registration.phtml'))
        ));


        $this->getElement('submit')->setLabel(_('Регистрация'))
                ->setDescription(_('После нажатия кнопки на ваш e-mail будет выслано письмо, в котором будет находиться ссылка, необходимая для подтверждения регистрации.'));

//        $this->captcha->removeDecorator('Captcha_Word');
        $this->captcha->setDecorators(array(new Default_Form_Decorator_Captcha_Word(array('placement' => Zend_Form_Decorator_Abstract::PREPEND))));
        return $this;
    }

}