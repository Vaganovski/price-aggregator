<?php

abstract class Advertisment_Form_Banner_Abstract extends Zend_Form
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
        $this->setEnctype('multipart/form-data');

        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);

        $type = new Zend_Form_Element_Hidden('type');
        $this->addElement($type);

        $imageFilename = new Zend_Form_Element_File('image_filename');
        $imageFilename->setAttrib('style', 'top: 0pt; left: 0pt;')
            ->addValidator('IsImage')
            ->setDestination(Advertisment_Model_BannerImage::getImageAbsoluteUploadPath())
            ->addValidator('Size', false, 1048576)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $uniqOpt = array('targetDir' => Advertisment_Model_BannerImage::getImageAbsoluteUploadPath(),
                         'nameLength' => 10);
        $imageFilename->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
        $this->addElement($imageFilename);

        $backgroundColor = new Zend_Form_Element_Text('background_color');
        $backgroundColor->setLabel(_('# код фона'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($backgroundColor);

        $url = new Zend_Form_Element_Text('url');
        $url->setLabel(_('Ссылка с http://'))
            ->addValidator('regex', false, array("/^(((http(s?))|(ftp))\:\/\/)?(www.|[a-zA-Z].)[a-zA-Z0-9\-\.]+\.([a-zA-Z]+)(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+&%\$#\=~_\-]+))*$/"))
            ->setErrorMessages(array('Адрес сайта введен неверно'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($url);

        $period = new Zend_Form_Element_Select('period');
        $period->setLabel(_('Установка / продление срока:'))
            ->addMultiOptions(array('' => _('Выберите...'), '10' => '10 дн.', '30' => '30 дн.', '40' => '40 дн.', '60' => '60 дн.'));
        $this->addElement($period);

        $showOn = new Zend_Form_Element_Radio('show_on');
        $showOn->addMultiOptions(array('anywhere' => _('на всех страницах сайта'),
                                       'on-main' => _('только на главной'),
                                       'none' => _('нигде')))
               ->setValue('none');
        $showOn->getMultiOptions();
        $this->addElement($showOn);

        $rotation = new Zend_Form_Element_Checkbox('rotation');
        $rotation->setLabel(_('ротация'));
        $this->addElement($rotation);

        if ($this->_serviceLayer->getModel()->id) {

            $this->show_on->setAttrib('id', 'show_on-' . $this->_serviceLayer->getModel()->id);
            $last_renewal_date = new Zend_Date($this->_serviceLayer->getModel()->last_renewal_date);
            $created_at = new Zend_Date($this->_serviceLayer->getModel()->created_at);
            $this->setAttrib('dataFromModel', array(
                'banner_id' => $this->_serviceLayer->getModel()->id,
                'last_renewal_date' => $last_renewal_date->toString('dd.MM.YYY H:m'),
                'untill_date' => $this->_serviceLayer->getModel()->untill_date,
                'created_at' => $created_at->toString('dd.MM.YYY H:m'),
                'period' => $this->_serviceLayer->getModel()->period,
                'diff_untill_date_now_date' => $this->_serviceLayer->getModel()->diff_untill_date_now_date,
                'diff_last_renewal_untill_date' => $this->_serviceLayer->getModel()->diff_last_renewal_untill_date,
                'days_ago' => $this->_serviceLayer->getModel()->days_ago,
                'images' => $this->_serviceLayer->getModel()->Images,
                'background_image_url' => $this->_serviceLayer->getModel()->background_image_url,
                'background_image' => $this->_serviceLayer->getModel()->background_image
            ));
        }

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->image_filename->setDecorators(array('File'));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/abstract.phtml'))
        ));
        return $this;
    }
}