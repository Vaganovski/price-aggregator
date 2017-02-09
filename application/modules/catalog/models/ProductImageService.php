<?php

class Catalog_Model_ProductImageService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Catalog_Model_ProductImage';
    }
    /**
     * Get ProductImage by id
     *
     * @param integer $filename
     * @return Catalog_Model_ProductImage
     */
    public function getModelByImageFilename($filename)
    {
        $productImage = $this->getMapper()->findOneByImageFilename($filename);
        if ($productImage == false) {
            throw new Exception($this->getView()->translate('Такого файла картинки не существует.'));
        }
        return $productImage;
    }

    /**
     * find ProductImage by id and set model object for service layer
     *
     * @param integer $filename
     * @return Catalog_Model_ProductImageService
     */
    public function findModelByImageFilename($filename)
    {
        $this->setModel($this->getModelByImageFilename($filename));
        return $this;
    }
 
}