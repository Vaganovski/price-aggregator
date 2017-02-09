<?php

class Advertisment_IndexController extends Zend_Controller_Action
{
    protected $_serviceLayer;

    public function init()
    {
        $this->_serviceLayer = $this->_helper->getServiceLayer($this->_request->getModuleName(),'banner');
        $this->view->moduleName = $this->_request->getModuleName();
        $this->view->controllerName = $this->_request->getControllerName();
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->setAutoJsonSerialization(false);
        $ajaxContext->addActionContext('delete', 'json')->initContext();
        $ajaxContext->addActionContext('delete-image', 'json')->initContext();
        $ajaxContext->addActionContext('delete-backgound-image', 'json')->initContext();
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('admin');
        $forms = array('top'=> array(), 'right' => array());
        $postId = 0;
        if ($this->_request->isPost()) {
            $postData = $this->_request->getPost();
            $result = $this->_serviceLayer->processFormBanner($postData);
            if ($result) {
                $this->_helper->redirector->gotoRoute(array(
                        'module' => $this->_request->getModuleName(),
                        'controller' => $this->_request->getControllerName(),
                        'action' => 'index',),
                    'default', true
                 );
            } else {
                if (!empty($postData['id'])) {
                    $postId = $postData['id'];
                    $this->_serviceLayer->findModelById($postId);
                }
                $postForm = $this->_serviceLayer->getForm('top');
                $postForm->populate($postData);
                $forms[$postData['type']][] = $postForm;
            }
        }
        $banners = $this->_serviceLayer->getMapper()->findAll();
        foreach ($banners as $banner) {
            if ($banner->id != $postId) {
                $bannerServiceLayer = new Advertisment_Model_BannerService();
                $bannerServiceLayer->findModelById($banner->id);
                $form = $bannerServiceLayer->getForm($banner->type);
                $values = $banner->toArray();
                unset($values['period']);
                $form->populate($values);
                $forms[$banner->type][] = $form;
            }
        }
        $bannerServiceLayer = new Advertisment_Model_BannerService();
        array_unshift($forms['top'], $bannerServiceLayer->getForm('top'));
        array_unshift($forms['right'], $bannerServiceLayer->getForm('right'));
        $this->view->forms = $forms;

        $this->view->headScript()->appendFile('/javascripts/ajaxupload.js');
        $this->view->headScript()->appendFile('/javascripts/jquery.client-upload.js');
    }

    /**
     *  Загрузка картинок
     *
     *  @return void
     */
    public function uploadImageAction()
    {
        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormBannerImage($postData);
                if ($result) {
                    echo $result;
                } else {
                    echo "error";
                }
         }
        exit;
    }

    /**
     *  Загрузка картинок
     *
     *  @return void
     */
    public function uploadBackgoundImageAction()
    {
        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormBannerBackgoundImage($postData);
                if ($result) {
                    echo $result;
                } else {
                    echo "error";
                }
         }
        exit;
    }

    /**
     *  Удаление картинок
     *
     *  @return void
     */
   public function deleteImageAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
                $postData = $this->_request->getPost();
                try {
                    $bannerImage = new Advertisment_Model_BannerImageService();
                    $bannerImage->findModelById($postData['id']);
                    $bannerImage->getModel()->delete();
                    $this->view->result = array(
                        'success' => true,
                    );
                } catch (Exception $e) {
                    $this->view->result = array(
                        'success' => false,
                        'message' => _('Произошла ошибка, обратитесь в администрацию'),
                    );
                }
         }
    }

    /**
     *  Удаление картинок
     *
     *  @return void
     */
   public function deleteBackgoundImageAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
                $postData = $this->_request->getPost();
                try {
                    $banner = new Advertisment_Model_BannerService();
                    $banner->findModelById($postData['id']);
                    $banner->getModel()->background_image = '';
                    $banner->getModel()->save();
                    $this->view->result = array(
                        'success' => true,
                    );
                } catch (Exception $e) {
                    $this->view->result = array(
                        'success' => false,
                        'message' => _('Произошла ошибка, обратитесь в администрацию'),
                    );
                }
         }
    }
    /**
     *  Удаление баннера
     *
     *  @return void
     */
   public function deleteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
                $postData = $this->_request->getPost();
                try {
                    $this->_serviceLayer->findModelById($postData['id']);
                    $this->_serviceLayer->getModel()->delete();
                    $this->view->result = array(
                        'success' => true,
                    );
                } catch (Exception $e) {
                    $this->view->result = array(
                        'success' => false,
                        'message' => _('Произошла ошибка, обратитесь в администрацию'),
                    );
                }
         }
    }

    public function goAction()
    {
        if ($this->_request->id) {
            $this->_serviceLayer->findModelById($this->_request->id);
            if ($this->_serviceLayer->getModel()->url) {
                $this->_helper->redirector->gotoUrl($this->_serviceLayer->getModel()->url);
            } else {
                $this->_helper->redirector->gotoUrl('/');
            }
        } else {
            $this->_forward('not-found', 'error', 'default');
        }
    }
}