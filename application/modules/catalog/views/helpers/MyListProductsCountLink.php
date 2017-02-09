<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_MyListProductsCountLink extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function MyListProductsCountLink()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            // количество товаро в моем списке
            $namespace = new Zend_Session_Namespace('product-to-mylist');
            $this->view->countMyList = count($namespace->product_ids);
            $this->view->class = ' unloginUser';
        } else {
            // количество товаро в моем списке
            $myList = new Catalog_Model_MyProductsListService();
            $this->view->countMyList = $myList->getMapper()->countProductsFromList($auth->getIdentity()->id);
        }

        return $this->view->render($this->getViewScript());
    }

}