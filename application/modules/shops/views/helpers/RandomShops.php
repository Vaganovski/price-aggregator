<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_RandomShops extends ZFEngine_View_Helper_Abstract
{

    /**
     * Выполняет роль конструктора
     *
     */
    public function RandomShops()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('shops', 'shop');
        }
     
        return $this;
    }

    /**
     * Выбирает последнии отзывы от товара
     *
     * @return string
     */
    public function getRandomShops($limit)
    {

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCore = $cache->core;

        $id = 'shops_random_shops_blcok';
        if (!($shops = $cacheCore->load($id))) {
            $shops = $this->_serviceLayer->getMapper()->findAllAvailableOrderByName(NULL, NULL, 100, true);
            $cacheCore->save($shops, $id);
        }
        $countShops = $shops->count();
        if ($countShops > 0) {
            $shopsForView = array();
            $shopIndexes = array();
            $i = 0;
            do {
                $index = mt_rand(0, $countShops - 1);
                if (!in_array($index, $shopIndexes)) {
                    $shopsForView[$i++] = $shops[$index];
                    $shopIndexes[] = $index;
                }
                if ($i > $limit || $i == $countShops) {
                    break;
                }
            } while(true);

            $this->view->shops = $shopsForView;
        }
        return $this->view->render($this->getViewScript());
    }

 

    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}