<?php

abstract class Comments_Controller_Abstract_Index extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->ajaxContext
                ->addActionContext('new', 'json')
                ->addActionContext('reply', 'json')
                ->addActionContext('edit', 'json')
                ->addActionContext('delete', 'json')
                ->initContext();
    }

     /**
     * Ответ на комментарий
     *
     * @return void
     */
    public function replyAction()
    {
        $this->_doNewReply();
    }

    /**
     * Новый на комментарий
     *
     * @return void
     */
    public function newAction()
    {
        $this->_doNewReply();
    }




    /**
     * Ответ на комментарий
     *
     * @return void
     */
    public function editAction()
    {
        if ($this->_request->isPost()) {
            $comment = $this->_helper->getServiceLayer('comments','comment');
            $postData = $this->_request->getPost();
            $result = $comment->processFormEdit($postData);
            if ($result) {
                $this->view->result = true;
                $this->view->content = $comment->getModel()->text;
            } else {
                $this->view->result = false;
                $this->view->error = $this->view->translate("Произошла ошибка при сохранении комментария");
            }
        }
    }

    /**
     * Удаление
     */
    public function deleteAction()
    {
        if ( $this->_request->isPost()) {
            $comment = $this->_helper->getServiceLayer('comments','comment');
            $result = $comment->processFormDelete((int)$this->_request->id);
            if ($result) {
                $this->view->result = true;
            } else {
                $this->view->result = false;
                $this->view->error = $this->view->translate("Произошла ошибка при удалении комментария");
            }
        }
    }

    /**
     * 
     */
    private function _doNewReply() {
        if ($this->_request->isPost()) {
            $comment = $this->_helper->getServiceLayer('comments','comment');
            $postData = $this->_request->getPost();
            $result = $comment->processFormReply($postData);
            if ($result) {
                $this->view->comment = $comment->getModel();
                $response['result'] = true;
                $response['content'] = $this->view->render('comments/' . $this->_request->getActionName() . '.phtml');
            } else {
                $response['result'] = false;
                $response['error'] = $this->view->translate("Произошла ошибка при сохранении комментария");
            }
            // Очищаем лишние переменные вьюшки
            $this->view->clearVars();
            // Добавляем переменные для ответа
            $this->view->assign($response);
        }
    }
}