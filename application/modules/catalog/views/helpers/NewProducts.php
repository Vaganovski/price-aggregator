<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_NewProducts extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function NewProducts()
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
    public function getProducts($brandId = NULL)
    {
        
        $this->view->products = $this->_serviceLayer->getMapper()->findAllNewWithLimit(16, 1, true, $brandId);
        
        if ($brandId) {
            $brand = new Catalog_Model_BrandService();
            $this->view->brandName = $brand->findModelById($brandId)->getModel()->name;
        }
        return $this->view->render($this->getViewScript());
    }


    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}