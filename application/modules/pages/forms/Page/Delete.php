<?php

/**
 * Form for delete page
 */
class Pages_Form_Page_Delete extends Default_Form_Delete
{

    public function init()
    {
        parent::init();

        $this->setName('form_page_delete');

        return $this;
    }
}