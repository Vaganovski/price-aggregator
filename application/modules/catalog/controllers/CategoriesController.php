<?php

class Catalog_CategoriesController extends Categories_Controller_Abstract_Index
{
    /**
     *  Список категорий в админке
     *
     *  @return void
     */
    public function listAdminAction() {
        $this->view->setTitle(_('Категории товаров'));
        $this->_helper->layout->setLayout('admin');
    }

    public function listAction() {
        parent::listAction();
        $this->view->CategoriesChain($this->category->getModel()->id, false);
    }
    /**
     *  New category
     *
     *  @return void
     */
    public function newAction() {
        $this->_helper->layout->setLayout('admin');
        $defaults = $this->_getFeaturesGroupArray();
        $this->category->getForm('new')->setDefaults($defaults);
        $this->_request->setParam('action_redirect', 'list-admin');

        // формирование массива для загрузки каскадных селектов
        $selectedPath = array();
        if ($this->_request->accessory) {
            $i = 0;
            foreach ($this->_request->accessory as $accessory) {
                $previusValue = 0; $level = 1;
                foreach ($accessory as $value) {
                    $selectedPath[$i][] = array('id'=> $previusValue,
                                    'level' => $level++,
                                    'selected' => (int)$value,
                                    'selectedToDelete' => 0);
                    $previusValue = $value;
                }
                $i++;
            }
        } else {
            $selectedPath[0][] = array('id'=> 0,
                                    'level' => 1,
                                    'selected' => 0,
                                    'selectedToDelete' => 0);
        }
        // превращаем массив в json вид
        $jsArray = json_encode($selectedPath);
        $script = 'var dataAccessoriesArray = ' . $jsArray . ';';
        $this->view->headScript()->appendScript($script);
        parent::newAction();
        $this->view->setTitle(_('Категории товаров'));
    }

    /**
     *  Edit category
     *
     *  @return void
     */
    public function editAction() {
        $this->_helper->layout->setLayout('admin');
        $this->_request->setParam('action_redirect', 'list-admin');
        $defaults = $this->_getFeaturesGroupArray();
        $this->category->getForm('edit')->setDefaults($defaults);
        parent::editAction();
        $this->view->setTitle(_('Категории товаров'));
        
        // формирование массива для загрузки каскадных селектов
        $selectedPath = array();
        if ($this->_request->accessory) {
            $i = 0;
            foreach ($this->_request->accessory as $accessory) {
                $previusValue = 0; $level = 1;
                foreach ($accessory as $value) {
                    $selectedPath[$i][] = array('id'=> $previusValue,
                                    'level' => $level++,
                                    'selected' => (int)$value,
                                    'selectedToDelete' => 0);
                    $previusValue = $value;
                }
                $i++;
            }
        } else {
            $selectedPath[0][] = array('id'=> 0,
                                'level' => 1,
                                'selected' => 0,
                                'selectedToDelete' => 0);
            $categoriesAccessories = $this->category->getModel()->CategoriesAccessories;

            foreach ($categoriesAccessories as $accessory) {
                $selectedPath[] = $this->category->getSelectedCategoriesArray(false, false, $accessory);
            }
        }
        // превращаем массив в json вид
        $jsArray = json_encode($selectedPath);
        $script = 'var dataAccessoriesArray = ' . $jsArray . ';';
        $this->view->headScript()->appendScript($script);
    }

    /**
     *  Удаление региона
     *
     *  @return void
     */
    public function deleteAction() {
        $this->_helper->layout->setLayout('admin');
        $this->_request->setParam('action_redirect', 'list-admin');
        parent::deleteAction();
        $title = sprintf($this->view->translate('Вы хотите удалить категорию "%s"? Все вложенные категории будут удалены!'),
                $this->category->getModel()->title);
        $this->view->title = $title;
    }

    /**
     *  Get array with features group
     *
     *  @return void
     */
    private function _getFeaturesGroupArray() {
        $feature = $this->_helper->getServiceLayer('features','group');
        $default = $feature->getMapper()->findAll()->toKeyValueArray('id', 'title');
        $result = array('' => $this->view->translate('Выберите...'));
        foreach ($default as $key => $value) {
            $result[$key] = $value;
        }
        return array('features_group_id'=>$result);
    }

