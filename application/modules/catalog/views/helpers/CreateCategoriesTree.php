<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_CreateCategoriesTree extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function CreateCategoriesTree()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'category');
        }
        return $this;
    }

    /**
     * Возвращает форму поиска
     *
     * @return string
     */
    public function getCategoriesTree($categories)
    {
        $categoriesTree = array();
        // Проходимся по дочерних категориях и составляем полное дерево
        foreach ($categories as $category) {
            // Обьеденяем деревья от каждой категориии в одно
            $categoriesTree = $this->_merge($categoriesTree, $this->_generateTree(array($category)));
        }

        return $this->_generateHtmlTree($categoriesTree);
    }

    /**
     * Обьеденение деревьев и суммирование полей с количеством продуктов
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function _merge($array1, $array2)
    {            
        foreach ($array2 as $key=>$value) {
            // Если есть с таким ключем значение - обьеденяем
            if (isset($array1[$key])) {
                // Суммируем количество продуктов к категории
                $array1[$key]['model']['products_count'] += $value['model']['products_count'];
                // Если есть родителькие в обоих массивах - рекурсивно обьеденяем
                if (isset($value['children']) && isset($array1[$key]['children'])) {
                    $array1[$key]['children'] = $this->_merge($array1[$key]['children'], $value['children']);
                // иначе — присваиваем как новое
                } elseif(isset($value['children'])) {
                    $array1[$key]['children'] = $value['children'];
                }
            // если нет - просто присваиваем как новое
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    /**
     * Генерирует полное дерево по дочерней категории
     * 
     * @param Doctrine_Collection $categories
     * @param array $categoriesArray
     * @return array
     */
    protected function _generateTree($categories, $categoriesArray = array())
    {
        foreach ($categories as $category) {
            // Если уже есть сгенерированые елементы в дереве
            if (!empty($categoriesArray)) {
                // Добавляем их как дочерние
                $categoriesArray = array(
                    $category->id => array('children' => $categoriesArray)
                );
            }
            // Добавляем данные о текущей категории
            $categoriesArray[$category->id]['model'] = $category->toArray();

            // Подсчитываем сумарное количество товаров для родительских категорий по известным значениям в дочерних
            if (!isset($categoriesArray[$category->id]['model']['products_count'])) {
                if (isset($categoriesArray[$category->id]['children'])) {
                    foreach ($categoriesArray[$category->id]['children'] as $value) {
                        if (!isset($categoriesArray[$category->id]['model']['products_count'])) {
                            $categoriesArray[$category->id]['model']['products_count'] = 0;
                        }
                        $categoriesArray[$category->id]['model']['products_count'] += $value['model']['products_count'];
                    }
                }
            }

            // Если еще есть родительские категории - рукурсивно обращаемся к ним
            if ($category->getNode()->hasParent() && $category->level > 1) {
                $categoriesArray = $this->_generateTree(array($category->getNode()->getParent()), $categoriesArray);
            }
        }
        
        return $categoriesArray;
    }

    /**
     * Генерация HTML для дерева категорий
     *
     * @param array $categories
     * @return string
     */
    protected function _generateHtmlTree($categories) {
        $output = '<ul>';
        $product = new Catalog_Model_ProductService();
        foreach ($categories as $category) {
            $productsCount = (isset($category['model']['products_count'])) ? $category['model']['products_count'] : 0;
            $output .= '<li><a href="#" id="c' . $category['model']['id'] . '">' . $category['model']['title'] . '</a><span>' . $productsCount . '</span>';
            if (isset ($category['children'])) {
                 $output .= $this->_generateHtmlTree($category['children']);
            }
            $output .= '</li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}