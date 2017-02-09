<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_SubCategories extends ZFEngine_View_Helper_Abstract
{

    /**
     * Субкатегории
     */
    public function subCategories($categoryService)
    {
        if ($categoryService->getModel()->getNode()->isRoot() || !$categoryService->getModel()->id) {
           $this->view->setTitle($this->view->translate('Категории'));
        } else {
           $this->view->setTitle($categoryService->getModel()->title);
        }
        $childCategories = $categoryService->getCategoryCildren(0, $categoryService->getModel()->alias);
        if ($childCategories) {
            $this->view->categories = $childCategories;
            $this->view->category = $categoryService->getModel();
        }
        return $this->view->render($this->getViewScript());
    }

}