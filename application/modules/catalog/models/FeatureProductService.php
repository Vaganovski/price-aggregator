<?php

class Catalog_Model_FeatureProductService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Catalog_Model_FeatureProduct';
    }

    /**
     * Сохраняет связь товара и значение характеристики
     *
     * @param array $data
     * @return boolean
     */
    public function save($data)
    {
        $this->getMapper()->deleteAllByProductId($data['id']);
        foreach ($data['fields'] as $field) {
            foreach ($field as $key => $value) {
                // если значение текстовое, то сначала создаем его в таблице
                // значений характеристики, а потом привязвываем к товару
                if (array_key_exists('text' . $key, $field)) {
                    $newFieldValue = new Features_Model_Value();
                    $newFieldValue->title = $value;
                    $newFieldValue->link('Field', $key);
                    $newFieldValue->save();
                    $value = $newFieldValue->id;
                }
                if (strstr($key, 'text')) continue;
                $this->getModel(true)->product_id = $data['id'];
                $this->getModel()->link('Value', $value);
                $this->getModel()->save();
            }
        }
        return true;
    }

    /**
     * Get FeatureProduct by product id
     *
     * @param integer $id
     * @return Products_Model_Product
     */
    public function getModelByProductId($id)
    {
        $featuresProduct = $this->getMapper()->findByProductId((int) $id);

        return $featuresProduct;
    }

    /**
     * find FeatureProduct by id and set model object for service layer
     *
     * @param integer $id
     * @return Products_Model_ProductService
     */
    public function findModelByProductId($id)
    {
        $this->setModel($this->getModelByProductId($id));
        return $this;
    }

    /**
     * Parse ids of features values to array
     *
     * @param string $rawFeaturess
     * @return array|NULL
     */
    public function parseFeaturesIds($rawFeatures)
    {
        if ($rawFeatures) {
            return explode('-', $rawFeatures);
        }
        return NULL;
    }
}