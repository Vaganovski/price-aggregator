<?php

class Catalog_Model_ReviewService extends Reviews_Model_ReviewService
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Catalog_Model_Review';
    }

    public function approve($type)
    {
        if ($this->getModel() && ($type == 1 || $type == 2)) {
            $this->getModel()->approve = $type;
            $this->getModel()->save();
            return true;
        } else {
            return false;
        }
    }
}