<?php

class Catalog_SearchController extends Zend_Controller_Action
{

    public function intit()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')->initContext();
        parent::init();
    }

    /**
     * Поиск
     */
    public function indexAction()
    {
       $this->view->BreadCrumbs()->appendBreadCrumb('Каталог', $this->view->url(array(
                                        'module'=>'catalog',
                                        'controller'=>'categories',
                                        'action'=>'list'
                                    ), 'default', true));
        $keywords = $this->_request->getParam('keywords');
        $this->view->setTitle(_('По запросу "%s"'), array($keywords));

        // Если есть фраза для запроса
        if (mb_strlen($keywords, 'UTF-8') > 1) {
            // Ищем совпадения в индексе
            $search = new Catalog_Model_SearchService();
            $this->view->keywords = $keywords;
            $hits = $search->search($keywords);
            $productsIdFromSearchIndex = array();
            foreach ($hits as $hit){
                $productsIdFromSearchIndex[] = $hit->product_id;
            }
            // получение массива идентификаторов брэндов
            $brand = $this->_helper->getServiceLayer($this->_request->getModuleName(),'brand');
            $brands = $brand->parseBrandIds($this->_request->brands);

            // Подтягиваем категории
            $categories = NULL;
            $category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
            if ($this->_request->categories) {
                $categoriesIds = $category->parseCategoriesIds($this->_request->categories);
                $categories = $category->getMapper()->findAllByCategoriesIds($categoriesIds);
            }

            $product = $this->_helper->getServiceLayer($this->_request->getModuleName(),'product');

            // Если есть найденные товары
            if (!empty($productsIdFromSearchIndex)) {
                
                // Определяемся с сортировкой
                $orderby = $this->_request->getParam('orderby', 'score');
                if ($orderby == 'score' && !$this->_hasParam('sort_direction')) {
                    $sortDirection = 'DESC';
                } else {
                    $sortDirection = $this->_request->getParam('sort_direction', 'ASC');
                }

                // Если сортировка по релевантности
                if ($orderby == 'score') {
                    // Фильтруем найденные из индекса товары по городу
                    $query = $product->getMapper()->findAllByParamsAsQuery(
                            NULL, $orderby, $sortDirection,$this->_request->city,
                            NULL, NULL, NULL, NULL, NULL, $productsIdFromSearchIndex, NULL
                    );
                    $tmp = $query->select('p.id')->fetchArray();
                    // По идентификаторам из этой переменной вытягиваются доступные атрибуты для фильрации
                    $productsIdFiltered = array();
                    foreach ($tmp as $value) {
                        $productsIdFiltered[] = $value['id'];
                    }
                    
                    // Фильтруем найденные из индекса товары по брендах/категориях
                    $query = $product->getMapper()->findAllByParamsAsQuery(
                            NULL,$orderby,$sortDirection,NULL, NULL,
                            $brands, NULL, NULL, NULL, $productsIdFiltered, $categories
                    );
                    $tmp = $query->select('p.id')->fetchArray();
                    // Конечный профилтрованый список идентификаторов товаров
                    $productsIdCurrent = array();
                    foreach ($tmp as $value) {
                        $productsIdCurrent[] = $value['id'];
                    }

                    // Исключаем лишние (не прошедшие фильтрацию) идентификаторы из начального списка
                    foreach ($productsIdFromSearchIndex as $key=>$value) {
                        if (!in_array($value, $productsIdCurrent) ) {
                            unset($productsIdFromSearchIndex[$key]);
                        }
                    }

                    // Если сортировка пл возрастанию релевантности - реверсим массив
                    if ($sortDirection == 'ASC') {
                        $productsIdFromSearchIndex = array_reverse($productsIdFromSearchIndex);
                    }

                    // Достаем идентификаторы для текущей страницы
                    $paginator =  $this->_helper->paginator->getPaginator($productsIdFromSearchIndex);
                    // Достаем товары по заданым идентификаторам
                    $productsCollection = $product->getMapper()->findByProductIds($paginator->getCurrentItems());
                    $products = array();

                    // Сортируем выбранные товары по релевантности
                    foreach ($productsIdFromSearchIndex as $productId) {
                        foreach ($productsCollection as $product) {
                            if ($product->id == $productId) {
                                $products[] = $product;
                                break;
                            }
                        }

                    }
                // Если сортировка по другим параметрам
                } else {
                    $productsIdFiltered = $productsIdFromSearchIndex;
                    $query = $product->getMapper()->findAllByParamsAsQuery(
                            NULL,$orderby,$sortDirection,$this->_request->city, NULL,
                            $brands, NULL, NULL, NULL, $productsIdFiltered, $categories
                    );
                    $paginator =  $this->_helper->paginator->getPaginator($query);
                    $products = $paginator->getCurrentItems();
                }

                    $this->view->paginator = $paginator;
                    $this->view->products = $products;
                    // брэнды
                    $this->view->brands = $brand->getMapper()->findAllByProductIds($productsIdFiltered);
                    $this->view->categories = $category->getMapper()->findAllByProductIds($productsIdFiltered);
            }

            if (!$this->getRequest()->isXmlHttpRequest()) {
                // вставляем ссылку для аякс запросов для получения списка товаров
                $categoryUrl = $this->view->url(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action'=>'index',
                            'format'=>'html'),
                         'default', true);
                $script = 'var catalogCategoriesViewUrl = "' . $categoryUrl . '?' . $_SERVER['QUERY_STRING'] . '";';
                $this->view->headScript()->appendScript($script);
                $this->view->headScript()->appendFile('/javascripts/product.filtering.js');
            } else {
                echo $this->view->render('/search/index.ajax.phtml');
                exit;
            }
        } else {
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->_helper->layout()->disableLayout();
            }
        }
    }
    
    private function _sortArray(&$array, $field , $sortOrder = 'ASC')
    {
        $size = count($array);
        for( $i = 0; $i < $size; $i++) {            // i - номер прохода
            for($j = $size - 1; $j > $i; $j--) {     // внутренний цикл прохода
                if ($sortOrder == 'DESC') {
                    if ($array[$j-1][$field] < $array[$j][$field]) {
                        $x = $array[$j - 1];
                        $array[$j - 1] = $array[$j];
                        $array[$j] = $x;
                    }
                } else {
                    if ($array[$j-1][$field] > $array[$j][$field]) {
                        $x = $array[$j - 1];
                        $array[$j - 1] = $array[$j];
                        $array[$j] = $x;
                    }
                }
            }
         }
    }
}
