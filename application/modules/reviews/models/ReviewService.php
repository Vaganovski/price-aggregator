<?php

class Reviews_Model_ReviewService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Reviews_Model_Review';
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
                $this->getModel()->save();
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
               // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении нового товара:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
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
                return $this->getModel()->id;

            } catch (Exception $e) {
                // @todo show forms errors
               // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении нового товара:') . $e->getMessage());
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
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    /**
     * Parse ids of brand to array
     *
     * @param string $rawBrands
     * @return array|NULL
     */
    public function parseBrandIds($rawBrands)
    {
        if ($rawBrands) {
            return explode('-', $rawBrands);
        }
        return NULL;
    }

    /**
     * Get Product by id
     *
     * @param integer $id
     * @return Products_Model_Product
     */
    public function getModelById($id)
    {

        $product = $this->getMapper()->findOneById((int) $id);

        if ($product == false) {
            throw new Exception($this->getView()->translate('Instruction not founds.'));
        }

        return $product;
    }

    /**
     * find Product by id and set model object for service layer
     *
     * @param integer $id
     * @return Products_Model_ProductService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }
}