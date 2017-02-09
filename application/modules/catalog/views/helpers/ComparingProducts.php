<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_ComparingProducts extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ComparingProducts()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'product');
        }
        $namespace = new Zend_Session_Namespace('product-to-compare');
        if (count($namespace->product_ids) > 0) {
            $this->view->comaringProducts = $this->_serviceLayer->getMapper()->findByProductIds($namespace->product_ids);
        }
        return $this->view->render($this->getViewScript());
    }

}