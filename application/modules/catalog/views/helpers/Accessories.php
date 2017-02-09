<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_Accessories extends ZFEngine_View_Helper_Abstract
{

    /**
     * Выполняет роль конструктора
     *
     */
    public function Accessories()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'categoryAccessories');
        }

        return $this;
    }

    /**
     * Выбирает последнии отзывы от товара
     *
     * @return string
     */
    public function getAccessories($categoryId)
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'categoryAccessories');
        }
//        Zend_Debug::dump( $this->_serviceLayer->getMapper()->findAllByCategoryId($categoryId)->getFirst()->CategoryAccessory->toArray());
//        exit;
        $this->view->accessories = $this->_serviceLayer->getMapper()->findAllByCategoryId($categoryId);
        return $this->view->render($this->getViewScript());
    }



    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}