<?php

class Shops_IndexController extends Shops_Controller_Abstract_IndexBase
{

    public function init() {
        parent::init();
        $this->view->headLink()->appendStylesheet('/stylesheets/sellers.css')
                   ->appendStylesheet('/stylesheets/sellers-ie6.css', 'screen', 'IE 6');
    }

    public function deleteAction() {
        $this->_request->setParam('action_redirect', 'list-admin');
        parent::deleteAction();
    }

   /**
     *  View Shop
     *
     *  @return void
     */
    public function viewAdminAction()
    {
        if ($this->_request->id) {
            $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        } else {
            $this->_forward('not-found', 'error', 'default');
        }
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle('Фирмы / Продавцы');
        $this->view->shop = $this->_serviceLayer->getModel();
    }

    public function viewAction() {
        $this->viewAdminAction();
        $this->_helper->layout->setLayout('layout');
        $this->view->setTitle($this->_serviceLayer->getModel()->name);
        if ($this->_serviceLayer->isAvailable()) {
            $this->view->BreadCrumbs()->appendBreadCrumb('Продавцы', $this->view->url(array(
                                         'module'=>'shops',
                                         'controller'=>'index',
                                         'action'=>'index'
                                     ), 'default', true));
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }
    /**
     *  Edit Product
     *
     *  @return void
     */
    public function editImageAction()
    {
        $this->view->setTitle('Изменить логотип');

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $form = $this->_serviceLayer->getForm('editImage');
        
        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormEditImage($postData);
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
             $this->_serviceLayer->getForm('editImage')->populate($this->_serviceLayer->getModel()->toArray());
         }
         $this->view->form = $form;
         $this->view->shop = $this->_serviceLayer->getModel();
    }

