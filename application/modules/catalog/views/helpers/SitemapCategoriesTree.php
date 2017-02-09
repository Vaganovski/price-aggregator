<?php

/**
 * Хелпер
 * <?php echo $this->CategoriesTree()
                ->setServiceLayer('products', 'category')
                ->setControllerName('categories')
                ->getCategoriesTree(); ?>
 */
class Catalog_View_Helper_SitemapCategoriesTree extends ZFEngine_View_Helper_Abstract
{

    /**
     * Выполняет роль конструктора
     *
     */
    public function SitemapCategoriesTree()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('catalog', 'category');
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
    public function getCategoriesTree()
    {
        $this->view->moduleName = $this->getModuleName();
        $this->view->controllerName = $this->getContorllerName();
        
        return $this->_renderItem($this->_serviceLayer->getCategoryCildren(0, ''));
    }

    protected function _renderItem($categories, $level = 1)
    {
        $countCategories = $categories->count();
        $inCol = floor ($countCategories / 3);
        $i = 0; $j = 1;
        $output = '';
        foreach ($categories as $category) {
            if ($level == 1 && $i == 0) {
                $output.= '<div class="column">';
            }
            $i++;
            $this->view->category = $category;
            if ($level == 1) {
                $title = '<h3>' . $category->title . '</h3>';
            } else {
                $output .= '<li>';
                $title = $category->title;
            }
            $output .= '<a href="'. $this->view->url(array(
                            'module' => 'catalog',
                            'controller' => 'categories',
                            'action' => ($level == 1) ? 'list' : 'view',
                            'alias' => $category->alias),
                        'default', true) .'">' . $title . '</a>';
            if ($category->getNode()->hasChildren()) {
                $output .= '<ul>';
                $output .= $this->_renderItem($category->getNode()->getChildren(), $level + 1);
                $output .= '</ul>';
            }
            if ($level == 1 && ($i > $inCol || $j == $countCategories)) {
                $output.= '</div>';
                $i = 0;
            }
            if ($level > 1) {
                $output .= '</li>';
            }
            $j++;
        }
        return $output;
    }

    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }

}