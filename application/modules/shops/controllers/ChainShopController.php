<?php

class Shops_ChainShopController extends Zend_Controller_Action
{
    protected $_serviceLayer;

    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer('shops','chainShop');
    }


    /**
     *  New Product
     *
     *  @return void
     */
    public function newAction()
    {
        $this->view->setTitle('Новая сеть магазинов');
        $form = $this->_serviceLayer->getForm('new');
        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormNew($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list'),
                        'default', true
                     );
                }
         }
         $this->view->form = $form;
    }

    /**
     *  View Shop
     *
     *  @return void
     */
    public function listAction()
    {
        $this->view->setTitle('Сеть магазинов');
        $chainService = new Shops_Model_ChainShopService();
        $user = $this->_helper->getServiceLayer('users','user')->getUserByAuth();
        $this->view->user = $user;
        $this->view->chains = $chainService->getMapper()->findByUserId($user->id);
    }

}