<?php

class Users_IndexController extends Users_Controller_Abstract_IndexExtended
{

    public function profileAction()
    {
        $this->view->setTitle('Профиль');
        $user = new Users_Model_UserService();
        if ($this->_request->id) {
            $user->findUserById($this->_request->id);
        } else {
            $user->findUserByAuth();
        }
        $this->view->user = $user->getModel();
    }

    public function profileEditAction()
    {
        $this->view->setTitle(_('Редактирование профиля'));
        $user = $this->_helper->getServiceLayer('users', 'user');
        $user->findUserByAuth();

        if ($this->_helper->isAllowed($user->getModel(), 'edit')) {

            $user->setFormInstance('edit', new Users_Form_User_ProfileEdit($user));

            $user->getForm('edit')->getElement('id')->setValue($user->getModel()->id);

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $user->processFormEdit($postData);
                if ($formResult == true) {
                    $this->_helper->FlashMessenger($this->view->translate('Изменения профиля сохранены.'));
                    $this->_helper->redirector->gotoRoute(array(
                        'module' => 'users',
                        'controller' => 'index',
                        'action' => 'profile'), 'default');
                }
            }
            $form = $user->getForm('edit');
            $form->populate($user->getModel()->toArray());
            $this->view->form = $form;
            $this->view->user = $user->getModel();
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    public function viewAction()
    {
        $this->_helper->layout->setLayout('admin');
        $user = new Users_Model_UserService();
        if ($this->_request->id) {
            $user->findUserById($this->_request->id);

            $this->view->setTitle('Пользователь %s', array($user->getModel()->login));
            $this->view->user = $user->getModel();
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    public function listAction() {
        $this->_helper->layout->setLayout('admin');
        parent::listAction();
    }

    public function editAction() {
        $this->_helper->layout->setLayout('admin');
        parent::editAction();
    }

    public function deleteAction() {
        $this->_helper->layout->setLayout('admin');
        parent::deleteAction();
    }

    public function passwordChangeAction() {
        parent::passwordChangeAction();
        $this->view->BreadCrumbs()->appendBreadCrumb('Профиль', $this->view->url(array(
                 'module'=>'users',
                 'controller'=>'index',
                 'action'=>'profile',
             ), 'default', true));
    }
    /**
     * Reset password
     *
     * @return void
     */
    public function passwordChangeAdminAction()
    {
        $user = $this->_helper->getServiceLayer('users','user');
        if ($this->_request->id) {
            $this->_helper->layout->setLayout('admin');
            /* @var $user Users_Model_UserService */
            $user->findUserById($this->_request->id);
            $this->view->setTitle(_('Изменение пароля пользователя %s'), array($user->getModel()->login));
            if ($this->getRequest()->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $user->processFormPasswordChangeAdmin($postData);
                if ($formResult == true) {
                    $this->_helper->FlashMessenger(
                            $this->view->translate('Пароль успешно изменен.')
                    );
                    $this->_helper->redirector->gotoRoute(
                        array(
                            'module' => 'shops',
                            'controller' => 'index',
                            'action' => 'view-admin',
                            'id' => $this->_request->getParam('shop-id')),
                        'default',
                        true
                     );
                }
            }
            $this->view->form = $user->getForm('passwordChangeAdmin');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     * Создание пользователя
     */
    public function createUserAction()
    {
        $this->_helper->layout->setLayout('admin');
        $user = $this->_helper->getServiceLayer('users', 'user');
        /* @var $form Users_Form_User_CreateUser */
        $form = $user->getForm('createUser');
        $form->removeElement('foo');
        if ($this->_request->role && $this->_request->role == Users_Model_User::ROLE_MERCHANT) {
            $form->getElement('role')->setValue(Users_Model_User::ROLE_MERCHANT);
            $this->view->setTitle(_('Регистрация фирмы'));
        } else {
            $this->view->setTitle(_('Регистрация пользователя'));
        }
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            $formResult = $user->processFormCreate($formData);
            if ($formResult == true) {
                $this->_helper->FlashMessenger(
                    $this->view->translate('Пользователь успешно создан.')
                );
                $this->_helper->redirector->gotoRoute(array(
                        'module' => 'users',
                        'controller' => 'index',
                        'action' => 'list'),
                    'default', true
                );
            }
        }
        $this->view->form = $form;
    }

}