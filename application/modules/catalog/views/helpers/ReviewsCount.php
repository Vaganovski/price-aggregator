<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_ReviewsCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ReviewsCount($productId = NULL)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'review');
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;
        
        $id = strtolower(__CLASS__);
        if (!($reviewsCount = $cacheCounters->load($id))) {
            $reviewsCount[$productId] = $this->_serviceLayer->getMapper()->countAllByProductId($productId);
            $cacheCounters->save($reviewsCount, $id);
        } else {
            if (!isset ($reviewsCount[$productId])) {
                $reviewsCount[$productId] = $this->_serviceLayer->getMapper()->countAllByProductId($productId);
                $cacheCounters->save($reviewsCount, $id);
            }
        }

        return $reviewsCount[$productId];
    }

}