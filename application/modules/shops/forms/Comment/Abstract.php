<?php
abstract class Shops_Form_Comment_Abstract extends Comments_Form_Comment_Abstract
{

    public function init()
    {
        parent::init();

        $mark = new Zend_Form_Element_Select('mark');
        $mark->setLabel(_('Оценка'))
            ->setRequired()
            ->setAttrib('class', 'width')
            ->addMultiOptions(array('' => 'Выберите...', 'good' => 'хороший магазин',
                'normal' => 'нормальный магазин', 'bad' => 'плохой магазин'));
        $this->addElement($mark);

        $this->setElementDecorators(array(
            'ViewHelper'
        ));

        return $this;
    }
}

?>
