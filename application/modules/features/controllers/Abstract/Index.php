<?php

abstract class Features_Controller_Abstract_Index extends Zend_Controller_Action
{
    /**
     *  service layer of features set
     */
    protected $set;

    public function init()
    {
        $this->set = $this->_helper->getServiceLayer($this->_request->getModuleName(),'group');
        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->controllerName = $this->_request->getControllerName();
    }

    /**
     *  New features group
     *
     *  @return void
     */
    public function newGroupAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');
        $this->view->setTitle('Создание шаблона характеристик');
        $form = $this->set->getForm('new');

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->set->processFormNewGroup($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin'),
                        'default', true
                     );
                }
        } 
        $this->view->form = $form;
        
        $this->view->headScript()->appendFile('/javascripts/features.js');
    }

    /**
     *  Edit features group
     *
     *  @return void
     */
    public function editGroupAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');
        $this->view->setTitle('Редактирование шаблона характеристик');
        $form = $this->set->getForm('edit');
        $this->set->findModelById($this->_request->getParam('id'));
        
        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->set->processFormEditGroup($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin'),
                        'default', true
                     );
                }
        }
        $this->set->getModel()->Sets;
        $form->populate($this->set->getModel()->toArray());
        $this->view->form = $form;

        $this->view->headScript()->appendFile('/javascripts/features.js');
    }

    /**
     *  Delete features group
     *
     *  @return void
     */
    public function deleteGroupAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');
        $group = $this->_helper->getServiceLayer($this->_request->getModuleName(),'group');
        $group->findModelById($this->_request->getParam('id'));

        if ($this->_helper->isAllowed($group->getModel(), 'delete')) {
            $this->view->setTitle('Удалить шаблон "%s"?', array($group->getModel()->title));

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $group->processFormDeleteGroup($postData);
                $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin'),
                    'default', true);
            }
            $this->view->form = $group->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  List of features
     *
     *  @return void
     */
    public function listAdminAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');
        $group = $this->_helper->getServiceLayer($this->_request->getModuleName(),'group');
        $this->view->setTitle('Шаблоны характеристик');
        $this->view->groups = $group->getMapper()->findAll();

        $this->view->headScript()->appendFile('/javascripts/features.js');
    }

    /**
     *  New features set
     *
     *  @return void
     */
    public function newSetAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');

        $group = $this->_helper->getServiceLayer($this->_request->getModuleName(),'group');
        $group->findModelById($this->_request->getParam('group_id'));

        $this->view->setTitle('Создание набора характеристик');
        $set = $this->_helper->getServiceLayer($this->_request->getModuleName(),'set');
        $form = $set->getForm('new');
        $form->features_group_id->setValue($this->_request->getParam('group_id'));

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $set->processFormNewSet($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin'),
                        'default', true
                     );
                }
        }
        $this->view->form = $form;

//        $this->view->headScript()->appendFile('/javascripts/jquery.client.admin.js');
        $this->view->headScript()->appendFile('/javascripts/features.js');
    }

    /**
     *  Edit features set
     *
     *  @return void
     */
    public function editSetAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('admin');

        $set = $this->_helper->getServiceLayer($this->_request->getModuleName(),'set');
        $set->findModelById($this->_request->getParam('id'));

        $this->view->setTitle('Редактирование набора характеристик');
        $form = $set->getForm('edit');
        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $set->processFormEditSet($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'list-admin'),
                        'default', true
                     );
                }
        } else {
            foreach ($set->getModel()->Fields as $field) {
               $field->Values;
            }
            $form->setDefaults($set->getModel()->toArray());
        }
        $this->view->form = $form;

//        $this->view->headScript()->appendFile('/javascripts/jquery.client.admin.js');
        $this->view->headScript()->appendFile('/javascripts/features.js');
    }

}