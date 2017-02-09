<?php

/**
 * Хелпер
 */
class Categories_View_Helper_CategoriesList extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function CategoriesList()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('categories', 'category', 'index');
        }
        return $this;
    }

    /**
     * Блок списока комитариев
     *
     * @param integer $entityId
     * @param Users_Model_UserBase $user
     *
     * @return string
     */
    public function getCategoriesList()
    {
        $this->view->moduleName = $this->getModuleName();
        $this->view->controllerName = $this->getContorllerName();

        $this->view->categories = $this->_serviceLayer->getCategoryCildren(0, NULL);

        /**
         * @todo этот путь дублируется в jquery.client.js
         */
//        $listUrl = $this->view->url(array('module'=>$this->getModuleName(),
//                                             'controller'=>$this->getContorllerName(),
//                                             'action'=>'list'), 'default');
//
//        $script = 'var categoriesUrlList = "' . $listUrl . '";';
//
//        $this->view->headScript()->appendScript($script);
//        // Добавляем пути к скриптам представления

        return $this->view->render($this->getViewScript());
    }


    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}