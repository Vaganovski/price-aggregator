<?php
/**
 *  Index Controller for module Pages
 */
class Pages_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * New page
     *
     * @return void
     */
    public function newAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle('Новая страница');
        $page = $this->_helper->getServiceLayer('pages','page');

        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $form = $page->getForm('new');
                $formResult = $page->processFormNew($postData);
                if ($formResult == true) {
                    $this->_helper->redirector->gotoRoute(array(
                        'alias' => $page->getModel()->alias),
                      'pages-view', true);
                } else {

                }
         }
         $this->view->form = $page->getForm('new');
    }

    /**
     * Edit page
     *
     * @return void
     */
    public function editAction()
    {
        $this->_helper->layout->setLayout('admin');
        $title = $this->view->translate('Редактировать страницу');
        $this->view->headTitle($title);
        $this->view->title = $title;

        $pageId = (int)$this->_request->getParam('id');
        $page = $this->_helper->getServiceLayer('pages','page');
        $page->findPageById($pageId);

        $form = $page->getForm('edit');
        $form->getElement('id')->setValue($pageId);
        if ($this->_request->isPost()) {
            $postData = $this->_request->getPost();
            $formResult = $page->processFormEdit($postData);
            $this->_helper->redirector->gotoRoute(array('alias' => $page->getModel()->alias),
                                                  'pages-view', true);
         } else {
            $page->getModel()->Translation;
            $form->populate($page->getModel()->toArray());
         }
         $this->view->form = $form;
    }

    /**
     * View page
     *
     * @return void
     */
    public function viewAction()
    {
        $page = $this->_helper->getServiceLayer('pages','page');
        $pageAlias = $this->_request->getParam('alias');
        $page->findPageByAlias($pageAlias);
        $title = $page->getModel()->title;
        $this->view->title = $title;
        $this->view->headTitle($title);

        $this->view->page = $page->getModel();
    }

    /**
     * Delete page
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->layout->setLayout('admin');
        $title = $this->view->translate('Удалить старинцу');
        $this->view->headTitle($title);
        $this->view->title = $title;

        $page = $this->_helper->getServiceLayer('pages','page');
        $pageId = (int)$this->_request->getParam('id');
        $page->findPageById($pageId);

        $title = sprintf($this->view->translate('Вы хотите удалить страницу "%s"?'),
                $page->getModel()->title);
        $this->view->title = $title;
        $this->view->form = $page->getForm('delete');

        if ($this->_request->isPost()) {

            $postData = $this->_request->getPost();
            $page->processFormDelete($postData);

            $this->_helper->redirector->gotoRoute(array(
                                                    'module' => 'pages',
                                                    'controller'=>'index',
                                                    'action'=>'list'),
                                              'default', true);

        }
    }

    /**
     * View page
     *
     * @return void
     */
    public function listAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle('Страницы');

        $page = $this->_helper->getServiceLayer('pages','page');
        $query = $page->getMapper()->findAllAsQuery();

        // @todo paginator option from config
        $paginator =  $this->_helper->paginator->getPaginator($query, null, 5);

        $this->view->paginator = $paginator;
        $this->view->pages = $paginator->getCurrentItems();
    }

    /**
     * View page
     *
     * @return void
     */
    public function pricesAction()
    {
        $this->view->setTitle('Размещение прайсов');

        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $user = $this->_helper->getServiceLayer('users','user');
            $this->view->form = $user->getForm('merchantLogin');
        }
    }

    /**
     * View page
     *
     * @return void
     */
    public function sitemapAction()
    {
        $this->view->setTitle(_('Карта сайта'));
    
    }
}