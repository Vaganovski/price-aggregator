<?php

class Catalog_Model_ProductService extends Products_Model_ProductService
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Catalog_Model_Product';
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
            }
            // привязка к брэнду
            if (array_key_exists('brand_id', $rawData)) {
                $this->getModel()->link('Brand', $rawData['brand_id'], true);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Обработка формы заполнения характеристик товара
     *
     * @param $rawData
     * @return boolean|int
     */
    public function processFormEditFeatures($rawData)
    {
        $form = $this->getForm('editFeatures');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $featureService = new Catalog_Model_FeatureProductService();
                $featureService->save($formValues);
                return true;

            } catch (Exception $e) {
                $form->addError($this->getView()->translate('Произошла ошибка:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Обработка формы добавления картинок товара
     *
     * @param $rawData
     * @return boolean
     */
    public function processFormProductImage($rawData)
    {
        $form = $this->getForm('productImage');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $productImage = new Catalog_Model_ProductImageService();
                $productImage->getModel()->image_filename = $formValues['image_filename'];
                $productImage->getModel()->link('Product', $formValues['id']);
                $productImage->getModel()->save();
                return $productImage->getModel()->image_thumbnail_url;
            } catch (Exception $e) {
                // @todo show forms errors
                // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Обработка формы добавления картинок товара
     *
     * @param $rawData
     * @return boolean
     */
    public function processFormProductMainImage($rawData)
    {
        $form = $this->getForm('productMainImage');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $this->getModel()->image_filename = $formValues['image_filename'];
                $this->getModel()->image_to_download = null;
                $this->getModel()->save();
                return $this->getModel()->image_thumbnail_url;
            } catch (Exception $e) {
                // @todo show forms errors
                // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Обработка формы удаления картинок товара
     *
     * @param $rawData
     * @return boolean
     */
    public function processFormDeleteProductImage($rawData)
    {
        if (array_key_exists('filename', $rawData)) {
            // выдиляем название картинки для удаление
            $filename = $rawData['filename'];
            $filename = explode('/', $filename);
            $filename = $filename[count($filename) - 1];
            $pos = strpos($filename, '-');
            $imageFilename = substr($filename, 0, $pos);
            $imageFilename .= substr($filename, $pos + 6, strlen($filename));

            // удаление картинки с диска и БД
            $productImage = new Catalog_Model_ProductImageService();
            $productImage->findModelByImageFilename($imageFilename);
            $productImage->getModel()->unlinkImages();
            $productImage->getModel()->delete();
            return true;
        } else {
            return false;
        }
    }


    /**
     * Обработка добавления товара в мой список
     *
     * @param $productIds
     * @param $userId
     * @return boolean
     */
    public function processAddToMyList($productIds, $userId)
    {
        if (count($productIds) > 0) {
            $myProductList = new Catalog_Model_MyProductsListService();
            return $myProductList->save($productIds, $userId);
        }
        return false;
    }

    /**
     * изменяет статус отображения
     *
     * @param $visible
     * @return integer
     */
    public function processFormVisible($visible)
    {
        $this->getModel()->visible = $visible;
        $this->getModel()->save();
        return $this->getModel()->visible;
    }

    /**
     * Получение идентификатора группы характеристик
     *
     * @param $category
     * @return integer
     */
    public function getFeaturesGroup($category)
    {
        // если в данной категории отсутствует связь с группой характеристик
        // обходим всех предков
        if (!$category->features_group_id){
            if ($category->getNode()->hasParent()) {
                $this->getFeaturesGroup($category->getNode()->getParent());
            }
        }
        return $category->features_group_id;
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

    public function setInViewCompareUrl($categoryId, $backUrl = NULL)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $namespace = new Zend_Session_Namespace('product-to-compare');
        if (!$namespace->category_id || $namespace->category_id != $categoryId) {
            $namespace->category_id = $categoryId;
            $namespace->product_ids = array();
        }
        if ($backUrl) {
            $namespace->back_url = $backUrl;
        }
        $view = $this->getView();
        $compareUrl = $view->url(array(
                    'module' => $request->getModuleName(),
                    'controller' => 'products',
                    'action'=>'compare',
                    'products'=> implode('-', $namespace->product_ids ? $namespace->product_ids : array())),
                 'default', true);
        $script = 'var catalogCompareUrl = "' . $compareUrl . '";';
        $view->headScript()->appendScript($script);
    }
}