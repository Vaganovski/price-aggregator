<?php

/**
 * Form for delete page
 */
class Shops_Form_Manager_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));

        return $this;
    }
}