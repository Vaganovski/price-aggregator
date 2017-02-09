<?php

/**
 * Form for delete user
 */
class Users_Form_UserBase_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));

        return $this;
    }
}
