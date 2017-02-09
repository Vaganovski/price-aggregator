<?php

class Features_Model_GroupService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
        $this->setFormsModelNamespace('Features_Form_Feature_Group');
        // @todo refact
        $this->_modelName = 'Features_Model_Group';
    }

    
    /**
     * Обработка формы добавление группы характеристик
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormNewGroup($rawData)
    {
        $form = $this->getForm('new');
        // заполняем данными форму
        $form->populate($rawData);
        
        if ($form->isValid($rawData)) {
            $set = new Features_Model_SetService();
            $formValues = $form->getValues();
            try {
                $sets = $formValues['sets'];
                unset($formValues['sets']);
                // записываем данные о группе
                $this->getModel()->fromArray($formValues);
                $this->getModel()->save();

                // сохраняем данные о наборах группы
                $set->save($sets, $this->getModel()->id);
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
                //echo $e->getMessage();
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
     * Обработка формы редактирования группы характеристик
     *
     * @param array $rawData
     *
     * @return boolean
     */
    public function processFormEditGroup($rawData)
    {
        $form = $this->getForm('Edit');
        // заполняем данными форму
        $form->populate($rawData);

        if ($form->isValid($rawData)) {
            $set = new Features_Model_SetService();
            $formValues = $form->getValues();
            try {
                // сохраняем данные о наборах группы
                $set->save($formValues['sets'], $formValues['id']);
                unset($formValues['sets']);
                
                // записываем данные о группе
                $this->getModel()->fromArray($formValues);
                $this->getModel()->save();
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
                // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении страницы:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

     /**
     * Обработка формы удаление группы характеристик
     *
     * @param array $rawData
     *
     * @return boolean
     */
    public function processFormDeleteGroup($rawData)
    {
        if (array_key_exists('submit_ok',$rawData)) {
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    /**
     * Get Group by id
     *
     * @param integer $id
     * @return Features_Model_Group
     */
    public function getModelById($id) {

        $group = $this->getMapper()->findOneById((int) $id);

        if ($group == false) {
            throw new Exception($this->getView()->translate('Шаблоны характеристик не существуют.'));
        }

        return $group;
    }

    /**
     * find Group by id and set model object for service layer
     *
     * @param integer $id
     * @return Features_Model_GroupService
     */
    public function findModelById($id) {
        $this->setModel($this->getModelById($id));
        return $this;
    }


}