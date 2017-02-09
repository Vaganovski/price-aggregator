<?php

class Catalog_Model_CategoryAccessoriesService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        // @todo refact
        $this->_modelName = 'Catalog_Model_CategoryAccessories';
    }

}