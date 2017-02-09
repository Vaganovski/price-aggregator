<?php

abstract class Categories_Controller_Abstract_Index extends Zend_Controller_Action
{
    protected $category;

    public function init()
    {
        //$this->_helper->ViewScripts->setModuleName('categories');
        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->setAutoJsonSerialization(false);
        $ajaxContext->addActionContext('listJson', 'json')
                        ->initContext();

        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->controllerName = $this->_request->getControllerName();
        
    }

    /**
     * Create new category
     *
     * @return void
     */
    public function newAction()
    {
        $this->view->setTitle('Добавление категории');

        if (isset($this->_request->id_to)) {
            $category_to = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
            $category_to->findCategoryById($this->_request->id_to);
        }

        if (!$this->_request->action_redirect) {
            $this->_request->setParam('action_redirect', 'list');
        }
        if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();
                $formResult = $this->category->processFormNew($formData);
                if ($formResult == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => $this->_request->action_redirect),
                        'default',
                        true
                     );
                } else {

                }
         }

         if (isset($category_to)) {
             $selectedPath = $category_to->getSelectedCategoriesArray(false, true);
         } else {
             // формирование массива для загрузки каскадных селектов
             $selectedPath[] = array('id'=> 0,
                                     'level' => 1,
                                     'selected' => 0,
                                     'selectedToDelete' => 0);
         }

         // превращаем массив в json вид
         $jsArray = json_encode($selectedPath);
         $script = 'var dataArray = ' . $jsArray . ';';
         $this->view->headScript()->appendScript($script);

         $this->view->form = $this->category->getForm('new');

         $this->_appendJsVarUrlListJson();

    }

    /**
     * Edit category
     *
     * @return void
     */
    public function editAction()
    {
        $this->view->setTitle('Редактирование категории');
                
        $this->category->findCategoryById($this->_request->id);
        
        $form = $this->category->getForm('edit');

        if (!$this->_request->action_redirect) {
            $this->_request->setParam('action_redirect', 'list');
        }
        if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();
                $formResult = $this->category->processFormEdit($formData);
                if ($formResult == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => $this->_request->action_redirect),
                        'default',
                        true
                     );
                } else {

                }
         } else {
             $form->populate($this->category->getModel()->toArray());
         }

         // превращаем массив в json вид
         $jsArray = json_encode($this->category->getSelectedCategoriesArray());
         $script = 'var dataArray = ' . $jsArray . ';';
         $this->view->headScript()->appendScript($script);

         $this->view->form = $form;
         
         $this->_appendJsVarUrlListJson();

    }

    /**
     * List category
     *
     * @return void
     */
    public function listJsonAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');
            // отправляем массив с категориями
            try {
                $categories = $this->category->getCategoryCildren($this->_request->id, $this->_request->alias);
            } catch (Exception $e) {
                $categories = array();
            }

            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
            $this->_helper->json($this->category->getCategoriesArray($categories,
                    (int)$this->_request->level,
                    (int)$this->_request->selected));
        }

    }
    
    /**
     * List category
     *
     * @return void
     */
    public function listAction()
    {
        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
        $this->category->findCategoryByIdOrAlias(0, $this->_request->alias);
        if ($this->category->getModel()->getNode()->isRoot() || !$this->category->getModel()->id) {
           $this->view->setTitle($this->view->translate('Категории'));
        } else {
           $this->view->setTitle($this->category->getModel()->title);
        }
        $this->view->category = $this->category->getModel();
        $childCategories = $this->category->getCategoryCildren(0, $this->_request->alias);
        if ($childCategories) {
            $this->view->categories = $childCategories;
        }
        if ($this->_request->isXmlHttpRequest()) {
            Zend_Layout::getMvcInstance()->disableLayout();
        }
    }

        /**
     * View category
     *
     * @return void
     */
    public function viewAction()
    {

    }

    public function treeAction()
    {
        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');

        if ($this->_request->isXmlHttpRequest()) {
            // выбираем наследников категории

            try {
                $categories = $this->category->getCategoryCildren($this->_request->id, $this->_request->alias);
            } catch (Exception $e) {
                $categories = array();
            }
            // отдаем список для дерева списка категорий
            $this->view->categories = $categories;
            $this->view->CategoriesTree()
                        ->setServiceLayer($this->_request->getModuleName(), 'category')
                        ->setControllerName($this->_request->getControllerName())
                        ->setViewScript('categories');
            $text = $this->view->render($this->view->CategoriesTree()->getViewScript('item'));
            echo $text;
            exit;
        }
    }

    public function moveAction()
    {
        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->category->processFormMove((int)$this->_request->source,
                    (int)$this->_request->destination,
                    (int)$this->_request->position)) {
                $this->_helper->json(array('error' => 'Перемещено'));
                return;
            } else {
                $this->_helper->json(array('error' => 'Произошла ошибка'));
                return;
            }
        }
    }

     /**
     * Delete categories
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->view->setTitle('Удаление категории');

        if (!$this->_request->action_redirect) {
            $this->_request->setParam('action_redirect', 'list');
        }

        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
        $this->category->findCategoryByIdOrAlias($this->_request->id, $this->_request->alias);

        $title = sprintf($this->view->translate('Do you want to delete category "%s"? All nested categories will be deleted!'),
                $this->category->getModel()->title);
        $this->view->title = $title;
        $this->view->form = $this->category->getForm('delete');

        if ($this->_request->isPost()) {

            $postData = $this->_request->getPost();
            $this->category->processFormDelete($postData);

            $this->_helper->redirector->gotoRoute(array(
                    'module' => $this->_request->getModuleName(),
                    'controller' => $this->_request->getControllerName(),
                    'action' => $this->_request->action_redirect),
                'default',
                true
             );

        }
    }

    protected function _appendJsVarUrlListJson()
    {
         $urlListJson = $this->view->url(array(
                    'module' => $this->_request->getModuleName(),
                    'controller' => $this->_request->getControllerName(),
                    'action'=>'list-json'),
                 'default',
                 true);
         $script = 'var categoriesUrlListJson = "' . $urlListJson . '";';
         $this->view->headScript()->appendScript($script);
    }

}