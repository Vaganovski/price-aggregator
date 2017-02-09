<?php

abstract class Products_Controller_Abstract_Index extends Zend_Controller_Action
{
    protected $_serviceLayer;
    protected $_processResult;

    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer($this->_request->getModuleName(),'product');
        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->controllerName = $this->_request->getControllerName();
    }
    /**
     *  New Product
     *
     *  @return void
     */
    public function newAction()
    {
        $this->view->setTitle('Добавить товар');
     
        $form = $this->_serviceLayer->getForm('new');

        if (!$this->_request->action_redirect) {
            $this->_request->setParam('action_redirect', 'view');
        }

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormNew($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => $this->_request->action_redirect,
                            'id' => $this->_serviceLayer->getModel()->id),
                        'default', true
                     );
                }
                $this->_processResult = $result;
         } 
         $this->view->form = $form;
    }

    /**
     *  Edit Product
     *
     *  @return void
     */
    public function editAction()
    {
        $this->view->setTitle('Редактировать товар');

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $form = $this->_serviceLayer->getForm('edit');

        if (!$this->_request->action_redirect) {
            $this->_request->setParam('action_redirect', 'view');
        }

        if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'delete')) {
            if ($this->_request->isPost()) {

                   $postData = $this->_request->getPost();
                   $result = $this->_serviceLayer->processFormEdit($postData);
                   if ($result == true) {
                       $this->_helper->redirector->gotoRoute(array(
                               'module' => $this->_request->getModuleName(),
                               'controller' => $this->_request->getControllerName(),
                               'action' => $this->_request->action_redirect,
                               'id' => $this->_serviceLayer->getModel()->id),
                           'default', true
                        );
                   }
                   $this->_processResult = $result;
            } else {
                $this->_serviceLayer->getForm('edit')->populate($this->_serviceLayer->getModel()->toArray());
            }
            $this->view->form = $form;
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  View Product
     *
     *  @return void
     */
    public function viewAction()
    {
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $this->view->setTitle($this->_serviceLayer->getModel()->name);
        $this->view->product = $this->_serviceLayer->getModel();
    }

    /**
     *  Delete Product
     *
     *  @return void
     */
    public function deleteAction()
    {
       $product = $this->_helper->getServiceLayer($this->_request->getModuleName(),'product');
        $product->findModelById($this->_request->getParam('id'));

        if ($this->_helper->isAllowed($product->getModel(), 'delete')) {
            $this->view->setTitle('Удалить товар "%s"?', array($product->getModel()->name));
            
            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $product->processFormDelete($postData);
                $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'new'),
                    'default');
            }
            $this->view->form = $product->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }
}