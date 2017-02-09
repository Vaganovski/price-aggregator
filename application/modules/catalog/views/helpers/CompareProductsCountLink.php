<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_CompareProductsCountLink extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function CompareProductsCountLink()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->view->class = ' unloginUser';
        } 
        // количество товаро к сравнению
        $namespace = new Zend_Session_Namespace('product-to-compare');
        $this->view->countCompare = count($namespace->product_ids);
        $this->view->productIds = $namespace->product_ids;

        return $this->view->render($this->getViewScript());
    }

}