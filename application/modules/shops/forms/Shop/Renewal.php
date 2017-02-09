<?php

class Shops_Form_Shop_Renewal extends Zend_Form
{

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));
        $period = new Zend_Form_Element_Select('period');
        $period->setLabel(_('Город'))
            ->setRequired()
            ->addMultiOptions(array('10' => '10 дн.', '30' => '30 дн.', '40' => '40 дн.', '60' => '60 дн.'))
            ->setValue(30);
        $this->addElement($period);

        $id = new Zend_Form_Element_Hidden('id');
        $this->addElement($id);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)
               ->setLabel(_('Окей'))
               ->setOrder(100);
        $this->addElement($submit);
        
        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => '/forms/renewal.phtml'))
        ));
    }

}