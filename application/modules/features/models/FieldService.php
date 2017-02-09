<?php

class Features_Model_FieldService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
       //$this->setFormsModelNamespace('Features_Form_Feature_Set');
        // @todo refact
        $this->_modelName = 'Features_Model_Field';
    }

    /**
     * Сохранение характеристик набора
     *
     * @param array $arrayFields
     * @param integer $setId
     *
     * @return boolean
     */
    public function save($arrayFields, $setId)
    {
        $existsFields = array();
        foreach ($arrayFields as $field) {
            $value = new Features_Model_ValueService();
            // если идентификатор есть тогда редактируем характеристику, иначе создаем новую
            if ($field['id']) {
                $values = $field['values'];
                unset ($field['values']);
                $this->setModel($this->getMapper()->findOneById($field['id']));
                unset ($field['id']);
                $this->getModel()->fromArray($field);
                $this->getModel()->save();
                $existsFields[] = $this->getModel()->id;
                $value->save($values, $this->getModel()->id);
            } else {
                $values = $field['values'];
                unset ($field['values']);
                unset ($field['id']);
                $this->getModel(true)->fromArray($field);
                $this->getModel()->link('Set', array($setId));
                $this->getModel()->save();
                $existsFields[] = $this->getModel()->id;
                $value->save($values, $this->getModel()->id);
            }

        }
        // удаление не нужных характеристик
        $this->getMapper()->deleteFields($existsFields, $setId);

    }

}