<?php

class SettingsController extends Zend_Controller_Action
{

    protected $_serviceLayer;
    
    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer($this->_request->getModuleName(),'settings');
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle('Настройка');

         if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $form = $this->_serviceLayer->getForm('edit');
                $formResult = $this->_serviceLayer->processFormEdit($postData);
                $this->_helper->redirector->gotoRoute(array(
                        'module' => $this->_request->getModuleName(),
                        'controller' => $this->_request->getControllerName(),
                        'action' => 'index'
                    ),'default', true);
         }
         $this->view->form = $this->_serviceLayer->getForm('edit');
    }
}

