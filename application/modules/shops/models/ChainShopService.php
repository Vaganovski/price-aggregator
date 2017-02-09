<?php

class Shops_Model_ChainShopService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        $this->_modelName = 'Shops_Model_ChainShop';
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
            $this->getModel()->fromArray($formValues);
            $this->getModel()->user_id = Users_Model_UserService::getUserAuthIdentity()->id;
            $this->getModel()->save();
            return true;
        } else {
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
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    /**
     * Get Shop by id
     *
     * @param integer $id
     * @return Shops_Model_ChainShop
     */
    public function getModelById($id)
    {
        $shop = $this->getMapper()->findOneById((int) $id);
        if ($shop == false) {
                throw new Exception($this->getView()->translate('Магазины не найдены.'));
        }

        return $shop;
    }

    /**
     * find Shop by id and set model object for service layer
     *
     * @param integer $id
     * @return Shops_Model_ChainService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }
}