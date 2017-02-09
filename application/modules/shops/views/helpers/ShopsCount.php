<?php

/**
 * Хелпер
 */
class Shops_View_Helper_ShopsCount extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ShopsCount($withComment = false)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('shops', 'shop');
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCounters = $cache->counters;

        $id = strtolower(__CLASS__);
        if (!($shopsCount = $cacheCounters->load($id))) {
            $shopsCount[(int)$withComment] = $this->_serviceLayer->getMapper()->countAll($withComment);
            $cacheCounters->save($shopsCount, $id);
        } else {
            if (!isset ($shopsCount[(int)$withComment])) {
                $shopsCount[(int)$withComment] = $this->_serviceLayer->getMapper()->countAll($withComment);
                $cacheCounters->save($shopsCount, $id);
            }
        }

        return $shopsCount[(int)$withComment];
    }

}