<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

class Shops_Model_CommentService extends Comments_Model_CommentService
{

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Shops_Model_Comment';
    }


    /**
     * Обработка формы редактирования
     *
     * @return boolean|int
     */
    public function processFormEditAdmin($rawData)
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
}