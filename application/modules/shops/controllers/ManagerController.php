<?php
class Shops_ManagerController extends Zend_Controller_Action
{
    protected $_serviceLayer;

    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer('shops','manager');

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
        $this->view->setTitle(_('Добавить менеджера'));

        $form = $this->_serviceLayer->getForm('new');
        $shop = $this->_helper->getServiceLayer($this->_request->getModuleName(),'shop');
        $shop->findModelById($this->_request->getParam('shop-id'));

        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $postData['shop_id'] = $shop->getModel()->id;
                $result = $this->_serviceLayer->processFormNew($postData);
                if ($result) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => 'index',
                            'action' => 'profile',
                            'id' =>  $shop->getModel()->id),
                        'default', true
                     );
                }
         }
         $this->view->form = $form;
         $this->view->shop = $shop->getModel();
    }

    /**
     *  Edit Product
     *
     *  @return void
     */
    public function editAction()
    {
        $this->view->setTitle(_('Редактировать менеджера'));

        $this->_serviceLayer->findUserById($this->_request->getParam('id'));

        $shop = $this->_serviceLayer->getModel()->Shop->getFirst();

        if ($shop && $this->_helper->IsAllowed($shop, 'edit')) {
            $form = $this->_serviceLayer->getForm('edit');
            if ($this->_request->isPost()) {
                    $postData = $this->_request->getPost();
                    $result = $this->_serviceLayer->processFormEdit($postData);
                    if ($result == true) {
                        $this->_helper->redirector->gotoRoute(array(
                                'module' => $this->_request->getModuleName(),
                                'controller' => 'index',
                                'action' => 'profile',
                                'id' => $shop->id),
                            'default', true
                         );
                    }
             } else {
                 $this->_serviceLayer->getForm('edit')->populate($this->_serviceLayer->getModel()->toArray());
             }
             $this->view->form = $form;
             $this->view->shop = $shop;
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }
}