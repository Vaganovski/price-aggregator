<?php

/**
 * Create new comment form
 */
class Comments_Form_Comment_New extends Comments_Form_Comment_Abstract
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $this->submit->setLabel(_('Добавить коментарий'));

        return $this;
    }
}