<?php

class Catalog_Model_MyProductsListService extends Products_Model_ProductService
{

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->_modelName = 'Catalog_Model_MyProductsList';
    }

    public function save($productIds, $userId)
    {
        $existingProductIds = $this->getMapper()->findExistingProductsIds($userId);
        $existedProductIds = array();
        foreach ($existingProductIds as $productId) {
            $existedProductIds[] = $productId;
        }
        foreach ($productIds as $productId) {
            if (in_array($productId, $existedProductIds)) {
                continue;
            }
            $this->getModel(true)->link('Product', $productId);
            $this->getModel()->link('User', $userId);
            $this->getModel()->save();
        }
        return true;
    }
}