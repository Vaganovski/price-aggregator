<?php

class Marketplace_Model_ProductService extends Products_Model_ProductService
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        $this->setFormModelNamespace('delete', 'Products_Model_Product');
        // @todo refact
        $this->_modelName = 'Marketplace_Model_Product';
    }

    /**
     * Обработка формы добавления
     *
     * @param $rawData
     * @return boolean|int
     */
    public function processFormNew($rawData)
    {
        if (parent::processFormNew($rawData)) {
            // привязка к категории
             if (array_key_exists('category', $rawData)) {
                $selectedCategory = 0;
                foreach ($rawData['category'] as $category) {
                    if ($category != '') {
                        $selectedCategory = (int)$category;
                    }
                }
                $this->getModel()->link('Categories', $selectedCategory, true);

                $moderation = Zend_Registry::get('settings')->moderation_in_marketplace;
                if(!$moderation){
                    $this->approve(2);
                }
            }
            return true;
        }
    }


    public function approve($value)
    {
        if ($this->getModel() && ($value == 1 || $value == 2)) {
            $this->getModel()->approve = $value;
            $this->getModel()->save();
            return true;
        } else {
            return false;
        }
    }

  

    /**
     * Parse ids of products to array
     *
     * @param string $rawProducts
     * @return array|NULL
     */
    public function parseProductIds($rawProducts)
    {
        if ($rawProducts) {
            return explode('-', $rawProducts);
        }
        return array();
    }
}