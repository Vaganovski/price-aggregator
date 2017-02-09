<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_PricesCountByProductId extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function PricesCountByProductId($productId)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'price');
        }

        return $this->_serviceLayer->getMapper()->countAllByProductId($productId);
    }

}