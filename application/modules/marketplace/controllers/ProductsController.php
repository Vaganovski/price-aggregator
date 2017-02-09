<?php

class Marketplace_ProductsController extends Products_Controller_Abstract_Index
{
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('search-autocomplete', 'html')->initContext();
        $ajaxContext->addActionContext('search', 'html')->initContext();
        $ajaxContext->addActionContext('get-description', 'json')->initContext();
        parent::init();
    }

    /**
     *  New Product
     *
     *  @return void
     */
    public function newAction()
    {
        $this->view->BreadCrumbs()->appendBreadCrumb('Профиль', $this->view->url(array(
                 'module'=>'users',
                 'controller'=>'index',
                 'action'=>'profile',
             ), 'default', true));
        $category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');
        $form = $this->_serviceLayer->getForm('new');
        if ($this->_request->isPost()) {
            $categories = $category->getMapper();
            $form->setDefaults(array('categories' => $category->getMapper()));
            $postData = $this->_request->getPost();
        }
        // @todo refact!
        // формирование массива для загрузки каскадных селектов
        $selectedPath[] = array('id'=> 0,
                             'level' => 1,
                             'selected' => '');
        $category->transformArrayToJavascriptArray($selectedPath);
        
        $this->_serviceLayer->getForm('new')->getElement('user_id')->setValue(Zend_Auth::getInstance()->getIdentity()->id);

        $this->_request->setParam('action_redirect', 'list');
        parent::newAction();
        
        $this->view->setTitle(_('Добавить объявление'));

        if (!$this->_processResult && $this->_request->isPost()) {
            $categoriesId = $category->getCategoryIdFromRawArray($postData['category']);
            $category->findCategoryById($categoriesId);
            $categories = $category->getSelectedCategoriesArray(false);
            $category->transformArrayToJavascriptArray($categories);
        }
        $category->insertListJsonLinkAsJsScript();

        $user = $this->_helper->getServiceLayer('users', 'user');
        $user->findUserByAuth();
        $this->view->user = $user->getModel();

    }

    /**
     *  Edit Product
     *
     *  @return void
     */
    public function editAction()
    {
        $this->view->BreadCrumbs()->appendBreadCrumb('Профиль', $this->view->url(array(
                 'module'=>'users',
                 'controller'=>'index',
                 'action'=>'profile',
             ), 'default', true));
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');
        $form = $this->_serviceLayer->getForm('edit');
        if ($this->_request->isPost()) {
            $categories = $category->getMapper();
            // заполняем форму значениями по умолчанию (категории и брэнды)
            $form->setDefaults(array('categories' => $category->getMapper()));
            $postData = $this->_request->getPost();
        } else {
            // выбираем категорию товара
            $category->findCategoryById($this->_serviceLayer->getModel()->Categories[0]->id);
            // получаем массив всей цепочки категорий
            $categories = $category->getSelectedCategoriesArray(false);
            // превращаем массив Javascript массив и вставляем в скрипт представления
            $category->transformArrayToJavascriptArray($categories);
        }

        $this->_request->setParam('action_redirect', 'list');
        parent::editAction();

        $this->view->setTitle(_('Редактировать объявление'));
        
        if (!$this->_processResult && $this->_request->isPost()) {
            $categoriesId = $category->getCategoryIdFromRawArray($postData['category']);
            $category->findCategoryById($categoriesId);
            $categories = $category->getSelectedCategoriesArray(false);
            $category->transformArrayToJavascriptArray($categories);
        }
        $category->insertListJsonLinkAsJsScript();

        $user = $this->_helper->getServiceLayer('users', 'user');
        $user->findUserByAuth();
        $this->view->user = $user->getModel();

    }

    /**
     *  Список товаров
     *
     *  @return void
     */
    public function listAction()
    {
        $query = $this->_serviceLayer->getMapper()->findAllByUserId(Zend_Auth::getInstance()->getIdentity()->id, NULL, true);
        $paginator =  $this->_helper->paginator->getPaginator($query);
        $this->view->BreadCrumbs()->appendBreadCrumb('Профиль', $this->view->url(array(
                 'module'=>'users',
                 'controller'=>'index',
                 'action'=>'profile',
             ), 'default', true));
        $user = $this->_helper->getServiceLayer('users', 'user');
        $user->findUserByAuth();
        $this->view->user = $user->getModel();

        $this->view->setTitle(_('Мои объявления (%s)'), array($paginator->getTotalItemCount()));
        $this->view->paginator = $paginator;
        $this->view->products = $paginator->getCurrentItems();

    }

    /**
     *  Список товаров в админке
     *
     *  @return void
     */
    public function listAdminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle(_('Товары / модели'));

        if (is_array($this->_request->category)) {
            foreach ($this->_request->category as $rawCategory) {
                if (!empty ($rawCategory)) {
                    $selectedCategory = $rawCategory;
                }
            }
        } else {
            $selectedCategory = $this->_request->category;
        }
        $keywords = $this->_request->getParam('keywords');
        $search = new Marketplace_Model_SearchService();
        $this->view->keywords = $keywords;
        $hits = $search->search($keywords);
        $productsIdArray = array();
        foreach ($hits as $hit){
            $productsIdArray[] = $hit->product_id;
        }
        if (!empty ($keywords) && count($productsIdArray) == 0) {
            $productsIdArray[] = 0;
        }
        switch ($this->_request->tab) {
            case 'no-approved':
                $query = $this->_serviceLayer->getMapper()->findAllByApprove(1, true, $productsIdArray, NULL, NULL, $selectedCategory);
                break;
            case 'approved':
                $query = $this->_serviceLayer->getMapper()->findAllByApprove(2, true, $productsIdArray, NULL, NULL, $selectedCategory);
                break;
            case 'all':
            default:
                $query = $this->_serviceLayer->getMapper()->findAllByApprove(0, true, $productsIdArray, NULL, NULL, $selectedCategory);
                break;
        }
        $paginator =  $this->_helper->paginator->getPaginator($query);
        $this->view->paginator = $paginator;
        $this->view->products = $paginator->getCurrentItems();
        $this->view->tab = $this->_request->tab;
    }

    public function approveAction()
    {
        if ($this->_request->id) {
            $this->_serviceLayer->findModelById($this->_request->getParam('id'));
            if ($this->_serviceLayer->approve($this->_request->value)) {
                 $this->_helper->redirector->gotoRoute(array(
                        'module' => $this->_request->getModuleName(),
                        'controller' => 'products',
                        'action' => 'list-admin',
                        'tab' => 'all'),
                    'default', true);
            }
        } else {
            $this->_forward('not-found', 'error', 'default');
        }
    }

    public function viewAction() {
        parent::viewAction();
        $this->view->MarketpalceCategoriesChain($this->_serviceLayer->getModel()->Categories->getFirst()->id);
    }

    public function getDescriptionAction() {
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $result = array('success' => true, 'text' => $this->_serviceLayer->getModel()->description);
        $this->view->result = $result;
    }
}