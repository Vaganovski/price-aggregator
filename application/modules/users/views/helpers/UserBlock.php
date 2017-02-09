<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню пользователя
 */
class Users_View_Helper_UserBlock extends ZFEngine_View_Helper_Abstract
{

    private $_content = null;

    /**
     * Возвращает отрендереное меню пользователя
     *
     * @return string
     */
    public function userBlock()
    {
        if (null === $this->_content) {
            $auth = Zend_Auth::getInstance();
            if (!$auth->hasIdentity()) {
                // количество товаро в моем списке
                $namespace = new Zend_Session_Namespace('product-to-mylist');
                $this->view->countMyList = count($namespace->product_ids);
                // количество товаро к сравнению
                $namespace = new Zend_Session_Namespace('product-to-compare');
                $this->view->countCompare = count($namespace->product_ids);

                $this->_content = $this->view->render($this->getViewScript('login'));
            } else {
                $userServiceLayer = new Users_Model_UserService();
                $userServiceLayer->findUserByAuth();
                // количество товаро в моем списке
                $myList = new Catalog_Model_MyProductsListService();
                $this->view->countMyList = $myList->getMapper()->countProductsFromList($userServiceLayer->getModel()->id);
                // количество товаро к сравнению
                $namespace = new Zend_Session_Namespace('product-to-compare');
                $this->view->countCompare = count($namespace->product_ids);
                
                $this->_content = $this->view->render($this->getViewScript('profile'));
            }
        }
        return $this->_content;
    }
}