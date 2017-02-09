<?php

/**
 * Create new comment form
 */
class Shops_Form_Comment_New extends Shops_Form_Comment_Abstract
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $this->text->setLabel(_('Отзыв'));
        $this->submit->setLabel(_('Отправить'));

        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => 'forms/new.phtml'))
        ));

        return $this;
    }
}