<?php

class Advertisment_Model_BannerService extends ZFEngine_Model_Service_Database_Abstract
{

    public $bannerWidth;
    public $bannerHeight;
    
    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Advertisment_Model_Banner';
    }

    /**
     * Обработка формы добавления
     *
     * @return boolean
     */
    public function processFormNew($rawData)
    {
        $form = $this->getForm('new');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $this->getModel()->fromArray($formValues);
                $this->getModel()->save();
                return true;

            } catch (Exception $e) {
                // @todo show forms errors
               // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении нового товара:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Обработка формы редактирования
     *
     * @return boolean|int
     */
    public function processFormEdit($rawData)
    {
        $form = $this->getForm('edit');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $this->getModel()->fromArray($formValues);
                $this->getModel()->save();
                return $this->getModel()->id;

            } catch (Exception $e) {
                // @todo show forms errors
               // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении нового товара:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Delete
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormDelete($rawData)
    {
        if (array_key_exists('submit_ok', $rawData)) {
            $this->getModel()->unlinkImages();
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    public function processFormBanner($rawData)
    {
        $form = $this->getForm($rawData['type']);
        $form->removeElement('image_filename');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            unset($formValues['background_image']);
            try {
                $banner = $this->getMapper()->findOneById((int) $formValues['id']);
                if (!$banner) {
                    $banner = $this->getModel(true);
                }
                if (!empty($formValues['period'])) {
                    $banner->last_renewal_date = Zend_Date::now()->toString('y-MM-dd WW');
                    $untillDate = new Zend_Date($this->getModel()->untill_date);
                    $banner->untill_date = $untillDate->addDay($formValues['period'])->toString('y-MM-dd WW');
                    $banner->status = 'available';
                } else {
                    unset ($formValues['period']);
                }

                if ($formValues['show_on'] != 'in-place') {
                    unset ($formValues['page_placement']);
                } else {
                    $lenght = strlen($formValues['page_placement']) - 1;
                    if ($formValues['page_placement']{$lenght} == '/') {
                        $formValues['page_placement'] = substr($formValues['page_placement'], 0, $lenght);
                    }
                }
                $banner->fromArray($formValues);
                $banner->save();
                return $banner->id;
            } catch (Exception $e) {
                // @todo show forms errors
//              echo $e->getMessage();
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
     * Обработка формы добавления картинок баннера
     *
     * @return boolean
     */
    public function processFormBannerImage($rawData)
    {
        if (isset ($rawData['type']) && $rawData['type'] == 'top') {
            $this->bannerWidth = 1024;
            $this->bannerHeight = 81;
        } else {
            $this->bannerWidth = 200;
            $this->bannerHeight = 300;
        }
        $form = $this->getForm('bannerImage');
        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $banner = $this->getMapper()->findOneById((int) $formValues['id']);
                if (!$banner) {
                    $banner = $this->getModel(true);
                }
                $banner->save();
                $productImage = new Advertisment_Model_BannerImageService();
                $productImage->getModel()->image_filename = $formValues['image_filename'];
                $productImage->getModel()->link('Banner', $banner->id);
                $productImage->getModel()->save();
                return $banner->id . '&' . $productImage->getModel()->image_filename . '&' . $productImage->getModel()->image_url
                         . '&' . $productImage->getModel()->id;;
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
     * Обработка формы добавления картинки фона
     *
     * @return boolean
     */
    public function processFormBannerBackgoundImage($rawData)
    {
        $form = $this->getForm('bannerBackgroundImage');
        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $banner = $this->getMapper()->findOneById((int) $formValues['id']);
                if (!$banner) {
                    $banner = $this->getModel(true);
                }
                $banner->background_image = $formValues['background_image'];
                $banner->save();
                return $banner->id . '&' . $banner->background_image . '&' . $banner->background_image_url;
            } catch (Exception $e) {
                // @todo show forms errors
//                echo $e->getMessage();
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
     * Get Product by id
     *
     * @param integer $id
     * @return Products_Model_Product
     */
    public function getModelById($id)
    {

        $product = $this->getMapper()->findOneById((int) $id);

        if ($product == false) {
            throw new Exception($this->getView()->translate('Banner not founds.'));
        }

        return $product;
    }

    /**
     * find Product by id and set model object for service layer
     *
     * @param integer $id
     * @return Products_Model_ProductService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }
}