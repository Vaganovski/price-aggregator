<?php
abstract class Users_Controller_Abstract_IndexExtended extends Users_Controller_Abstract_IndexBase
{
    /**
     * Registration user
     *
     * @return void
     */
    public function registrationAction()
    {
        /* @var $user Users_Model_UserService */
        $user = $this->_helper->getServiceLayer('users', 'user');
        $form = $user->getForm('registration');
        if ($this->_request->role && $this->_request->role == Users_Model_User::ROLE_MERCHANT) {
            $form->getElement('role')->setValue(Users_Model_User::ROLE_MERCHANT);
            $this->view->setTitle(_('Регистрация фирмы'));
        } else {
            $this->view->setTitle(_('Регистрация пользователя'));
        }
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            $formResult = $user->processFormRegistration($formData);
        }
        $this->view->form = $form;
    }


    /**
     * Activation user
     *
     * @return void
     */
    public function activationAction()
    {
        $this->view->setTitle(_('Активация учетной записи'));
        
        if (! ($code = $this->getRequest()->getParam('code'))) {
            throw new Exception($this->view->translate('Не указан код активации.'));
        }
        if (! ($id = $this->getRequest()->getParam('id'))) {
            throw new Exception($this->view->translate('Не указан идентификатор пользователя.'));
        }

        /* @var $user Users_Model_UserService */
        $user = $this->_helper->getServiceLayer('users','user');
        $user->findUserById($id);

        if ($user->checkActivation($code)) {
            $this->_helper->FlashMessenger($this->view->translate('Учетная запись успешно активирована.'));
            if ($user->getModel()->role == Users_Model_User::ROLE_MERCHANT) {
                 $user->auth();
                 $this->_helper->redirector->gotoRoute(array(
                        'module' => 'shops',
                        'controller' => 'index',
                        'action' => 'new'),
                    'default', true
                 );
            } else {
                $this->_helper->redirector->gotoUrl('/');
            }
        }
    }


    /**
     * Users list
     *
     * @return void
     */
    public function listAction()
    {
        $this->view->setTitle(_('Пользователи'));

        $user = $this->_helper->getServiceLayer('users', 'user');

        $query = $user->getMapper()->findAllAsQuery();

        $paginator =  $this->_helper->paginator->getPaginator($query);

        $this->view->paginator = $paginator;

        $this->view->users = $paginator->getCurrentItems();
    }

}