    /**
     *  View category
     *
     *  @return void
     */
    public function viewAction() {
        // поиск категории
        $this->category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
        $this->category->findCategoryByIdOrAlias(0, $this->_request->alias);
        $this->view->categoryService = $this->category;
        $this->view->setTitle($this->category->getModel()->title);

        $this->view->CategoriesChain($this->category->getModel()->id, false);
        
        // получение массива идентификаторов брэндов
        $brand = $this->_helper->getServiceLayer($this->_request->getModuleName(),'brand');
        $brands = $brand->parseBrandIds($this->_request->brands);

        // выборка товаров с параметрами
        $product = $this->_helper->getServiceLayer($this->_request->getModuleName(),'product');
        if ($this->_request->city == NULL) {
            $this->_request->setParam('city', 'Алматы');
        }

        // количество магазинов
        $shops = $this->_helper->getServiceLayer('shops','shop');
        $shopsCount = $shops->getMapper()->countAll();
         // получение массива идентификаторов характеристик
        $featureProduct = $this->_helper->getServiceLayer($this->_request->getModuleName(),'featureProduct');

        $featuresMinMax = array(); $features = array();
        foreach ($this->_request->getPost() as $key => $value) {
            if (($result = strpos($key, 'f_from')) !== false ) {
                $id = explode('_', $key);
                $featuresMinMax[$id[count($id) - 1]]['min'] = $value;
            }
            if (($result = strpos($key, 'f_to')) !== false ) {
                $id = explode('_', $key);
                $featuresMinMax[$id[count($id) - 1]]['max'] = $value;
            }
            if (($result = strpos($key, 'feature')) !== false ) {
                $id = explode('_', $key);
                $features[$id[count($id) - 1]] = $featureProduct->parseFeaturesIds($value);
            }
        }
        $categoriesIds = $this->category->getModel()->getAllChildrenIds();
        $categoriesIds[] = $this->category->getModel()->id;
        $query = $product->getMapper()->findAllByParamsAsQuery(
                $categoriesIds,
                $this->_request->orderby,
                $this->_request->sort_direction,
                $this->_request->city,
                $this->_request->keywords,
                $brands,
                $features,
                $this->_request->price_from,
                $this->_request->price_to,
                NULL, NULL,
                $featuresMinMax,
                $shopsCount
        );
        
        $this->view->city = $this->_request->city;//'Выберите...';

        $paginator =  $this->_helper->paginator->getPaginator($query);

        $this->view->paginator = $paginator;

        $this->view->products = $paginator->getCurrentItems();

        // если аякс запрос отдаем только представление списка товаров
        if ($this->getRequest()->isXmlHttpRequest()) {
            Zend_Layout::getMvcInstance()->disableLayout();
            echo $this->view->render('/products/list.phtml');
            exit;
        }

        // характеристики
        $featuresSets = array();
        foreach ($this->category->getModel()->Group->Sets as $set) {
            foreach ($set->Fields as $field) {
                if ($field->slider == 1) {
                    $valueServiceLayer = new Features_Model_ValueService();
                    $value = $valueServiceLayer->getMapper()->findMinAndMaxByFieldId($field->id);
                    $minMaxInJS = 'catalog_f_default_from[' . $field->id . '] = ' . $value->getFirst()->min . ";\n";
                    $minMaxInJS .= 'catalog_f_default_to[' . $field->id . '] = ' . $value->getFirst()->max . ";\n";
                    $featuresSets['sliding'][] = $field;
                } else {
                    $featuresSets['simple'][] = $field;
                }
            }
        }
        $this->view->featuresSets = $featuresSets;
        // брэнды
        $this->view->brands = $brand->getMapper()->findAllByCategoryId($categoriesIds);

        $script = '';
        $minAndMaxPrice = $product->getMapper()->findMinAndMaxPrices($categoriesIds);
        if ($minAndMaxPrice->count()) {
            if ($minAndMaxPrice->getFirst()->min_price > 0) {
                $script = 'var catalogMinPrice = ' . $minAndMaxPrice->getFirst()->min_price . ";\n";
            }
            if ($minAndMaxPrice->getFirst()->max_price > 0) {
                $script .= 'var catalogMaxPrice = ' . $minAndMaxPrice->getFirst()->max_price . ";\n";
            }
        }
        // вставляем ссылку для аякс запросов для получения списка товаров
        $categoryUrl = $this->view->url(array(
                    'module' => $this->_request->getModuleName(),
                    'controller' => $this->_request->getControllerName(),
                    'action'=>'view',
                    'alias'=>$this->category->getModel()->alias),
                 'default', true);
        $script .= 'var catalogCategoriesViewUrl = "' . $categoryUrl . '";';

        $product->setInViewCompareUrl($this->category->getModel()->id, $categoryUrl);

        $this->view->headScript()->appendFile('/javascripts/product.filtering.js');
        $this->view->headScript()->appendScript($script);
        if (!empty ($minMaxInJS)) {
            $this->view->headScript()->appendScript($minMaxInJS);
        }
    }
}