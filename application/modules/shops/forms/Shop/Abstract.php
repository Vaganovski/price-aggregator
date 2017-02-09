<?php

abstract class Shops_Form_Shop_Abstract extends Shops_Form_ShopBase_Abstract
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel(_('Описание'))
            ->setAttrib('cols', 39)
            ->setAttrib('rows', 10)
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($description);
        
        $workTime = new Zend_Form_Element_Text('work_time');
        $workTime->setLabel(_('Режим работы'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($workTime);

        $delivery = new Zend_Form_Element_Text('delivery');
        $delivery->setLabel(_('Доставка'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($delivery);

        $credit = new Zend_Form_Element_Text('credit');
        $credit->setLabel(_('Возможность кредита'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim');
        $this->addElement($credit);


        $type = new Zend_Form_Element_Select('type');
        $type->setLabel(_('Тип магазина'))
            ->setRequired()
            ->addMultiOptions(array(
                Shops_Model_Shop::TYPE_NORMAL => _('Обычный магазин'),
                Shops_Model_Shop::TYPE_INTERNET => _('Интернет магазин')
            ));
        $this->addElement($type);

        $chainShopId = new Zend_Form_Element_Select('chain_shop_id');
        $chainShopId->setLabel(_('Сеть магазинов'))
            ->addMultiOptions(array('' => 'Отдельный магазин'))
            ->setAttrib('class', 'width');
        $chainShopId->setDecorators(array(
            'ViewHelper'
        ));
        $this->addElement($chainShopId);

        $this->city->setAttrib('class','width');
        $this->type->setAttrib('class','width');

        $this->setElementDecorators(array(
            'ViewHelper'
        ));
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => '/forms/abstract.phtml'))
        ));

        return $this;
    }
    
    /**
     * validate hook
     * @param array $data
     * @return boolean
     */
    public function isValid($data)
    {
        if ($data['type'] == Shops_Model_Shop::TYPE_INTERNET) {
            $this->getElement('address')->setRequired(false);
        }
        return parent::isValid($data);
    }

}