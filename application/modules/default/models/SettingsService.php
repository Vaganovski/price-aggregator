<?php

class Default_Model_SettingsService extends ZFEngine_Model_Service_Database_Abstract
{
    public function init()
    {
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Default_Model_Settings';
    }

    /**
     * Processing edit page form
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormEdit($postData)
    {
        $form = $this->getForm('edit');
        if ($form->isValid($postData)) {
            try {
                $formValues = $form->getValues();
                $this->_setFormDataToModel($formValues);
                return true;
            } catch (Exception $e) {
                $form->addError($this->getView()->translate('An error occurred when changing settings:') . $e->getMessage());
                $form->populate($postData);
                return false;
            }
        } else {
            $form->populate($postData);
            return false;
        }
    }


    /**
     *  put data from post form into model
     *  @param  array $formValues
     *  @return void
     *
     */
    protected function _setFormDataToModel($formValues)
    {
        foreach ($formValues as $key => $value) {
            $option = $this->getMapper()->find($key);
            $option->value = $value;
            $option->save();
        }
    }
}