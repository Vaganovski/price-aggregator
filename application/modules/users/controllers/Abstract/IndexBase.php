<?php
abstract class Users_Controller_Abstract_IndexBase extends Zend_Controller_Action
{
    public function init() {
        parent::init();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('login', 'json')->initContext();
    }

    /**
     * Login user
     *
     * @return void
     */
    public function loginAction()
    {
        $this->view->setTitle(_('Авторизация'));

        // @todo use ajax for validate

        $user = $this->_helper->getServiceLayer('users','user');
        if ($this->_request->isPost()) {
            $postData = $this->_request->getPost();
            $formResult = $user->processFormLogin($postData);

            if ($formResult == true) {
                if ($this->_request->isXmlHttpRequest()) {
                    $result['success'] = true;
                } else {
                    if ($referer = $this->_request->getServer('HTTP REFERER')) {
                        $this->_redirect($referer);
                    } else {
                        $this->_redirect('/');
                    }
                }
            } else {
                // @todo show message
//                $this->_helper->FlashMessenger(
//                        $this->view->translate('Ошибка авторизации. Проверьте правильность ввода логина и пароля.')
//                );
//                $this->_helper->redirector->gotoRoute(
//                    array(
//                        'module' => 'users',
//                        'controller' => 'index',
//                        'action' => 'login'),
//                    'default',
//                    true
//                 );
                if ($this->_request->isXmlHttpRequest()) {
                    $this->view->result = false;
                    $result['success'] = false;
                    $result['form'] = $user->getForm('login')->render();
                }
            }
        }
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->result = $result;
        } else {
            $this->view->loginForm = $user->getForm('login');
        }
    }

    /**
     * User logout
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->_helper
               ->getServiceLayer('users', 'user')
               ->logout();
        
        $this->_helper->redirector->gotoUrl('/');
    }

    /**
     * Restore password
     *
     * @return value
     */
    public function passwordRestoreAction()
    {
        $this->view->setTitle(_('Восстановление пароля'));

        /* @var $user Users_Model_UserService */
        $user = $this->_helper->getServiceLayer('users','user');
        
        $form = $user->getForm('passwordRestore');
        if ($this->_request->isPost()) {
            $formResult = $user->processFormPasswordRestore($this->_request->getPost());
            if ($formResult == true) {
                $this->_helper->FlashMessenger(
                        $this->view->translate('На ваш e-mail отправлено сообщение с ссылкой для сброса пароля. Ссылка будет действительна два часа.')
                );
                $this->_helper->redirector->gotoUrl('/');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Reset password
     *
     * @return void
     */
    public function passwordResetAction()
    {
        $this->view->setTitle(_('Сброс пароля'));

        if (! ($code = $this->getRequest()->getParam('code'))) {
            throw new Exception($this->view->translate('Не указан код сброса пароля.'));
        }
        if (! ($id = $this->getRequest()->getParam('id'))) {
            throw new Exception($this->view->translate('Неправильная ссылка для сброса пароля.'));
        }

        $user = $this->_helper->getServiceLayer('users','user');
        $user->findUserById($id);
        
        if ($user->checkResetPasswordCode($code)) {

            if ($this->getRequest()->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $user->processFormPasswordReset($postData);
                if ($formResult == true) {
                    $this->_helper->FlashMessenger(
                            $this->view->translate('Пароль успешно изменен.')
                    );
                    $this->_helper->redirector->gotoRoute(
                        array(
                            'module' => 'users',
                            'controller' => 'index',
                            'action' => 'profile'),
                        'default',
                        true
                     );
                }
            }
            $this->view->form = $user->getForm('passwordReset');
        } else {
            throw new Exception($this->view->translate('Неправильный код сброса пароля.'));
        }
    }


    /**
     * Reset password
     *
     * @return void
     */
    public function passwordChangeAction()
    {
        $this->view->setTitle(_('Изменение пароля'));

        $user = $this->_helper->getServiceLayer('users','user');
        $user->findUserByAuth();

        if ($this->getRequest()->isPost()) {
            $postData = $this->_request->getPost();
            $formResult = $user->processFormPasswordChange($postData);
            if ($formResult == true) {
                $this->_helper->FlashMessenger(
                        $this->view->translate('Пароль успешно изменен.')
                );
                $this->_helper->redirector->gotoRoute(
                    array(
                        'module' => 'users',
                        'controller' => 'index',
                        'action' => 'profile'),
                    'default',
                    true
                 );
            }
        }
        $this->view->form = $user->getForm('passwordChange');
        $this->view->user = $user;

    }


    /**
     * Edit user
     *
     * @return void
     */
    public function editAction()
    {
        $this->view->setTitle(_('Редактирование профиля'));
        $user = $this->_helper->getServiceLayer('users', 'user');
        $user->findUserByIdOrAuth($this->getRequest()->getParam('id'));
        
        if ($this->_helper->isAllowed($user->getModel(), 'edit')) {

            $user->getForm('edit')->getElement('id')->setValue($user->getModel()->id);

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $user->processFormEdit($postData);
                if ($formResult == true) {
                    $this->_helper->FlashMessenger($this->view->translate('Изменения профиля сохранены.'));
                    $this->_helper->redirector->gotoRoute(array(
                        'module' => 'users',
                        'controller' => 'index',
                        'action' => 'list'), 'default');
                }
            }
            $form = $user->getForm('edit');
            $form->populate($user->getModel()->toArray());
            $this->view->form = $form;
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }


    /**
     * Delete user
     *
     * @return void
     */
    public function deleteAction()
    {
        $user = $this->_helper->getServiceLayer('users', 'user');
        $user->findUserByIdOrAuth($this->getRequest()->getParam('id'));

        if ($this->_helper->isAllowed($user->getModel(), 'delete')) {
            $this->view->setTitle(_('Удалить пользователя "%s"?'), $user->getModel()->email);
            
            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $user->processFormDelete($postData);
                $this->_helper->redirector->gotoRoute(
                    array(
                        'module' => 'users',
                        'controller' => 'index',
                        'action' => 'list'),
                    'default',
                    true
                 );
            }
            $this->view->form = $user->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

}
