<?php

class Feedback_IndexController extends Zend_Controller_Action
{

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-find-error-form', 'json')
                    ->initContext();
        parent::init();
    }

    /**
     * Экшен для формы обратной связи
     *
     * @return void
     */
    public function newAction()
    {
        $this->view->setTitle(_('Форма обратной связи'));
        $feedback = new Feedback_Model_FeedbackService();
        $form = $feedback->getForm('new');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getParams();
            if ($feedback->proccessFormNew($formData)) {
                if ($this->_request->isXmlHttpRequest()) {
                    $content = array(
                        'success'=> true,
                        'message' => $this->view->translate('Ваше сообщение успешно отправлено!')
                    );
                    $this->_helper->json($content);
                    return;
                } else {
                    $this->_helper->FlashMessenger($this->view->translate('Ваше сообщение успешно отправлено!'));
                    $form->reset();
                    $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
                }
            } else {
                if ($this->_request->isXmlHttpRequest()) {
                    $form->populate($formData);
                    $content = array(
                        'success'=> false,
                        'form' => $form->render()
                    );
                    $this->_helper->json($content);
                    return;
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Экшен для формы Нашли ошибку в описании
     *
     * @return void
     */
    public function findErrorAction() {
        $this->view->setTitle(_('Нашли ошибку в описании?'));
        $feedback = new Feedback_Model_FeedbackService();
        $form = $feedback->getForm('findError');
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getParams();
            if ($feedback->proccessFormFindError($formData)) {
                if ($this->_request->isXmlHttpRequest()) {
                    $content = array(
                        'success'=> true,
                        'message' => $this->view->translate('Ваше сообщение успешно отправлено!')
                    );
                    $this->_helper->json($content);
                    return;
                } else {
                    $this->_helper->FlashMessenger($this->view->translate('Ваше сообщение успешно отправлено!'));
                    $form->reset();
                    $this->_helper->redirector->gotoUrl($_SERVER['HTTP_REFERER']);
                }
            } else {
                if ($this->_request->isXmlHttpRequest()) {
                    $form->populate($formData);
                    $content = array(
                        'success'=> false,
                        'form' => $form->render()
                    );
                    $this->_helper->json($content);
                    return;
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Экшен для получения формы по яксу
     *
     * @return void
     */
    public function getFindErrorFormAction() {
        $form = new Feedback_Form_Feedback_FindError();
        $form->getElement('product_id')->setValue($this->_request->product_id);
        $this->view->form = $form->render();
    }
}