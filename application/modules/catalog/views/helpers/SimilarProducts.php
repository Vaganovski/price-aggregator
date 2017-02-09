<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_SimilarProducts extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function SimilarProducts()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'product');
        }
        return $this;
    }

    /**
     * Возвращает форму поиска
     *
     * @return string
     */
    public function getProducts($categoryId, $productId)
    {
        
        $this->view->products = $this->_serviceLayer->getMapper()->findAllSimilarWithLimit($categoryId, $productId, 16);

        return $this->view->render($this->getViewScript());
    }


    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}