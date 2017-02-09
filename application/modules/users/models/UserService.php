<?php

class Users_Model_UserService extends Users_Model_UserExtendedService
{

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setFormModelNamespace('edit',__CLASS__);
        $this->setFormModelNamespace('registration',__CLASS__);
        $this->setFormModelNamespace('login',__CLASS__);
        $this->setFormModelNamespace('merchantLogin',__CLASS__);
        $this->setFormModelNamespace('passwordRestore',__CLASS__);
        $this->setFormModelNamespace('passwordChangeAdmin',__CLASS__);
        $this->setFormModelNamespace('createUser',__CLASS__);
    }

    public function processFormLogin($postData) {
        $oldSession = $_SESSION;
        if (($result = parent::processFormLogin($postData)) == true) {
            // сливаем сессии авторизированого пользователя
            $_SESSION = array_merge($_SESSION, $oldSession);
            // записываем товары из моего списка не авторизированого пользователя в БД
            $namespace = new Zend_Session_Namespace('product-to-mylist');
            $product = new Catalog_Model_ProductService();
            if ($product->processAddToMyList($namespace->product_ids, Zend_Auth::getInstance()->getIdentity()->id)) {
                unset($namespace->product_ids);
            }
        }
        return $result;
    }

    /**
     * Обработка формы востановления пароля
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormPasswordChangeAdmin($postData)
    {
        $form = $this->getForm('passwordChangeAdmin');

        if ($form->isValid($postData)) {
            $this->getModel()->setPassword($form->getValue('password'));
            $this->getModel()->save();

            return true;
        } else {
            return false;
        }
    }


    /**
     * Обработка формы создания пользователя
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormCreate($postData)
    {
        $form = $this->getForm('createUser');

        if ($form->isValid($postData)) {
            $this->getModel()->fromArray($form->getValues());
            $this->getModel()->registration_ip = $_SERVER['REMOTE_ADDR'];
            $this->getModel()->activated = true;
            $this->getModel()->save();

            $this->getView()->user = $this->getModel();
            $this->getView()->passwird = $form->getValue('password');
            $body = $this->getView()->render('/mails/create-user.phtml');

            try {
                Users_Model_MailerService::sendmail(
                    $this->getModel()->email,
                    $this->getView()->translate('Новая учетная запись'),
                    $body
                );
                return true;
            } catch (Zend_Exception $e) {
                throw new Exception(
                    $this->getView()->translate('Произошла ошибка при отправке на почту данных для входа!')
                );
            }
        } else {
            return false;
        }
    }

}