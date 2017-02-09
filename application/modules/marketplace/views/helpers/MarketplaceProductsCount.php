<?php

/**
 * Хелпер
 */
class Marketplace_View_Helper_MarketplaceProductsCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function MarketplaceProductsCount($lft = NULL, $rgt = NULL, $city = NULL)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('marketplace', 'product');
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;

        $arrayKey = $lft . '-' . $rgt . '-' . $city;

        $id = strtolower(__CLASS__);
        if (!($productsCount = $cacheCounters->load($id))) {
            $productsCount[$arrayKey] = $this->_serviceLayer->getMapper()->countAllByCategoryIdAndCity($lft, $rgt, $city);
            $cacheCounters->save($productsCount, $id);
        } else {
            if (!isset ($productsCount[$arrayKey])) {
                $productsCount[$arrayKey] = $this->_serviceLayer->getMapper()->countAllByCategoryIdAndCity($lft, $rgt, $city);
                $cacheCounters->save($productsCount, $id);
            }
        }

        return $productsCount[$arrayKey];
    }

}