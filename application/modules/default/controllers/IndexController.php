<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
//        Zend_Debug::dump(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getPluginResource('modules')->getExecutedBootstraps()->categories->getResourceLoader());
//        Zend_Debug::dump(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('autoloadernamespaces'));

//        new Categories_View_Helper_CategoriesList();

        // action body

    }

    public function adminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle(_('Административная часть сайта'));
        
        $shopServices = new Shops_Model_ShopService();
        $this->view->shops= $shopServices->getMapper()->getAllByStatus('new');

        $productServices = new Catalog_Model_ProductService();
        $this->view->products = $productServices->getMapper()->findAllNewWithLimit(10);

        $productServices = new Marketplace_Model_ProductService();
        $this->view->marketplaceProducts = $productServices->getMapper()->findAllNewWithLimit(10);
    }


}

