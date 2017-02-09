<?php

class Advertisment_Form_Banner_Right extends Advertisment_Form_Banner_Abstract
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

        $this->getElement('submit')->setLabel(_('Сохранить'));
        $this->getElement('type')->setValue('right');
    }


}