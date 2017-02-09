<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_BrandsCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function BrandsCount()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'brand');
        }
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;

        $id = strtolower(__CLASS__);
        if (!($brandsCount = $cacheCounters->load($id))) {
            $brandsCount = $this->_serviceLayer->getMapper()->countAll();
            $cacheCounters->save($brandsCount, $id);
        }
        
        return $brandsCount;
    }

}