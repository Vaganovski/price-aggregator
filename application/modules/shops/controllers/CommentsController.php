<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

class Shops_CommentsController extends Comments_Controller_Abstract_Index
{
    public function myAction()
    {
        $this->view->setTitle(_('Мои отзывы'));
        $this->view->BreadCrumbs()->appendBreadCrumb('Профиль', $this->view->url(array(
                 'module'=>'shops',
                 'controller'=>'index',
                 'action'=>'profile',
             ), 'default', true));
        if ($this->_request->id) {
             $this->view->shopId = $this->_request->id;
            $userServiceLayer = new Users_Model_UserService();
            $userServiceLayer->findUserByAuth();
            $this->view->user = $userServiceLayer->getModel();
        } else {
            $this->_forward('not-found', 'error', 'default');
        }

    }

    /**
     * list all comments about user action
     */
    public function listAdminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle(_('Отзывы к продавцам'));

        $shopCommentService = new Shops_Model_CommentService();
        switch ($this->_request->tab) {
            case 'deleted':
                $comments = $shopCommentService->getMapper()->findAll('deleted', $asQuery = true);
                break;
            case 'new':
            default:
                $comments = $shopCommentService->getMapper()->findAll('new', $asQuery = true);
                break;
        }
        

        $paginator = $this->_helper->paginator->getPaginator($comments);
        $this->view->paginator = $paginator;
        $this->view->comments = $paginator->getCurrentItems();
        $this->view->tab = $this->_request->tab;

        $messageDelete = $this->view->translate("Вы уверены, что хотите удалить комментарий?");

        $script = 'var commentsMessageDelete = "' . $messageDelete . '"';
        // Добавляем пути к скриптам представления
        $this->view->headScript()->appendScript($script);
    }


        /**
     * Ответ на комментарий
     *
     * @return void
     */
    public function editAdminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle('Редактировать комментарий');
        $shopCommentService = new Shops_Model_CommentService();
        $shopCommentService->findModelById($this->_request->getParam('id'));

        $form = $shopCommentService->getForm('edit');
        if ($shopCommentService->getModel()->level == 2) {
           $form->removeElement('mark');
        }
        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $shopCommentService->processFormEditAdmin($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin',
                            'tab' => 'new'),
                        'default', true
                     );
                }
         } else {
             $form->populate($shopCommentService->getModel()->toArray());
         }
         $this->view->form = $form;
    }

    public function newAction() {
        parent::newAction();
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('Cache');
        $cacheCore = $cache->core;
        $cacheCore->remove(strtolower('Shops_View_Helper_ShopsCount'));
    }
}
