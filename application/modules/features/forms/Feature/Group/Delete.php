<?php

/**
 * Form for delete page
 */
class Features_Form_Feature_Group_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));

        return $this;
    }
}