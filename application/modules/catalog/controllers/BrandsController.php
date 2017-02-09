<?php
class Catalog_BrandsController extends Zend_Controller_Action
{
    protected $_serviceLayer;

    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer($this->_request->getModuleName(),'brand');
        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->controllerName = $this->_request->getControllerName();
    }

    /**
     *  New Brand
     *
     *  @return void
     */
    public function newAction()
    {
        $this->view->setTitle('Добавить производителя');

        $form = $this->_serviceLayer->getForm('new');

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormNew($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'view',
                            'id' => $this->_serviceLayer->getModel()->id),
                        'default', true
                     );
                }
         }
         $this->view->form = $form;
    }

    /**
     *  Edit Brand
     *
     *  @return void
     */
    public function editAction()
    {
        $this->view->setTitle('Редактировать производителя');

        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        $form = $this->_serviceLayer->getForm('edit');

        if ($this->_request->isPost()) {

                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormEdit($postData);
                if ($result == true) {
                    $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'view',
                            'id' => $this->_serviceLayer->getModel()->id),
                        'default', true
                     );
                }
         } else {
             $this->_serviceLayer->getForm('edit')->populate($this->_serviceLayer->getModel()->toArray());
         }
         $this->view->form = $form;
    }

    /**
     *  View Brand
     *
     *  @return void
     */
//    public function viewAction()
//    {
//        $this->_serviceLayer->findModelById($this->_request->getParam('id'));
//
//        $this->view->setTitle($this->_serviceLayer->getModel()->name);
//        $this->view->product = $this->_serviceLayer->getModel();
//    }

    /**
     *  Delete Brand
     *
     *  @return void
     */
    public function deleteAction()
    {
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));

        if ($this->_helper->isAllowed($this->_serviceLayer->getModel(), 'delete')) {
            $this->view->setTitle('Удалить производителя "%s"?', array($this->_serviceLayer->getModel()->name));

            if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $formResult = $this->_serviceLayer->processFormDelete($postData);
                $this->_helper->redirector->gotoRoute(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'new'),
                    'default');
            }
            $this->view->form = $this->_serviceLayer->getForm('delete');
        } else {
            $this->_forward('denied', 'error', 'default');
        }
    }

    /**
     *  Alphabet brands list
     *
     *  @return void
     */
    public function indexAction()
    {
        $this->view->setTitle('Список производителей');

        $brands = $this->_serviceLayer->getMapper()->findAllOrderByName($this->_request->city);
        $brandsByLetter = null;
        foreach ($brands as $shop) {
            $letter = mb_substr($shop->name, 0, 1, 'UTF-8');
            if (is_numeric($letter)) {
                $key = '0-9';
            } else {
                $key = mb_strtoupper($letter, 'UTF-8');
            }
            $brandsByLetter[strtoupper($key)][] = $shop;
        }
        $this->view->brandsByLetter = $brandsByLetter;
    }

    /**
     *  Просмотр бренда
     *
     *  @return void
     */
    public function viewAction() {
        $categories = $this->_serviceLayer->getMapper()->findCategoriesOfThisBrand($this->_request->id);
        $brand = $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $this->view->setTitle($brand->getModel()->name);
        $categoriesArray = array();
        foreach ($categories as $category) {
            $parent = $category->getNode()->getParent();
            $categoriesArray[$parent->title]['this'] = $parent;
            $categoriesArray[$parent->title]['children'][] = $category;
        }
        $this->view->categories = $categoriesArray;
        $this->view->brand = $brand->getModel();
        $this->view->BreadCrumbs()->appendBreadCrumb('Производители', $this->view->url(array(
                                 'module'=>'catalog',
                                 'controller'=>'brands',
                                 'action'=>'index'
                             ), 'default', true));
    }
}