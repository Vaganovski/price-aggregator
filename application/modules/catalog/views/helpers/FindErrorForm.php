<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_FindErrorForm extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function FindErrorForm($productId = NULL)
    {
        $form = new Feedback_Form_Feedback_FindError();
        if ($productId) {
            $form->getElement('product_id')->setValue($productId);
        }
        $this->view->form = $form;
        return $this->view->render($this->getViewScript());
    }

    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}