<?php

/**
 * Create new comment form
 */
class Shops_Form_Comment_Edit extends Shops_Form_Comment_Abstract
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $this->text->setLabel(_('Отзыв'));
        $this->submit->setLabel(_('Отправить'));

        return $this;
    }
}