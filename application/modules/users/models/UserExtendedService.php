<?php

class Users_Model_UserExtendedService extends Users_Model_UserBaseService
{

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setFormModelNamespace('registration',__CLASS__);
    }


    /**
     * Обработка формы регистрации
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormRegistration($postData)
    {
        $form = $this->getForm('registration');

        if ($form->isValid($postData)) {
            $this->getModel()->fromArray($form->getValues());
            if (!in_array($form->getValue('role'), array(Users_Model_User::ROLE_MEMBER, Users_Model_User::ROLE_MERCHANT))) {
                $this->getModel()->role = Users_Model_User::ROLE_MEMBER;
            }
            $this->getModel()->registration_ip = $_SERVER['REMOTE_ADDR'];
            $this->getModel()->activation_code = substr(md5(mktime() + rand(0, 100)), -8);

            $this->getModel()->save();
            $this->auth();

            $this->getView()->user = $this->getModel();
            $body = $this->getView()->render('/mails/activation.phtml');

            try {
                Users_Model_MailerService::sendmail(
                    $this->getModel()->email,
                    $this->getView()->translate('Активация'),
                    $body
                );
                return true;
            } catch (Zend_Exception $e) {
                throw new Exception(
                    $this->getView()->translate('Произошла ошибка при отправке Вам на почту ссылки для активации аккаунта!') . '<br/>' .
                    $this->getView()->translate('Свяжитесь пожалуйста с администратором сайта.')
                );
            }
        } else {
            return false;
        }
    }
}