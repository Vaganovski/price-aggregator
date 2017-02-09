<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_PricesCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     * @return int
     */
    public function PricesCount()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'price');
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;

        $id = strtolower(__CLASS__);
        if (!($pricesCount = $cacheCounters->load($id))) {
            $pricesCount = $this->_serviceLayer->getMapper()->countAll();
            $cacheCounters->save($pricesCount, $id);
        }
        return $pricesCount;
    }

}