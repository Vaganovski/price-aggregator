<?php

class Shops_Model_ShopService extends Shops_Model_ShopBaseService
{

    /**
     *
     */
    public function init()
    {

        parent::init();
        $this->setFormModelNamespace('edit',__CLASS__);
        $this->setFormModelNamespace('new',__CLASS__);
        $this->setFormModelNamespace('editImage',__CLASS__);
        $this->setFormModelNamespace('renewal',__CLASS__);
        $this->setFormModelNamespace('searchAdmin',__CLASS__);
        $this->setFormModelNamespace('reject',__CLASS__);
    }

    /**
     * Обработка формы редактирования
     *
     * @return boolean|int
     */
    public function processFormEditImage($rawData)
    {
        $form = $this->getForm('editImage');
        
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
                $form->addError($view->translate('Произошла ошибка при редактировании:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Disable
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormDisable($rawData)
    {
        $form = $this->getForm('reject');

        if ($form->isValid($rawData)) {
            if (array_key_exists('submit_ok', $rawData)) {
                $this->getModel()->status = 'disable';
                $this->getModel()->save();
                if ($form->getValue('reason')) {
                    try {
                        $view = Zend_Layout::getMvcInstance()->getView();
                        $mail = new Zend_Mail('UTF-8');

                        $mail->addTo($form->getValue('email'));

                        $mail->setSubject(sprintf($view->translate('Ваш магазин %s отключен на сайте eprice.kz'), $this->getModel()->name));

                        $emailConfig = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('email');
                        $mail->setFrom($emailConfig['noreply']);

                        $text = $form->getValue('reason');

                        $mail->setBodyHtml($text, 'UTF-8', Zend_Mime::ENCODING_BASE64);
                        $mail->send();
                    } catch (Zend_Exception $e) {

                        $form->addError($view->translate('Произошла ошибка при редактировании:') . $e->getMessage());
                        $form->populate($rawData);
                        return false;
                    }
                }
                return true;
            } else {
                return true;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
        return false;
    }

    /**
     * Создание магазина
     * @param array $rawData
     * @return boolean
     */
    public function processFormNew($rawData)
    {
        $result = parent::processFormNew($rawData);
        if ($result && $this->getView()->isAllowed('mvc:shops:index', 'admin')) {
            $this->getModel()->status = 'available';
            $this->getModel()->save();
        }
        return $result;
    }

    /**
     * Available
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormAvailable($rawData)
    {
        if (array_key_exists('submit_ok', $rawData)) {
            $this->getModel()->status = 'available';
            $this->getModel()->save();
            return true;
        }
        return false;
    }

    /**
     * Обработка формы редактирования
     *
     * @return boolean|int
     */
    public function processFormRenewal($rawData)
    {
        $form = $this->getForm('renewal');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $this->getModel()->period = $formValues['period'];
                $this->getModel()->last_renewal_date = Zend_Date::now()->toString('y-MM-dd WW');
                $untillDate = new Zend_Date($this->getModel()->untill_date);
                $this->getModel()->untill_date = $untillDate->addDay($formValues['period'])->toString('y-MM-dd WW');
                $this->getModel()->status = 'available';
                $this->getModel()->save();
                return true;

            } catch (Exception $e) {
               // @todo show forms errors
               // echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при редактировании:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     *  Обновление цен для продуктов магазина
     */
    public function updatePrices()
    {
        $priceService = new Catalog_Model_PriceService();
        $productService = Catalog_Model_ProductTable::getInstance();
        $products = $priceService->getMapper()->findProductsIdsByShopId($this->getModel()->id);

        if (!empty ($products)) {
            $toUpdate = $priceService->getMapper()->findMaxMinAvgPriceByProducts($products);

            $conn = Doctrine_Manager::connection();
            try {
                $conn->beginTransaction();

                foreach ($products as $product) {
                    if (isset($toUpdate[$product])) {
                        $prices = $toUpdate[$product];
                    } else {
                        $prices = array(
                            'MIN' => 0,
                            'MAX' => 0,
                            'AVG' => 0
                        );
                    }
                    $productService->updatePriceByProductId($product, $prices);
                }

                $conn->commit();
            } catch(Doctrine_Exception $e) {
                $conn->rollback();
            }
        }
    }


    public function isAvailable()
    {
        if ($this->getModel()->status == 'available') {
            return true;
        }
        return false;
    }
}