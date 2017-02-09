<?php

abstract class Users_Controller_Abstract_Mailer extends Zend_Controller_Action
{
    /**
     *  Action New
     */
    public function newAction()
    {
        $title = $this->view->translate('New Mailing');
        $this->view->headTitle($title);
        $this->view->title = $title;

        $mailer = $this->_helper->getServiceLayer('users','mailer');
        $user   = $this->_helper->getServiceLayer('users','user');

        $form = $mailer->getForm('new');

        //  вытаскиваем роли и сохраняем их в multiselect
        $form->getElement('user_role')->setMultiOptions(
            $user->getMapper()
                ->getAllRoles()
                ->toKeyValueArray('role', 'role')
        );

        if ($this->_request->isPost()) {
            $postData = $this->_request->getPost();

            $count = $mailer->processFormNew($postData, $user);
            if ($count != false) {
                $this->_helper->FlashMessenger('Письма отосланы [ Counts: ' . count($recipients) . ']');
                $this->_helper->redirector->gotoRoute(array(
                                    'module' => $this->_request->getModuleName(),
                                    'controller' => $this->_request->getControllerName(),
                                    'action' => 'new'),
                                  'default', true);
            } else {
                throw new Exception('There ere no users');
            }
        } else {
            $form->populate();
        }
        $this->view->form = $form;
    }

}