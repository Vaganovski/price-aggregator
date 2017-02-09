<?php

class Feedback_Model_FeedbackService extends ZFEngine_Model_Service_Abstract
{
    protected $_currentForm;

    public function init()
    {
        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
    }

    /**
     * Обработка формы фидбека
     *
     * @param array $rawData
     * 
     * @return boolean
     */
    public function proccessFormNew($rawData)
    {
        $form = $this->getForm('new');
        if ($form->isValid($rawData)) {
            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->config;
            $emailRecipient = $config->feedback->emailRecipient;
            $userAuth = Zend_Auth::getInstance();
            $this->getView()->message = $form->getValue('message');
            $message = $this->getView()->render('/feedback/views/scripts/index/mails/message.phtml');
            if ($userAuth->hasIdentity()) {
                return $this->send($message,
                    sprintf($this->view->translate('Сообщение от пользователя %s с eprice.kz'), $userAuth->getIdentity()->name),
                    $emailRecipient,
                    $userAuth->getIdentity()->email,
                    $userAuth->getIdentity()->name
                );
            } else {
                return $this->send($message,
                    $this->view->translate('Сообщение c eprice.kz'),
                    $emailRecipient,
                    $form->getValue('email')
                );
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Обработка формы "Нашли ошибку в описании"
     *
     * @param array $rawData
     *
     * @return boolean
     */
    public function proccessFormFindError($rawData)
    {
        $form = $this->getForm('findError');
        if ($form->isValid($rawData)) {
            $frontController = Zend_Controller_Front::getInstance();
            $config = $frontController->getParam('bootstrap')->config;
            $emailRecipient = $config->feedback->emailRecipient;
            $userAuth = Zend_Auth::getInstance();

            $product = new Catalog_Model_ProductService();
            $product->findModelById($form->getValue('product_id'));

            $subject = sprintf('Ошибка в описании товара "%s"', $product->getModel()->name);
            $this->getView()->product = $product->getModel();
            $this->getView()->baseUrl = $frontController->getRequest()->getBaseUrl();
            $this->getView()->userLogin = $userAuth->getIdentity()->login;
            $this->getView()->message = $form->getValue('message');
            $message = $this->getView()->render('/feedback/views/scripts/index/mails/find-error.phtml');

            if ($userAuth->hasIdentity()) {
                return $this->send($message,
                    $subject,
                    $emailRecipient,
                    $userAuth->getIdentity()->email,
                    $userAuth->getIdentity()->name
                );
            } else {
                return $this->send($message,
                    $subject,
                    $emailRecipient,
                    $form->getValue('email')
                );
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Отправляет письмо
     *
     * @param string $message
     * @param string $subject
     * @param string $emailRecipient
     * @param string $emailFrom
     * @param string $emailFromName
     *
     * @return boolean
     */
    public function send($message, $subject, $emailRecipient, $emailFrom, $emailFromName = NULL)
    {
        try {
            $mail = new Zend_Mail('UTF-8');
            $mail->addTo($emailRecipient);
            $mail->setSubject($subject);
            $mail->setFrom($emailFrom, $emailFromName);
            $mail->setBodyHtml($message, 'UTF-8', Zend_Mime::ENCODING_BASE64);
            $mail->send();
            return true;
        } catch (Zend_Exception $e) {
            return false;
        }

    }



}