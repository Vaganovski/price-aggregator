<?php

/**
 * Хелпер
 */
class Shops_View_Helper_ShopCommentsCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ShopCommentsCount($shopId = NULL)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('shops', 'comment');
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;

        $id = strtolower(__CLASS__);
        if (!($shopCommentsCount = $cacheCounters->load($id))) {
            $shopCommentsCount[$shopId] = $this->_serviceLayer->getMapper()->countByShopId($shopId);
            $cacheCounters->save($shopCommentsCount, $id);
        } else {
            if (!isset ($shopCommentsCount[$shopId])) {
                $shopCommentsCount[$shopId] = $this->_serviceLayer->getMapper()->countByShopId($shopId);
                $cacheCounters->save($shopCommentsCount, $id);
            }
        }
        return $shopCommentsCount[$shopId];
    }

}