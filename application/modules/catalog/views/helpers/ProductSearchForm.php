<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_ProductSearchForm extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function ProductSearchForm()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'product');
        }
        return $this;
    }

    /**
     * Возвращает форму поиска
     *
     * @return string
     */
    public function getSearchForm($formName = 'searchUser')
    {
        $this->_serviceLayer->getForm($formName)->populate($this->_getRequest()->getParams());
        $this->view->searchForm = $this->_serviceLayer->getForm($formName);
        $this->view->formName = $formName;
        if ($formName == 'searchAdmin') {
            $this->view->searchByCategory = $this->_serviceLayer->getForm('searchByCategory');
            $class = ucfirst($this->getModuleName()) . '_Model_CategoryService';
            $category = new  $class;
            if ($this->_getRequest()->category) {
                $selectedCategory = 0;
                foreach ($this->_getRequest()->category as $rawCategory) {
                    if (!empty ($rawCategory)) {
                        $selectedCategory = $rawCategory;
                    }
                }
                $category->findCategoryById($selectedCategory);
                // получаем массив всей цепочки категорий
                $categories = $category->getSelectedCategoriesArray(false);
                // превращаем массив Javascript массив и вставляем в скрипт представления
                $category->transformArrayToJavascriptArray($categories);
            } else {
                $selectedPath[] = array('id'=> 0,
                                     'level' => 1,
                                     'selected' => '');
                $category->transformArrayToJavascriptArray($selectedPath);
            }
            $category->insertListJsonLinkAsJsScript();
        }

        return $this->view->render($this->getViewScript());
    }


    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}