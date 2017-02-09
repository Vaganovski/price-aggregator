<?php

/**
 * Хелпер
 */
class Shops_View_Helper_ShopsSearchForm extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function shopsSearchForm()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('shops', 'shop');
        }
        return $this;
    }

    /**
     * Возвращает форму поиска
     *
     * @return string
     */
    public function getSearchForm($formName = 'searchAdmin')
    {
        $this->_serviceLayer->getForm($formName)->populate($this->_getRequest()->getParams());
        $this->view->searchForm = $this->_serviceLayer->getForm($formName);

        return $this->view->render($this->getViewScript());
    }

}