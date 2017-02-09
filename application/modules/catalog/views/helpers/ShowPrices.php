<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_ShowPrices extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ShowPrices($productId, $orderBy = NULL, $sortDirection = 'ASC')
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'price');
        }
        $shopServiceLayer = new Shops_Model_ShopService();
        $this->view->shops = $shopServiceLayer->getMapper()->findAllWithPricesByProductId($productId, $orderBy, $sortDirection);
        $this->view->productId = $productId;
        $this->view->orderBy = $orderBy;
        $this->view->sortDirection = $sortDirection;
        return $this->view->render($this->getViewScript());
    }

}