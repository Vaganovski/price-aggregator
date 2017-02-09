<?php

/**
 * Form for delete page
 */
class Reviews_Form_Review_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));

        return $this;
    }
}