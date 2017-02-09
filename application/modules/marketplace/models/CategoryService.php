<?php

class Marketplace_Model_CategoryService extends Categories_Model_CategoryService
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Marketplace_Model_Category';
    }

    /**
     * Вставляет в скрипт представления ссылку аякс запроса для выбора категорий
     *
     * @return Catalog_Model_CategoryService
     */
    public function insertListJsonLinkAsJsScript() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $view = Zend_Layout::getMvcInstance()->getView();
        $categoriesListUrl = $view->url(array('module'=>$request->getModuleName(),
                                             'controller'=>'categories',
                                             'action'=>'list-json'), 'default', true);

        $script = 'var categoriesUrlListJson = "' . $categoriesListUrl . '";';


        $view->headScript()->appendScript($script);

        return $this;
    }

    /**
     * get category id from raw array
     *
     * @param array $categoryArray
     * @return integer
     */
    public function getCategoryIdFromRawArray($categoryArray)
    {
        $categoryId = 0;
        foreach ($categoryArray as $category) {
            if ($category != '') {
                $categoryId = (int)$category;
            }
        }
        return $categoryId;
    }

}