<?php
abstract class Shops_Controller_Abstract_IndexBase extends Zend_Controller_Action
{
    protected $_serviceLayer;

    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer($this->_request->getModuleName(),'shop');
        
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
        $this->view->setTitle('Данные о фирме');

        $userServiceLayer = new Users_Model_UserService();
        $userServiceLayer->findUserByAuth();
        $form = $this->_serviceLayer->getForm('new');
        if (($id = $this->_request->getParam('id')) && $this->view->isAllowed('mvc:shops:index', 'admin')) {
            $user = $userServiceLayer->getUserById($id);
            $form->setDefaults(array(
                'user_id' => $user->id,
                'email' => $user->email
            ));
        } else {
            $user = $userServiceLayer->getModel();
            $form->setDefaults();
        }

        $form->getElement('chain_shop_id')->addMultiOptions($user->ChainShop->toKeyValueArray('id', 'name'));

        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormNew($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'profile',
                            'id' => $this->_serviceLayer->getModel()->id),
                        'default', true
                     );
                }
         }
         $this->view->form = $form;
         $this->view->user = $user;
    }

    /**
     *  Edit Product
     *
     *  @return void
     */
    public function editAction()
    {
        $this->view->setTitle('Изменить');

        if ($this->_request->id) {
            $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        } else {
            $this->_forward('not-found', 'error', 'default');
            return;
        }

        if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'edit')) {
            $this->view->shop = $this->_serviceLayer->getModel();
            $form = $this->_serviceLayer->getForm('edit');

            $form->getElement('chain_shop_id')->addMultiOptions($this->_serviceLayer->getModel()->User->ChainShop->toKeyValueArray('id', 'name'));

            if ($this->_request->isPost()) {
                    $postData = $this->_request->getPost();
                    $result = $this->_serviceLayer->processFormEdit($postData);
                    if ($result == true) {
                        $this->_helper->redirector->gotoRoute(array(
                                'module' => $this->_request->getModuleName(),
                                'controller' => $this->_request->getControllerName(),
                                'action' => 'profile',
                                'id' => $this->_serviceLayer->getModel()->id),
                            'default', true
                         );
                    }
             } else {
                 $this->_serviceLayer->getForm('edit')->populate($this->_serviceLayer->getModel()->toArray());
             }
             $this->view->form = $form;
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  Delete Shop
     *
     *  @return void
     */
    public function deleteAction()
    {
        if (!$this->_request->action_redirect) {
            $this->_request->setParam('action_redirect', 'new');
        }

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $this->_helper->layout->setLayout('admin');
        if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'delete')) {
            $this->view->setTitle('Удалить магазин "%s"?', array($this->_serviceLayer->getModel()->name));

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $this->_serviceLayer->processFormDelete($postData);
                $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => $this->_request->action_redirect),
                    'default', true);
            }
            $this->view->form = $this->_serviceLayer->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  View Shop
     *
     *  @return void
     */
    public function profileAction()
    {
        if ($id = $this->_request->getParam('id')) {
            $this->_serviceLayer->findModelById($id);

            if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'view-profile')) {
                $this->view->shop = $this->_serviceLayer->getModel();
                $this->view->setTitle('Профиль магазина %s', $this->_serviceLayer->getModel()->name);

                if ($this->_serviceLayer->getModel()->price_status != Shops_Model_Shop::PRICE_STATUS_QUEUE) {
                    $price = $this->_helper->getServiceLayer('catalog','price');
                    if ($this->_request->isPost()) {
                        $postData = $this->_request->getPost();
                        if ($price->processFormUpload($postData, $this->_serviceLayer->getModel())) {
                            $this->_helper->FlashMessenger->addMessages($price->getMessages());
                            $this->_helper->redirector->gotoRoute(array(
                                'module' => $this->_request->getModuleName(),
                                'controller' => $this->_request->getControllerName(),
                                'action' => 'profile',
                                'id' => $postData['shop_id']),
                            'default');
                        }
                    }
                    $price->getForm('upload')->shop_id->setValue($this->_serviceLayer->getModel()->id);
                    $this->view->priceForm = $price->getForm('upload');
                }
            } else {
                $this->_forward('denied', 'error', 'default');
            }
        } else {
            $this->view->setTitle('Мои магазины');
            $userServiceLayer = new Users_Model_UserService();
            $userServiceLayer->findUserByAuth();
            $this->view->user = $userServiceLayer->getModel();
            if ($userServiceLayer->getModel()->role == Users_Model_User::ROLE_MANAGER) {
                $this->view->shops = $userServiceLayer->getModel()->Shop;
            } else {
                $this->view->shops = $userServiceLayer->getModel()->UserShop;
            }
            $this->view->chain_id = $this->_request->getParam('chain-id');
            $this->render('my-shops-list');
        }
    }

      /**
     *  View Shop
     *
     *  @return void
     */
    public function viewAction()
    {
        $this->profileAction();

        $this->view->setTitle($this->_serviceLayer->getModel()->name);
    }

}