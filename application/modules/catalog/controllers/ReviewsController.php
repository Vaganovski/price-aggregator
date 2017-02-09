<?php

class Catalog_ReviewsController extends Reviews_Controller_Abstract_Index
{
    /**
     * Новый отзыв
     * @return void
     */
    public function newAction()
    {
        if ($this->_request->id) {
            parent::newAction();
            $product = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'product');
            $product->findModelById($this->_request->id);
            $this->view->CategoriesChain($product->getModel()->Categories->getFirst()->id);
            $this->view->BreadCrumbs()->appendBreadCrumb($product->getModel()->name, $this->view->url(array(
                             'module'=>'catalog',
                             'controller'=>'products',
                             'action'=>'view',
                             'id' => $product->getModel()->id
                         ), 'default', true));
            
            if ($this->_processResult) {
                $this->_helper->flashMessenger('Ваш отзыв успешно добавлен. После проверки администрацией он станет доступен на сайте.');
                $this->_helper->redirector->gotoRoute(array(
                        'module' => $this->_request->getModuleName(),
                        'controller' => 'products',
                        'action' => 'view',
                        'id' => $product->getModel()->id),
                    'default', true
                 );
            }
            $this->view->setTitle(_('Написать отзыв о %s'), $product->getModel()->name);
            
            $this->_serviceLayer->getForm('new')->populate(array(
                'product_id' => $product->getModel()->id,
                'user_id' => Zend_Auth::getInstance()->getIdentity()->id
            ));

            $this->view->product = $product->getModel();
            $product->setInViewCompareUrl($product->getModel()->Categories->getFirst()->id,
                $this->_request->getRequestUri());
        } else {
            $this->_forward('not-found', 'error', 'default');
        }
    }


    /**
     * Список отзывов к продукту
     * @return void
     */
    public function listAction()
    {
        if ($this->_request->id) {
            $product = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'product');
            $product->findModelById($this->_request->id);
            $this->view->CategoriesChain($product->getModel()->Categories->getFirst()->id);
            $this->view->BreadCrumbs()->appendBreadCrumb($product->getModel()->name, $this->view->url(array(
                             'module'=>'catalog',
                             'controller'=>'products',
                             'action'=>'view',
                             'id' => $product->getModel()->id
                         ), 'default', true));
            $this->view->setTitle(_('Все отзывы о %s'), $product->getModel()->name);

            $reviews = $this->_serviceLayer->getMapper()->findAllProductsReviews($this->_request->id, NULL, true);

            $paginator =  $this->_helper->paginator->getPaginator($reviews);
            $this->view->paginator = $paginator;
            $this->view->reviews = $paginator->getCurrentItems();
            $this->view->productId = $this->_request->id;
            $this->view->product = $product->getModel();
            $product->setInViewCompareUrl($product->getModel()->Categories->getFirst()->id,
                $this->_request->getRequestUri());
        } else {
            $this->_forward('not-found', 'error', 'default');
        }
    }

    /**
     * Список на модерацию
     * @return void
     */
    public function listAdminAction()
    {
            $this->_helper->layout->setLayout('admin');
            $this->view->setTitle(_('Отзывы к товарам'));
            switch ($this->_request->tab) {
                case 'no-approved':
                    $reviews = $this->_serviceLayer->getMapper()->findAll(1, true);
                    break;
                case 'approved':
                    $reviews = $this->_serviceLayer->getMapper()->findAll(2, true);
                    break;
                case 'all':
                default:
                    $reviews = $this->_serviceLayer->getMapper()->findAll(0, true);
                    break;
            }
            

            $paginator =  $this->_helper->paginator->getPaginator($reviews);
            $this->view->paginator = $paginator;
            $this->view->reviews = $paginator->getCurrentItems();
            $this->view->tab = $this->_request->tab;
    }

    /**
     * Одобрение/отклонение отзывов
     * @return void
     */
    public function approveAction()
    {
        if ($this->_request->id) {
            $this->_serviceLayer->findModelById($this->_request->getParam('id'));
            if ($this->_serviceLayer->approve($this->_request->type)) {
                // чистим кеш при подтверждении отзыва к продукту
                $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
                $cacheCore = $cache->core;
                $id = strtolower('Catalog_View_Helper_ReviewsCount');
                $cacheCore->remove($id);

                $this->_helper->redirector->gotoRoute(array(
                        'module' => $this->_request->getModuleName(),
                        'controller' => 'reviews',
                        'action' => 'list-admin',
                        'tab' => 'all'),
                    'default', true);
            }
        } else {
            $this->_forward('not-found', 'error', 'default');
        }
    }

    /**
     *  Просмотр отзыва
     *  @return void
     */
    public function viewAction() {
        // перенаправление на список отзывов
        $this->_helper->redirector->gotoRoute(array(
            'module' => $this->_request->getModuleName(),
            'controller' => 'reviews',
            'action' => 'list-admin',
            'tab' => 'all'),
        'default', true);
    }
}
