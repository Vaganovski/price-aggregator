<?php

class Shops_Model_ManagerService extends Users_Model_UserService
{

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setFormModelNamespace('new',__CLASS__);
        $this->setFormModelNamespace('edit',__CLASS__);
    }

    /**
     * Обработка формы добавления
     *
     * @return boolean
     */
    public function processFormNew($rawData)
    {
        $form = $this->getForm('new');
        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $this->getModel()->fromArray($formValues);
                $this->getModel()->role = Users_Model_User::ROLE_MANAGER;
                $this->getModel()->save();
                $this->getModel()->link('Shop', array($formValues['shop_id']), true);

                if (strlen($this->getModel()->email)) {
                    $this->getView()->user = $this->getModel();
                    $this->getView()->password = $formValues['password'];
                    $body = $this->getView()->render('/mails/manager.phtml');
                    Users_Model_MailerService::sendmail(
                        $this->getModel()->email,
                        $this->getView()->translate('Данные для доступа к обновлению магазина'),
                        $body
                    );
                }
                return true;
            } catch (Exception $e) {
                Zend_Debug::dump($e);
                exit;
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении нового менеджера:') . $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Обработка формы редактирования
     *
     * @return boolean|int
     */
    public function processFormEdit($rawData)
    {
        $form = $this->getForm('edit');
        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $this->getModel()->fromArray($formValues);
                $this->getModel()->save();
                return true;

            } catch (Exception $e) {
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при редактировании менеджера:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }
}