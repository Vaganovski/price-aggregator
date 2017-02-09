<?php

class Catalog_Model_PriceService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     * @var Zend_Queue
     */
    protected $_queue = null;

    /**
     * Сервис слой категорий
     * @var Catalog_Model_CategoryService
     */
    protected $_categoryServiceLayer = null;

    /**
     * Сервис слой брэндов
     * @var Catalog_Model_BrandService
     */
    protected $_brandServiceLayer = null;

    /**
     * Сервис слой товаров
     * @var Catalog_Model_ProductService
     */
    protected $_productServiceLayer = null;

    /**
     * Количество добавленых товаров
     * @var int
     */
    protected $_addedProducts = 0;

    /**
     * Количество обновленых товаров
     * @var int
     */
    protected $_updatedProducts = 0;

    /**
     * Количество удаленных товаров
     * @var int
     */
    protected $_deletedProducts = 0;

    /**
     * Количество обработанных строк
     *
     * @var int
     */
    protected $_rowsCounter = 0;

    /**
     * Лимит на обработку строк
     *
     * @var int
     */
    protected $_rowsLimit = 500;

    /**
     * Последняя обработанная строка
     *
     * @var int
     */
    protected $_lastRow = 0;

    /**
     * Обработка завершена
     *
     * @var boolean
     */
    protected $_complete = true;

    /**
     * Инициализация
     */
    public function init()
    {

        parent::init();
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Catalog_Model_Price';
    }



    /**
     * Сервис слой категорий
     *
     * @return Catalog_Model_CategoryService
     */
    protected function _getCategoryService()
    {
        if ($this->_categoryServiceLayer == null) {
            $this->_categoryServiceLayer = new Catalog_Model_CategoryService;
        }
        return $this->_categoryServiceLayer;
    }

    /**
     * Очередь
     *
     * @return Zend_Queue
     */
    protected function _getQueue()
    {
        if ($this->_queue == null) {

            $db = Zend_Controller_Front::getInstance()->getParam('bootstrap')->config->resources->doctrine->connections->primary->dsn;

            $connection = Zend_Db::factory(
                'Pdo_Mysql',
                array(
                    'host'      => $db->host,
                    'username'  => $db->username,
                    'password'  => $db->password,
                    'dbname'    => $db->dbname,
                )
            );

            $this->_queue = new Zend_Queue(
                'Db',
                array(
                    'dbAdapter' => $connection,
                    'options' => array(
                        Zend_Db_Select::FOR_UPDATE => true
                    ),
                    'name' => 'parse-price',
                )
            );
        }
        return $this->_queue;
    }

    /**
     * Сервис слой брэндов
     *
     * @return Catalog_Model_BrandService
     */
    protected function _getBrandService()
    {
        if ($this->_brandServiceLayer == null) {
            $this->_brandServiceLayer = new Catalog_Model_BrandService;
        }
        return $this->_brandServiceLayer;
    }

    /**
     * Сервис слой брэндов
     *
     * @return Catalog_Model_ProductService
     */
    protected function _getProductService()
    {
        if ($this->_productServiceLayer == null) {
            $this->_productServiceLayer = new Catalog_Model_ProductService;
        }
        return $this->_productServiceLayer;
    }

    /**
     * Обработка формы загрузки прайса
     *
     * @return boolean
     */
    public function processFormUpload($rawData, $shop)
    {
        $form = $this->getForm('upload');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            if ($message = $this->addExcelPriceToQueue($formValues['price_filename'], $formValues['shop_id'])){
                $shop->price_status = Shops_Model_Shop::PRICE_STATUS_QUEUE;
                $shop->message_id = $message->message_id;
                $shop->save();
                if ($shop->status == Shops_Model_Shop::SHOP_STATUS_NEW) {
                    $this->_sendNotifyNewShopPrice($shop);
                }
                $this->addMessage($this->getView()->translate('Прайс успешно добавлен в очередь на обработку'), self::MESSAGE_SUCCESS);
                return true;
            } else {
                $form->addErrorMessage('Произошла ошибка при загрузке прайса');
               return false;
            }
        } else {
            $form->addErrorMessage('Произошла ошибка при загрузке прайса');
            return false;
        }
    }
    
    protected function _sendNotifyNewShopPrice($shop)
    {
        $this->getView()->shop = $shop;
        $users = Users_Model_UserTable::getInstance()->findAllAsQuery()->addWhere('role = ?', Users_Model_User::ROLE_ADMINISTRATOR)->fetchArray();
        foreach ($users as $user) {
            Users_Model_MailerService::sendmail(
                    $user['email'], $this->getView()->translate('Новый магазин загрузил прайс на eprice.kz'), $this->getView()->render('/mails/notifications.phtml')
            );
        }
    }

    /**
     * Парсинг прайса
     *
     * @param string $filename
     * @param integer $shopId
     *
     * @param array $options
     * @return boolean
     */
    public function addExcelPriceToQueue($filename, $shopId, $options = array())
    {
        try {
            $options['filename'] = $filename;
            $options['shopId'] = $shopId;
            $message = $this->_getQueue()->send(serialize($options));
            return $message;
        } catch (Exception $exc) {
            return false;
        }
    }

    /**
     * Выборка из очереди
     * @param int $count
     * @return boolean
     */
    public function parseFromQueue($count)
    {
        try {
            $items = $this->_getQueue()->receive($count);
            foreach ($items as $item) {
                $this->clearMessages();
                $args = unserialize($item->body);
                $ext = substr(strrchr($args['filename'], '.'), 1);
                // Удаляем задание, иначе возможна ситуация, когда
                // параллельно запущенный скрипт возьмет в обработку это же задание
                if ($ext == 'csv') {
                    $this->_getQueue()->deleteMessage($item);
                    call_user_func_array(array($this, 'parserCsvPrice'), array($args));
                } else {
                    $this->_getQueue()->deleteMessage($item);
                    call_user_func_array(array($this, 'parserExcelPrice'), array($args));
                }
            }
            return true;
        } catch (Zend_Mail_Protocol_Exception $exc) {
            Zend_Debug::dump($exc->getMessage());
            Zend_Debug::dump($args);
            return false;
        } catch (Exception $exc) {
            Zend_Debug::dump($exc->getMessage());
            Zend_Debug::dump($exc->getTraceAsString());
            return false;
        }
    }


    /**
     * Парсинг excel прайса
     *
     * @param array $options
     * @return boolean
     */
    public function parserExcelPrice($options)
    {
        $filename = $options['filename'];
        $shopId = $options['shopId'];
        $skip = false;

        if (file_exists($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt')) {
            $messages = unserialize(file_get_contents($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt'));
            if (is_array($messages)) {
                foreach ($messages as $type=>$message) {
                    $this->addMessage(array_pop($message));
                }
            }
        }

        try {
            $excel = new ZFEngine_Data_Spreadsheet_Excel($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename);
        } catch (Exception $exc) {
            $skip = true;
            $this->_complete = true;
            $shopServiceLayer = $this->_invalidFile($filename, $shopId);
        }

        if (!$skip) {
            if ($excel->getColumnCount() > 9 && $excel->getColumnCount() < 8) {
                $shopServiceLayer = $this->_invalidFile($filename, $shopId);
            } else {
                if (isset($options['lastRow'])) {
                    $this->_lastRow = $options['lastRow'];
                    $this->_rowsLimit += $this->_lastRow;
                }
                if (isset($options['updatedProducts'])) {
                    $this->_updatedProducts = $options['updatedProducts'];
                }
                if (isset($options['addedProducts'])) {
                    $this->_addedProducts = $options['addedProducts'];
                }
                if (isset($options['deletedProducts'])) {
                    $this->_deletedProducts = $options['deletedProducts'];
                }

                // получение массива из excel файла
                $rawData = $excel->toArray();
                unset($excel);
                $this->_rowsCounter = 0;
                $pricesCount = isset($options['pricesCount']) ? $options['pricesCount'] : 0;
                foreach ($rawData as $row) {
                    $this->_rowsCounter++;
                    if ($this->_rowsCounter < 3 || ($this->_lastRow + 1) > $this->_rowsCounter) {
                        unset($rawData[$this->_rowsCounter]);
                        continue;
                    }
                    // формирование массива значений для валидации при помощи формы
                    $arrayForValidate = array(
                        'category'    => $row[1],
                        'brand'       => $row[2],
                        'model'       => $row[3],
                        'description' => $row[4],
                        'price'       => $row[5],
                        'available'   => $row[6],
                        'url'         => $row[7],
                        'photo'       => $row[8],
                        'shop_id'     => $shopId
                    );
                    if (!strlen($arrayForValidate['model'])) {
                        continue;
                    }

                    $result = $this->processExcelRow($arrayForValidate);
                    if ($result !== false) {
                        $pricesCount++;
                    }

                    if ($this->_rowsCounter > $this->_rowsLimit) {
                        $this->_lastRow = $this->_rowsCounter;
                        $this->_complete = false;
                        break;
                    }
                    unset($rawData[$this->_rowsCounter],$row,$arrayForValidate);
                }
                unset($rawData);


                if (!$this->_complete) {
                    $this->addExcelPriceToQueue(
                        $filename,
                        $shopId,
                        array(
                            'pricesCount' => $pricesCount,
                            'lastRow' => $this->_lastRow,
                            'updatedProducts' => $this->_updatedProducts,
                            'addedProducts' => $this->_addedProducts,
                            'deletedProducts' => $this->_deletedProducts,
                        )
                    );
                    file_put_contents($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt', serialize($this->getMessages()));
                } else {
                    // удаляем предложения, которых нет в новом прайсе
                    foreach ($this->getMapper()->findAllRemovedByShopId($shopId) as $price) {
                        $productId = $price->product_id;
                        $price->delete();
                        $price->free();
                        $this->_deletedProducts++;
                        // обновляем цены для данного продукта
                        if ($newPrices = $this->getMapper()->findPriceByProductId($productId)) {
                            Catalog_Model_ProductTable::getInstance()->updatePriceByProductId($productId, $newPrices);
                        }
                    }
                    $this->getMapper()->updateExistFlagByShopId($shopId);


                    $locale = Zend_Locale::findLocale();
                    $this->addMessage($this->getView()->translate('Всего обработано позиций в прайсе: ') . $pricesCount);
                    $this->addMessage($this->getView()->translate('Обновлено ') . $this->_updatedProducts . ' ' .
                            $this->getView()->translate(array(_('товар'), _('товара'), _('товаров'), $this->_updatedProducts, $locale)));
                    $this->addMessage($this->getView()->translate('Добавлено ') . $this->_addedProducts . ' ' .
                            $this->getView()->translate(array(_('товар'), _('товара'), _('товаров'), $this->_addedProducts, $locale)));
                    $this->addMessage($this->getView()->translate('Удалено ') . $this->_deletedProducts . ' ' .
                            $this->getView()->translate(array(_('товар'), _('товара'), _('товаров'), $this->_deletedProducts, $locale)));

                    // сохранение названия прайса в текущем магазине
                    $shopServiceLayer = new Shops_Model_ShopService();
                    $shopServiceLayer->findModelById($shopId);
                    if ($shopServiceLayer->getModel()->price_filename) {
                        $this->_removePriceFile($shopServiceLayer->getModel()->price_filename);
                    }
                    $shopServiceLayer->getModel()->price_filename = $filename;
                    $shopServiceLayer->getModel()->price_status = Shops_Model_Shop::PRICE_STATUS_PROCESSED;
                    $shopServiceLayer->getModel()->price_updated_at = new Doctrine_Expression('NOW()');
                    $shopServiceLayer->getModel()->save();
                }
            }
        }

        if ($this->_complete) {
            $this->getView()->setScriptPath(APPLICATION_PATH . '/modules/shops/views/scripts/index');
            $this->getView()->messages = $this->getMessages();
            Users_Model_MailerService::sendmail(
                $shopServiceLayer->getModel()->email,
                $this->getView()->translate('Результат обработки прайса на eprice.kz'),
                $this->getView()->render('mails/price-complete.phtml')
            );
        }
    }
    
    /**
     * Парсинг csv прайса
     *
     * @param array $options
     * @return boolean
     */
    public function parserCsvPrice($options)
    {
        $filename = $options['filename'];
        $shopId = $options['shopId'];
        $skip = false;
		setlocale(LC_ALL, 'ru_RU');
		
		if (file_exists($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt')) {
            $messages = unserialize(file_get_contents($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt'));
            if (is_array($messages)) {
                foreach ($messages as $type=>$message) {
                    $this->addMessage(array_pop($message));
                }
            }
        }

        try {
        	$handle = fopen($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename, "r");
        } catch (Exception $exc) {
            $skip = true;
            $this->_complete = true;
            $shopServiceLayer = $this->_invalidFile($filename, $shopId);
        }

        if (!$skip) {
				if (isset($options['lastRow'])) {
                    $this->_lastRow = $options['lastRow'];
                    $this->_rowsLimit += $this->_lastRow;
                }
                if (isset($options['updatedProducts'])) {
                    $this->_updatedProducts = $options['updatedProducts'];
                }
                if (isset($options['addedProducts'])) {
                    $this->_addedProducts = $options['addedProducts'];
                }
                if (isset($options['deletedProducts'])) {
                    $this->_deletedProducts = $options['deletedProducts'];
                }
                // построчная загрузка csv файла
                $this->_rowsCounter = 0;
                $pricesCount = isset($options['pricesCount']) ? $options['pricesCount'] : 0;
                while (($row = fgetcsv($handle, 8192, ";")) !== FALSE) {
                    $this->_rowsCounter++;
                    if ($this->_rowsCounter < 3 || ($this->_lastRow + 1) > $this->_rowsCounter) {
                        unset($row);
                        continue;
                    }
                 
                    for ($i = 0;$i < count($row);$i++) {
            			$row[$i] = mb_convert_encoding($row[$i],"UTF-8","WINDOWS-1251");
                    }
                    // формирование массива значений для валидации при помощи формы
                    $arrayForValidate = array(
                        'category'    => $row[0],
                        'brand'       => $row[1],
                        'model'       => $row[2],
                        'description' => $row[3],
                        'price'       => $row[4],
                        'available'   => $row[5],
                        'url'         => $row[6],
                        'photo'       => $row[7],
                        'shop_id'     => $shopId
                    );
                    $result = $this->processExcelRow($arrayForValidate);
                    if ($result !== false) {
                        $pricesCount++;
                    }
                    if ($this->_rowsCounter > $this->_rowsLimit) {
                        $this->_lastRow = $this->_rowsCounter;
                        $this->_complete = false;
                        break;
                    }

                    unset($row,$arrayForValidate);
                }
				fclose($handle);
				
				if (!$this->_complete) {
                    $this->addExcelPriceToQueue(
                        $filename,
                        $shopId,
                        array(
                            'pricesCount' => $pricesCount,
                            'lastRow' => $this->_lastRow,
                            'updatedProducts' => $this->_updatedProducts,
                            'addedProducts' => $this->_addedProducts,
                            'deletedProducts' => $this->_deletedProducts,
                        )
                    );
                    file_put_contents($this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt', serialize($this->getMessages()));
                } else {
                    // удаляем предложения, которых нет в новом прайсе
                    foreach ($this->getMapper()->findAllRemovedByShopId($shopId) as $price) {
                        $productId = $price->product_id;
                        $price->delete();
                        $price->free();
                        $this->_deletedProducts++;
                        // обновляем цены для данного продукта
                        if ($newPrices = $this->getMapper()->findPriceByProductId($productId)) {
                            Catalog_Model_ProductTable::getInstance()->updatePriceByProductId($productId, $newPrices);
                        }
                    }
                    $this->getMapper()->updateExistFlagByShopId($shopId);


                    $locale = Zend_Locale::findLocale();
                    $this->addMessage($this->getView()->translate('Всего обработано позиций в прайсе: ') . $pricesCount);
                    $this->addMessage($this->getView()->translate('Обновлено ') . $this->_updatedProducts . ' ' .
                            $this->getView()->translate(array(_('товар'), _('товара'), _('товаров'), $this->_updatedProducts, $locale)));
                    $this->addMessage($this->getView()->translate('Добавлено ') . $this->_addedProducts . ' ' .
                            $this->getView()->translate(array(_('товар'), _('товара'), _('товаров'), $this->_addedProducts, $locale)));
                    $this->addMessage($this->getView()->translate('Удалено ') . $this->_deletedProducts . ' ' .
                            $this->getView()->translate(array(_('товар'), _('товара'), _('товаров'), $this->_deletedProducts, $locale)));

                    // сохранение названия прайса в текущем магазине
                    $shopServiceLayer = new Shops_Model_ShopService();
                    $shopServiceLayer->findModelById($shopId);
                    if ($shopServiceLayer->getModel()->price_filename) {
                        $this->_removePriceFile($shopServiceLayer->getModel()->price_filename);
                    }
                    $shopServiceLayer->getModel()->price_filename = $filename;
                    $shopServiceLayer->getModel()->price_status = Shops_Model_Shop::PRICE_STATUS_PROCESSED;
                    $shopServiceLayer->getModel()->price_updated_at = new Doctrine_Expression('NOW()');
                    $shopServiceLayer->getModel()->save();
                }
            
        }

        if ($this->_complete) {
            $this->getView()->setScriptPath(APPLICATION_PATH . '/modules/shops/views/scripts/index');
            $this->getView()->messages = $this->getMessages();
            Users_Model_MailerService::sendmail(
                $shopServiceLayer->getModel()->email,
                $this->getView()->translate('Результат обработки прайса на eprice.kz'),
                $this->getView()->render('mails/price-complete.phtml')
            );
        }
    }

    /**
     * Неправильный формат файла
     * @param string $filename
     * @param int $shopId
     */
    protected function _invalidFile($filename, $shopId)
    {
        $this->addMessage('Ваш прайс не подходит по формату');
        $this->_removePriceFile($filename);
        $shopServiceLayer = new Shops_Model_ShopService();
        $shopServiceLayer->findModelById($shopId);
        $shopServiceLayer->getModel()->price_status = Shops_Model_Shop::PRICE_STATUS_ABSENT;
        $shopServiceLayer->getModel()->save();
        return $shopServiceLayer;
    }

    /**
     * Удаляет файл прайса
     *
     * @param string $filename
     */
    protected function _removePriceFile($filename)
    {
        $filepath = $this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $filepath = $this->getModel()->getPriceAbsoluteUploadPath() . '/' . $filename . '.txt';
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    /**
     * Обработка одной строки из excel-прайса
     *
     * @param array $rawData
     *
     * @return boolean
     */
    public function processExcelRow($rawData)
    {
        $form = $this->getForm('check');

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                // находим категорию
                $categoryId = $this->_getCategoryService()->getMapper()->getIdByTitle($formValues['category']);
                if (is_null($categoryId)) {
                    $message = sprintf($this->getView()->translate('Товар "%s %s" не добавлен.'), $formValues['brand'], $formValues['model']);
                    $message .= ' ' . sprintf($this->getView()->translate("Категория \"%s\" не найдена."), $formValues['category']);
                    $this->addMessage($message);
                    return false;
                }

                // находим брэнд
                if ($brandId = $this->_getBrandService()->getMapper()->getIdByName($formValues['brand'])) {
                    // находим товар в нужной категории
                    $product = $this->_getProductService()->getMapper()->findOneByFullNameAndCategoryId($formValues['model'], $brandId, $categoryId);
                } else {
                    $product = false;
                }

                // если продавец раньше добавлял этот товар в другую категорию находим их
                $productsForDelete = $this->_getProductService()->getMapper()->findIdsToDelete(
                    $formValues['model'],
                    $brandId,
                    ($product) ? array($product->id) : array()
                );
                // удаляем товары из прайса продавца
                if (count($productsForDelete) > 0) {
                    $this->getMapper()->deletePrices($productsForDelete, $formValues['shop_id']);
                }

                // если брэнда нету создаем новый
                if ($brandId == null) {
                    $newBrand = array(
                        'name' => $formValues['brand'],
                        'description' => ''
                    );
                    $this->_getBrandService()->processFormNew($newBrand, true);
                    $brandId = $this->_getBrandService()->getModel()->id;
                    $this->_getBrandService()->removeModel();
                }

                $_FILES['image_filename']['name'] = '';
                $_FILES['image_filename']['tmp_name'] = '';
                $_FILES['image_filename']['error'] = 4;

                // если товара нету создаем новый
                if (!$product) {
                        if (strlen($formValues['photo'])) {
                            $image = $formValues['photo'];
                        } else {
                            $image = null;
                        }

                        // Чистим форму
                        $this->_getProductService()->clearForm('new');
                        $this->_getProductService()->getForm('new')->setDefaults(
                            array('categories' => $this->_getCategoryService()->getMapper(),
                                  'brands' => $this->_getBrandService()->getMapper())
                        );
                        $newProduct = array(
                            'category' => array($categoryId),
                            'name' => $formValues['model'],
                            'brand_id' => $brandId,
                            'description' => $formValues['description'],
                            'image_to_download' => $image
                        );
                        if (!$this->_getProductService()->processFormNew($newProduct)) {
                            throw new Exception($this->getView()->translate("Произошла ошибка при добавлении нового товара"));
                        }
                        $product = $this->_getProductService()->getModel();
						unset($newProduct);
                }
                // находим прайс по идентификатору продукта и магазина
                $price = $this->getMapper()->findOneByProductIdAndShopIdAndDescription($product->id, $formValues['shop_id'], $formValues['description']);

                // если есть редактируем запись, если нет создаем новую
                if ($price) {
                    $price->price = $formValues['price'];
                    $price->url = $formValues['url'];
                    $price->description = $formValues['description'];
                    $price->available = ($formValues['available'] == 'Заказ' ? 'in_stock' : 'available');
                    $price->exist = true;
                    $price->save();

                    $prices = $this->getMapper()->findPriceByProductId($product->id);

                    $product->min_price = $prices['MIN'];
                    $product->max_price = $prices['MAX'];
                    $product->avg_price = $prices['AVG'];
                    if (strlen($formValues['photo']) && !$product->image_filename) $product->image_to_download = $formValues['photo'];
                    $product->save();
                    $this->_updatedProducts++;
                    $lastPriceProductId = $price->id;
                    unset($price,$product, $image, $formValues);
                    return $lastPriceProductId;
                } else {
                    $this->getModel(true)->price = $formValues['price'];
                    $this->getModel()->url = $formValues['url'];
                    $this->getModel()->description = $formValues['description'];
                    $this->getModel()->available = ($formValues['available'] == 'Заказ' ? 'in_stock' : 'available');
                    $this->getModel()->link('Product', $product->id);
                    $this->getModel()->link('Shop', $formValues['shop_id']);
                    $this->getModel()->exist = true;
                    $this->getModel()->save();

                    $prices = $this->getMapper()->findPriceByProductId($product->id);

                    $product->min_price = $prices['MIN'];
                    $product->max_price = $prices['MAX'];
                    $product->avg_price = $prices['AVG'];
                    $product->save();
                    $this->_addedProducts++;
                    $lastPriceProductId = $this->getModel()->id;
                    $this->removeModel();
                    unset($product,$image,$formValues);
                    return $lastPriceProductId;
                }

                return false;

            } catch (Exception $e) {
                $this->addMessage($this->getView()->translate('Произошла ошибка:'). $e->getMessage());
                $message = sprintf($this->getView()->translate('Товар "%s %s" не добавлен.'), $formValues['brand'], $formValues['model']);
                $this->addMessage($message);
                return false;
            }
        } else {
            $message = sprintf($this->getView()->translate('Товар "%s %s" не добавлен.'), $rawData['brand'], $rawData['model']);
            $message .= ' ' . $this->getView()->translate('Не пройдена валидация данных.');
            $this->addMessage($message);
            return false;
        }
    }

    /**
     * Возвращает список Id продуктов по запросу с прайса
     * @param Doctrine_Query $query
     * @return array
     */
    public function getProductIds($query)
    {
        $productsIds = array();
        foreach ($query->select('p.id pid, pr.id')->fetchArray() as $value) {
            $productsIds[] = $value['pid'];
        }
        return $productsIds;
    }

    /**
     * Возвращает список Id продуктов по запросу с прайса
     * @param Doctrine_Query $query
     * @param array $categories
     * @return array
     */
    public function addCategoryFilter($query, $categories)
    {
        if ($categories) {
             $query->leftJoin('p.CategoryProduct cp')
                 ->leftJoin('cp.Category c');
             $index = 0;
             $queryPart = '';
             $params = array();
             foreach ($categories as $category) {
                $index++;
                if ($index > 1) {
                    $queryPart .= ' OR ';
                }
                $queryPart .= "(c.lft >= ? AND c.rgt <= ?)";
                $params[] = $category['lft'];
                $params[] = $category['rgt'];
             }
             $query->addWhere($queryPart, $params);
        }
    }


    /**
     * Get Price by id
     *
     * @param integer $id
     * @return Products_Model_Price
     */
    public function getModelById($id)
    {

        $price = $this->getMapper()->findOneById((int) $id);

        if ($price == false) {
            throw new Exception($this->getView()->translate('Прайс не найден.'));
        }

        return $price;
    }

    /**
     * find Price by id and set model object for service layer
     *
     * @param integer $id
     * @return Products_Model_PriceService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }
}

function convert($size)
 {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 }