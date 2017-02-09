<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню пользователя
 */
class Catalog_View_Helper_MarketpalceCategoriesChain extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function MarketpalceCategoriesChain($categoryId, $isOutputLast = true)
    {
        $category = new Marketplace_Model_CategoryService();
        $category->findCategoryById($categoryId);
        $ancestors = $category->getModel()->getNode()->getAncestors();
        $count = sizeof($ancestors);
        if ($ancestors) {
            $this->view->BreadCrumbs()->appendBreadCrumb('Барахолка', $this->view->url(array(
                                            'module'=>'marketplace',
                                            'controller'=>'categories',
                                            'action'=>'list'
                                        ), 'default', true));
            foreach ($ancestors as $ancestor)
            {
                if($ancestor->title == 'Root') {
                    continue;
                }
                $this->view->BreadCrumbs()->appendBreadCrumb($ancestor->title, $this->view->url(array(
                                        'module'=>'marketplace',
                                        'controller'=>'categories',
                                        'action'=>'list',
                                        'alias'=>$ancestor->alias,
                                    ), 'default', true));
            }
            if ($isOutputLast) {
                $this->view->BreadCrumbs()->appendBreadCrumb($category->getModel()->title, $this->view->url(array(
                                        'module'=>'marketplace',
                                        'controller'=>'categories',
                                        'action'=>'view',
                                        'alias'=>$category->getModel()->alias,
                                    ), 'default', true));
            }

        }
        return $this;
    }
}
