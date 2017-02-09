<?php

/**
 * Form for delete comment
 */
class Default_Form_Comment_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName('form_comment_delete');

        return $this;
    }
}