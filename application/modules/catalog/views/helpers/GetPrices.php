<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_GetPrices extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function GetPrices($productId = NULL, $shopId = NULL, $orderBy = NULL, $sortDirection = 'ASC')
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'price');
        }
        
        return $this->_serviceLayer->getMapper()->findAllByProductId($productId, $shopId, $orderBy, $sortDirection);
    }

}