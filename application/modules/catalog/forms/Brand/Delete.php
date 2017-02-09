<?php

/**
 * Form for delete page
 */
class Catalog_Form_Brand_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName(strtolower(__CLASS__));

        return $this;
    }
}