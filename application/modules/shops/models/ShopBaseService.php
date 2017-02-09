<?php

class Shops_Model_ShopBaseService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Shops_Model_Shop';
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
            if (!strlen($formValues['chain_shop_id'])) {
                unset($formValues['chain_shop_id']);
            }
            try {
                $this->getModel()->fromArray($formValues);
                $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->config;
                $newShopPeriod = $config->shops->newShopPeriod;
                $this->getModel()->period = $newShopPeriod;
                $this->getModel()->last_renewal_date = Zend_Date::now()->toString('y-MM-dd WW');
                $untillDate = new Zend_Date($this->getModel()->untill_date);
                $this->getModel()->untill_date = $untillDate->addDay($newShopPeriod)->toString('y-MM-dd WW');
                $this->getModel()->save();
                $this->_sendNotifications();
                return true;
            } catch (Exception $e) {
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении нового магазина:') . $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Отправка уведомлений администрации при регистрации нового магазина
     */
    protected function _sendNotifications()
    {
        $this->getView()->shop = $this->getModel();
        $users = Users_Model_UserTable::getInstance()->findAllAsQuery()->addWhere('role = ?', Users_Model_User::ROLE_ADMINISTRATOR)->fetchArray();
        foreach ($users as $user) {
            Users_Model_MailerService::sendmail(
                    $user['email'],
                    $this->getView()->translate('Регистрация нового магазина на eprice.kz'),
                    $this->getView()->render('/mails/notifications.phtml')
            );
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
                if (!strlen($formValues['chain_shop_id'])) {
                    $this->getModel()->chain_shop_id = null;
                }
                $this->getModel()->save();
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
               // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при редактировании нового категории:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Delete
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormDelete($rawData)
    {
        if (array_key_exists('submit_ok', $rawData)) {
            $this->getModel()->unlinkImages();

            $message_id = $this->getModel()->message_id;

            // delete message by message_id
            if($message_id){
                $conn = Doctrine_Manager::connection();
                $statementDeleteMessage = 'DELETE FROM message WHERE message_id = ?';
                $conn->prepare($statementDeleteMessage)->execute(array($message_id));
            }
            
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    /**
     * Get Shop by id
     *
     * @param integer $id
     * @return Shops_Model_Shop
     */
    public function getModelById($id)
    {

        $shop = $this->getMapper()->findOneById((int) $id);

        if ($shop == false) {
            throw new Exception($this->getView()->translate('Shop not founds.'));
        }

        return $shop;
    }

    /**
     * find Shop by id and set model object for service layer
     *
     * @param integer $id
     * @return Shops_Model_ShopService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }
}