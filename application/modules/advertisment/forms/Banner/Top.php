<?php

class Advertisment_Form_Banner_Top extends Advertisment_Form_Banner_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();


        $backgroundImage = new Zend_Form_Element_File('background_image');
        $backgroundImage->setAttrib('style', 'top: 0pt; left: 0pt;')
            ->addValidator('IsImage')
            ->setDestination($this->_serviceLayer->getModel()->getImageAbsoluteUploadPath())
            ->addValidator('Size', false, 1048576)
            ->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $uniqOpt = array('targetDir' => $this->_serviceLayer->getModel()->getImageAbsoluteUploadPath(),
                         'nameLength' => 10);
        $backgroundImage->addFilter(new ZFEngine_Filter_File_SetUniqueName($uniqOpt));
        $this->addElement($backgroundImage);

        $this->show_on->addMultiOptions(array('in-place' => _('на указаной странице')));

        $page_placement = new Zend_Form_Element_Text('page_placement');
        $page_placement->setLabel(_('Страница размещения'))
            ->addValidator('regex', false, array("/^([a-zA-Z]+)(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+&%\$#\=~_\-]+))*$/"))
            ->setErrorMessages(array('Адрес страницы введен неверно'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($page_placement);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->background_image->setDecorators(array('File'));
        $this->image_filename->setDecorators(array('File'));

        $this->getElement('type')->setValue('top');
        $this->getElement('submit')->setLabel(_('Сохранить'));

    }


}