    /**
     *  Disable Shop
     *
     *  @return void
     */
    public function disableAction()
    {
        $this->_helper->layout->setLayout('admin');
        
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        //if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'disable')) {
            $this->view->setTitle('Отключить магазин "%s"?', array($this->_serviceLayer->getModel()->name));

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $this->_serviceLayer->processFormDisable($postData);
                if ($formResult) {
                    $this->_serviceLayer->updatePrices();
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin',
                            'status' => 'disable'),
                        'default', true
                     );
                }
            }
            $this->view->form = $this->_serviceLayer->getForm('reject');
            $this->view->form->populate(array('email' => $this->_serviceLayer->getModel()->email));
        //} else {
        //    $this->_forward('denied', 'error', 'default');
        //}
    }

    /**
     *  Disable Shop
     *
     *  @return void
     */
    public function availableAction()
    {
        $this->_helper->layout->setLayout('admin');

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $this->view->setTitle('Активизировать магазин "%s"?', array($this->_serviceLayer->getModel()->name));

        if ($this->_request->isPost()) {
            $postData = $this->_request->getPost();
            $formResult = $this->_serviceLayer->processFormAvailable($postData);
            if ($formResult) {
                $this->_serviceLayer->updatePrices();
                $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->config;
                $emailNoReply = $config->feedback->emailSender;
                $message = $this->view->render('mails/available.phtml');
                $subject = $this->view->translate('Учетная запись на eprice.kz активирована');
                $feedback = new Feedback_Model_FeedbackService();
                $feedback->send($message, $subject, $this->_serviceLayer->getModel()->email, $emailNoReply);
            }
            $this->_helper->redirector->gotoRoute(array(
                    'module' => $this->_request->getModuleName(),
                    'controller' => $this->_request->getControllerName(),
                    'action' => 'list-admin',
                    'status' => 'available'),
                'default', true
            );
        }
        $this->view->form = $this->_serviceLayer->getForm('delete');
    }

    /**
     *  Renewal date
     *
     *  @return void
     */
    public function renewalAction()
    {

        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle('Фирмы / Продавцы');

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $form = $this->_serviceLayer->getForm('renewal');

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormRenewal($postData);
                if ($result == true) {
                    $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->config;
                    $emailNoReply = $config->feedback->emailSender;
                    $this->view->period = $this->_serviceLayer->getModel()->period;
                    $message = $this->view->render('mails/renewal.phtml');
                    $subject = $this->view->translate('Учетная запись на eprice.kz продлена');
                    $feedback = new Feedback_Model_FeedbackService();
                    $feedback->send($message, $subject, $this->_serviceLayer->getModel()->email, $emailNoReply);
//                    $this->_serviceLayer->updatePrices();
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin',
                            'status' => 'available'),
                        'default', true
                     );
                }
         } else {
             $this->_serviceLayer->getForm('renewal')->populate($this->_serviceLayer->getModel()->toArray());
         }
         $this->view->form = $form;
         $this->view->shop = $this->_serviceLayer->getModel();
    }



    /**
     *  Alphabet shops list
     *
     *  @return void
     */
    public function indexAction()
    {
        $this->view->setTitle('Список продавцов');
        
        $shops = $this->_serviceLayer->getMapper()->findAllAvailableOrderByName($this->_request->city, $this->_request->withComments);
        $shopsByLetter = null;
        foreach ($shops as $shop) {
            $letter = mb_substr($shop->chain_name, 0, 1, 'UTF-8');
            if (is_numeric($letter)) {
                $key = '0-9';
            } else {
                $key = mb_strtoupper($letter, 'UTF-8');
            }
            $shopsByLetter[strtoupper($key)][] = $shop;
        }
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }
        $this->view->shopCount = $shops->count();
        $this->view->shopsByLetter = $shopsByLetter;
    }


    /**
     *  Lists shops
     *
     *  @return void
     */
    public function listAdminAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');
        $this->view->setTitle('Фирмы / Продавцы');

        if ($this->_request->status == 'disable' || $this->_request->status == 'available' ||
            $this->_request->status == 'new') {
            $status = $this->_request->status;
        } else {
            $status = 'new';
        }
        
        if ($status == 'new') {
            $price = $this->_request->price;
        } else {
            $price = null;
        }
        
        if ($this->_request->status == 'available') {
            $orderBy = 'untill_date';
            $sortOrder = 'ASC';
        } else {
            $orderBy = 'created_at';
            $sortOrder = 'DESC';
        }

        $keywords = $this->_request->getParam('keywords', null);
        if ($userId = $this->_request->getParam('id')) {
            $query = $this->_serviceLayer->getMapper()->findAllByUserAsQuery($userId);
            $userService = new Users_Model_UserService();
            $this->view->user = $userService->getUserById($userId);
        } else {
            /* @var $query Doctrine_Query */
            $query = $this->_serviceLayer->getMapper()->findAllByStatusAsQuery($status, $orderBy, $sortOrder, $keywords, $price);
        }
        
        $paginator =  $this->_helper->paginator->getPaginator($query);

        $this->view->paginator = $paginator;
        
        $this->view->shops = $paginator->getCurrentItems();
        $this->view->status = $status;
    }

    /**
     * Edit shop
     */
    public function editAction() {
        parent::editAction();
        $this->view->shopId = $this->_serviceLayer->getModel()->id;
    }


    /**
     *  View Shop
     *
     *  @return void
     */
    public function chainViewAction()
    {
        if ($id = $this->_request->getParam('id')) {
            $chainShopService = $this->_helper->getServiceLayer('shops','chainShop');
            $chainShopService->findModelById($id);
            $this->view->shops = $chainShopService->getModel()->Shops;
            $this->view->setTitle('Сеть магазинов %s', $chainShopService->getModel()->name);
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  Disable and delete Shop
     *
     *  @return void
     */
    public function disableAndDeleteAction()
    {

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'delete')) {
            $this->view->setTitle('Удалить магазин "%s"?', array($this->_serviceLayer->getModel()->name));

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $this->_serviceLayer->processFormDisable($postData);
                
                if ($formResult) {
                    $this->_serviceLayer->updatePrices();
                }

                $formResult = $this->_serviceLayer->processFormDelete($postData);
                $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'profile'),
                    'default', true);
            }
            $this->view->form = $this->_serviceLayer->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }
}