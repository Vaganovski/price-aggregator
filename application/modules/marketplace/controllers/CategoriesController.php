<?php

class Marketplace_CategoriesController extends Categories_Controller_Abstract_Index
{
    /**
     *  Список категорий в админке
     *
     *  @return void
     */
    public function listAdminAction() {
        $this->view->setTitle(_('Категории на Барахолке'));
        $this->_helper->layout->setLayout('admin');
    }

    public function listAction() {
        parent::listAction();
        $this->view->MarketpalceCategoriesChain($this->category->getModel()->id, false);
        if ($this->category->getModel()->getNode()->isRoot() || !$this->category->getModel()->id) {
           $this->view->setTitle($this->view->translate('Барахолка'));
        }
        if (!$this->_request->city) {
            $city = 'Алматы';
        } else {
            $city = $this->_request->city;
        }
        $this->view->city = $city;
    }
/**
     *  New category
     *
     *  @return void
     */
    public function newAction() {
        $this->_helper->layout->setLayout('admin');
        $this->_request->setParam('action_redirect', 'list-admin');
        parent::newAction();
        $this->view->setTitle(_('Категории на Барахолке'));
    }

    /**
     *  Edit category
     *
     *  @return void
     */
    public function editAction() {
        $this->_helper->layout->setLayout('admin');
        $this->_request->setParam('action_redirect', 'list-admin');
        parent::editAction();
        $this->view->setTitle(_('Категории на Барахолке'));
    }

    /**
     *  Удаление региона
     *
     *  @return void
     */
    public function deleteAction() {
        $this->_helper->layout->setLayout('admin');
        $this->_request->setParam('action_redirect', 'list-admin');
        parent::deleteAction();
        $title = sprintf($this->view->translate('Вы хотите удалить категорию "%s"? Все вложенные категории будут удалены!'),
                $this->category->getModel()->title);
        $this->view->title = $title;
    }
    
    /**
     *  View category
     *
     *  @return void
     */
    public function viewAction() {
        // поиск категории
        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
        $this->category->findCategoryByIdOrAlias(0, $this->_request->alias);
        $this->view->setTitle($this->category->getModel()->title);
        $this->view->MarketpalceCategoriesChain($this->category->getModel()->id, false);

        if (!$this->_request->city) {
            $city = 'Алматы';
        } else {
            $city = $this->_request->city;
        }
        // выборка товаров с параметрами
        $product = $this->_helper->getServiceLayer($this->_request->getModuleName(),'product');
        $query = $product->getMapper()->findAllByCategoryIdAndTypeAndCity($this->category->getModel()->id,
                $this->_request->type, $city, true);

        $paginator =  $this->_helper->paginator->getPaginator($query);

        $this->view->paginator = $paginator;
        
        $this->view->city = $city;
        $this->view->category = $this->category->getModel();
        $this->view->type = $this->_request->type;
        $this->view->products = $paginator->getCurrentItems();

    }
}