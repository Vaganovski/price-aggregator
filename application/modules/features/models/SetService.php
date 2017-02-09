<?php

class Features_Model_SetService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
       $this->setFormsModelNamespace('Features_Form_Feature_Set');
        // @todo refact
        $this->_modelName = 'Features_Model_Set';
    }

     /**
     * Обработка формы добавление набора характеристик в группу
     *
     * @param array $rawData
     * 
     * @return boolean
     */
    public function processFormNewSet($rawData)
    {
        $form = $this->getForm('new');
        // заполняем данными форму
        $form->populate($rawData);
        
        if ($form->isValid($rawData)) {
            $field = new Features_Model_FieldService();
            $formValues = $form->getValues();
            try {
                $fields = $formValues['fields'];
                unset($formValues['fields']);
                // записываем данные о наборе
                $this->getModel()->fromArray($formValues);
                $this->getModel()->save();

                // сохраняем данные о характеристиках набора
                $field->save($fields, $this->getModel()->id);
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
                // echo $e->getMessage();
                $form->addError($this->getView()->translate('Произошла ошибка при добавлении:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

     /**
     * Обработка формы редактирования набора характеристик в группу
     *
     * @param array $rawData
     *
     * @return boolean
     */
    public function processFormEditSet($rawData)
    {
        $form = $this->getForm('edit');
        // заполняем данными форму
        $form->populate($rawData);
        
        if ($form->isValid($rawData)) {
            $field = new Features_Model_FieldService();
            $formValues = $form->getValues();
            try {
                $fields = $formValues['fields'];
                unset($formValues['fields']);
                // записываем данные о наборе
                $this->getModel()->title = $formValues['title'];
                $this->getModel()->save();

                // сохраняем данные о характеристиках набора
                $field->save($fields, $this->getModel()->id);
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
                // echo $e->getMessage();
                $form->addError($this->getView()->translate('Произошла ошибка при добавлении:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Сохранение наборов характеристик
     *
     * @param array $arraySets
     * @param integer $groupId
     *
     * @return boolean
     */
    public function save($arraySets, $groupId)
    {
        $existsSets = array();
        foreach ($arraySets as $set) {
            // если идентификатор есть тогда редактируем набор, иначе создаем новый
            if ($set['id']) {
                $this->setModel($this->getMapper()->findOneById($set['id']));
                $this->getModel()->title = $set['title'];
                $this->getModel()->save();
                $existsSets[] = $set['id'];
            } else {
                $this->getModel(true)->title = $set['title'];
                $this->getModel()->link('Group', array($groupId));
                $this->getModel()->save();
                $existsSets[] = $this->getModel()->id;

            }

        }
        // удаление не нужных наборов
        $this->getMapper()->deleteSets($existsSets, $groupId);

    }
    /**
     * Get Set by id
     *
     * @param integer $id
     * @return Features_Model_Set
     */
    public function getModelById($id) {

        $set = $this->getMapper()->findOneById((int) $id);

        if ($set == false) {
            throw new Exception($this->getView()->translate('Набор характеристик не существует.'));
        }

        return $set;
    }

    /**
     * find Set by id and set model object for service layer
     *
     * @param integer $id
     * @return Features_Model_SetService
     */
    public function findModelById($id) {
        $this->setModel($this->getModelById($id));
        return $this;
    }

}