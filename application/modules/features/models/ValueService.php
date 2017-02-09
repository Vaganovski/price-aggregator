<?php

class Features_Model_ValueService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
       //$this->setFormsModelNamespace('Features_Form_Feature_Set');
        // @todo refact
        $this->_modelName = 'Features_Model_Value';
    }

    /**
     * Сохранение значений характеристик
     *
     * @param array $arrayValues
     * @param integer $fieldId
     *
     * @return boolean
     */
    public function save($arrayValues, $fieldId)
    {
        $existsValues = array();
        foreach ($arrayValues as $value) {
            // если идентификатор есть тогда редактируем значение, иначе создаем новое
            if ($value['id']) {
                $this->setModel($this->getMapper()->findOneById($value['id']));
                unset ($value['id']);
                $this->getModel()->fromArray($value);
                $this->getModel()->save();
                $existsValues[] = $this->getModel()->id;
            } else {
                $this->getModel(true)->fromArray($value);
                $this->getModel()->link('Field', array($fieldId));
                $this->getModel()->save();
                $existsValues[] = $this->getModel()->id;
            }

        }
        // удаление не нужных значений
        $this->getMapper()->deleteValues($existsValues, $fieldId);
    }

}