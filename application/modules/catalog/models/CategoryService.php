<?php

class Catalog_Model_CategoryService extends Categories_Model_CategoryService
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Catalog_Model_Category';
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
     * Обработка формы добавления
     *
     * @return boolean|int
     */
    public function processFormNew($rawData)
    {
        if (array_key_exists('features_group_id', $rawData) && empty ($rawData['features_group_id'])) {
            unset ($rawData['features_group_id']);
        }
        if (array_key_exists('accessory', $rawData)) {
            $accessories = $rawData['accessory'];
            unset($rawData['accessory']);
        }
        if (parent::processFormNew($rawData)) {
             if (array_key_exists('features_group_id', $rawData)) {
                $this->getModel()->link('Group', $rawData['features_group_id'], true);
            }

            if ($accessories) {
                $selectedAccessory = 0; $selectedAccessories = array();
                foreach ($accessories as $key => $accessoryValues) {
                    foreach ($accessoryValues as $value) {
                        if ($value != '') {
                            $selectedAccessory = (int)$value;
                        }
                    }
                    if ($selectedAccessory != 0) {
                        $selectedAccessories[] = $selectedAccessory;
                    }
                }
                
                $this->getModel()->link('CategoriesAccessories', $selectedAccessories, true);
            }
            return true;
        }
    }

    /**
     * Обработка формы редактирования
     *
     * @return boolean|int
     */
    public function processFormEdit($rawData)
    {
        if (array_key_exists('features_group_id', $rawData) && empty ($rawData['features_group_id'])) {
            unset ($rawData['features_group_id']);
        }
        if (array_key_exists('accessory', $rawData)) {
            $accessories = $rawData['accessory'];
            unset($rawData['accessory']);
        }
        if (parent::processFormEdit($rawData)) {
             if (array_key_exists('features_group_id', $rawData)) {
                if ($this->getModel()->features_group_id != $rawData['features_group_id']) {
                    $this->getModel()->link('Group', $rawData['features_group_id'], true);
                }
            }

            if ($accessories) {
                $selectedAccessory = 0; $selectedAccessories = array();
                foreach ($accessories as $key => $accessoryValues) {
                    foreach ($accessoryValues as $value) {
                        if ($value != '') {
                            $selectedAccessory = (int)$value;
                        }
                    }
                    if ($selectedAccessory != 0) {
                        $selectedAccessories[] = $selectedAccessory;
                    }
                }
                //удаление связей
                $this->getModel()->unlink('CategoriesAccessories', $this->getModel()->CategoriesAccessories->toArray(), true);

                //добавление связей
                $this->getModel()->link('CategoriesAccessories', $selectedAccessories, true);
            }

            return true;
        }
    }

    /**
     * Parse ids of categories to array
     *
     * @param string $rawCategories
     * @return array|NULL
     */
    public function parseCategoriesIds($rawCategories)
    {
        if ($rawCategories) {
            return explode('-', $rawCategories);
        }
        return array();
    }
}