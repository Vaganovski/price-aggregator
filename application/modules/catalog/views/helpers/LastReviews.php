<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_LastReviews extends ZFEngine_View_Helper_Abstract
{

    /**
     * Выполняет роль конструктора
     *
     */
    public function LastReviews()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'product');
        }
     
        return $this;
    }

    /**
     * Выбирает последнии отзывы от товара
     *
     * @return string
     */
    public function getLastReviews($productId)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'product');
        }
        $this->view->reviews = $this->_serviceLayer->getMapper()->findAllProductsReviews($productId, 2);
        $this->view->productId = $productId;
        return $this->view->render($this->getViewScript());
    }

 

    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}