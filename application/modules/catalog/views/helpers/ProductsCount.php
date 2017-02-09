<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_ProductsCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ProductsCount($lft = NULL, $rgt = NULL)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'product');
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;

        $arrayKey = $lft . '-' . $rgt;

        $id = strtolower(__CLASS__);
        if (!($productsCount = $cacheCounters->load($id))) {
            $productsCount[$arrayKey] = $this->_serviceLayer->getMapper()->countAllByCategoryId($lft, $rgt);
            $cacheCounters->save($productsCount, $id);
        } else {
            if (!isset ($productsCount[$arrayKey])) {
                $productsCount[$arrayKey] = $this->_serviceLayer->getMapper()->countAllByCategoryId($lft, $rgt);
                $cacheCounters->save($productsCount, $id);
            }
        }

        return $productsCount[$arrayKey];
    }

}