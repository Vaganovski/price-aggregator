<?php

class Advertisment_Model_BannerImageService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Advertisment_Model_BannerImage';
    }
    /**
     * Get ProductImage by id
     *
     * @param integer $id
     * @return Catalog_Model_ProductImage
     */
    public function getModelById($id)
    {
        $bannerImage = $this->getMapper()->findOneById($id);
        if ($bannerImage == false) {
            throw new Exception($this->getView()->translate('Такого файла картинки не существует.'));
        }
        return $bannerImage;
    }

    /**
     * find ProductImage by id and set model object for service layer
     *
     * @param integer $id
     * @return Catalog_Model_ProductImageService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }
 
}