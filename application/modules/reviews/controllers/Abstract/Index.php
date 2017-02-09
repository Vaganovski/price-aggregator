<?php

abstract class Reviews_Controller_Abstract_Index extends Zend_Controller_Action
{
    protected $_serviceLayer;
    protected $_processResult;
    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer($this->_request->getModuleName(),'review');
        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->controllerName = $this->_request->getControllerName();
    }

    /**
     *  New Brand
     *
     *  @return void
     */
    public function newAction()
    {
        $this->view->setTitle(_('Написать отзыв'));

        $form = $this->_serviceLayer->getForm('new');

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $this->_processResult = $this->_serviceLayer->processFormNew($postData);
         }
         $this->view->form = $form;
    }

    /**
     *  Edit Brand
     *
     *  @return void
     */
    public function editAction()
    {
        $this->view->setTitle('Редактировать отзыв');

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $form = $this->_serviceLayer->getForm('edit');

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormEdit($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'view',
                            'id' => $this->_serviceLayer->getModel()->id),
                        'default', true
                     );
                }
         } else {
             $this->_serviceLayer->getForm('edit')->populate($this->_serviceLayer->getModel()->toArray());
         }
         $this->view->form = $form;
    }


    /**
     *  Delete Brand
     *
     *  @return void
     */
    public function deleteAction()
    {
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'delete')) {
            $this->view->setTitle('Удалить отзыв о товаре "%s"?', array($this->_serviceLayer->getModel()->comment));

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $this->_serviceLayer->processFormDelete($postData);
                $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'new'),
                    'default');
            }
            $this->view->form = $this->_serviceLayer->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  Просмотр бренда
     *
     *  @return void
     */
    public function viewAction() {
        $review = $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $this->view->setTitle(_('Отзыв'));
        $this->view->review = $review->getModel();
    }
}