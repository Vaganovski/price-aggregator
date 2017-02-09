<?php

/** @property $_serviceLayer Catalog_Model_ProductService */
class Catalog_ProductsController extends Products_Controller_Abstract_Index
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('search-autocomplete', 'html')
                    ->addActionContext('shop-list', 'html')
                    ->addActionContext('add-to-compare', 'json')
                    ->addActionContext('add-to-my-list', 'json')
                    ->initContext();
        parent::init();
    }

    /**
     *  New Product
     *
     *  @return void
     */
    public function newAction()
    {
        $this->_helper->layout->setLayout('admin');
        $category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');
        $brand = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'brand');
        $form = $this->_serviceLayer->getForm('new');
        if ($this->_request->isPost()) {
            $categories = $category->getMapper();
            $form->setDefaults(array('categories' => $category->getMapper(),
                                     'brands' => $brand->getMapper()));
        } else {
            $form->setDefaults(array('brands' => $brand->getMapper()));
        }
        // @todo refact!
        // формирование массива для загрузки каскадных селектов
        $selectedPath[] = array('id'=> 0,
                             'level' => 1,
                             'selected' => '');
        $category->transformArrayToJavascriptArray($selectedPath);


        parent::newAction();
        
        $category->insertListJsonLinkAsJsScript();

    }

    /**
     *  Edit Product
     *
     *  @return void
     */
    public function editAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $category = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'category');
        $brand = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'brand');
        $form = $this->_serviceLayer->getForm('edit');
        if ($this->_request->isPost()) {
            $categories = $category->getMapper();
            // заполняем форму значениями по умолчанию (категории и брэнды)
            $form->setDefaults(array('categories' => $category->getMapper(),
                                     'brands' => $brand->getMapper()));
        } else {
            // заполняем форму значениями по умолчанию (брэнды)
            $form->setDefaults(array('brands' => $brand->getMapper()));
            // выбираем категорию товара
            $category->findCategoryById($this->_serviceLayer->getModel()->Categories[0]->id);
            // получаем массив всей цепочки категорий
            $categories = $category->getSelectedCategoriesArray(false);
            // превращаем массив Javascript массив и вставляем в скрипт представления
            $category->transformArrayToJavascriptArray($categories);
         }


        parent::editAction();

        $category->insertListJsonLinkAsJsScript();

    }

    /**
     *  Заполнения характеристик товара
     *
     *  @return void
     */
    public function editFeaturesAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->_serviceLayer->findModelById($this->_request->getParam('id'));
        $this->view->setTitle($this->_serviceLayer->getModel()->name);

        $featuresGroupId = $this->_serviceLayer->getFeaturesGroup($this->_serviceLayer->getModel()->Categories[0]);
        if ($featuresGroupId) {
            $featuresGroup = $this->_helper->getServiceLayer('features', 'group');
            $featuresGroup->findModelById($featuresGroupId);
            $featuresGroup->getModel()->Sets;

            $featureProduct = $this->_helper->getServiceLayer($this->_request->getModuleName(), 'featureProduct');
            $featuresOfProduct = $featureProduct->getModelByProductId($this->_request->getParam('id'));
            $values = array();
            // формирование массива характеристик для передачи в скрипт представления
            foreach ($featuresGroup->getModel()->Sets as $set) {
                foreach ($set->Fields as $field) {
                    foreach ($field->Values as $value) {
                        if ($featuresOfProduct) {
                            foreach ($featuresOfProduct as $feature) {
                                if ($feature->features_value_id == $value->id) {
                                    $values['s' . $set->id][$field->id] = $feature->features_value_id;
                                }
                            }
                        }
                    }
                }
            }
            $values['id'] = $this->_serviceLayer->getModel()->id;

            $default = $featuresGroup->getModel()->Sets->toArray();
            
            /** @var $form Catalog_Form_Product_EditFeatures */
            $form = $this->_serviceLayer->getForm('editFeatures');
            // генерация формы по наборам характеристик
            $form->generateForm($default);

            // обработка формы
            if ($this->_request->isPost()) {
                    $postData = $this->_request->getPost();
                    $result = $this->_serviceLayer->processFormEditFeatures($postData);
                    if ($result == true) {
                        $this->_helper->redirector->gotoRoute(array(
                                'module' => $this->_request->getModuleName(),
                                'controller' => $this->_request->getControllerName(),
                                'action' => 'list-admin',
                            'default', true
                         ));
                    }
             } else {
                 $form->populate($values);
             }

             $this->view->form = $form;
        }
        // форма добавления картинок
        $formImage = $this->_serviceLayer->getForm('productImage');
        $formImage->id->setValue($this->_serviceLayer->getModel()->id);
        $this->view->formImage = $formImage;
        $formMainImage = $this->_serviceLayer->getForm('productMainImage');
        $formMainImage->id->setValue($this->_serviceLayer->getModel()->id);
        $this->view->formMainImage = $formMainImage;
        // получение загруженых картинок
        $this->view->images = $this->_serviceLayer->getModel()->Images;
        $uploadImageUrl = $this->view->url(array(
                   'module' => $this->_request->getModuleName(),
                   'controller' => $this->_request->getControllerName(),
                   'action'=>'upload-image'),
                'default',
                true);
        $uploadMainImageUrl = $this->view->url(array(
                   'module' => $this->_request->getModuleName(),
                   'controller' => $this->_request->getControllerName(),
                   'action'=>'upload-main-image'),
                'default',
                true);

        $this->view->product =  $this->_serviceLayer->getModel();
        
        $this->view->headScript()->appendFile('/javascripts/ajaxupload.js');
        $this->view->headScript()->appendFile('/javascripts/jquery.client-upload.js');
        $script = 'var catalogProductImageUrl = "' . $uploadImageUrl . '";' . "\n";
        $script .= 'var catalogProductMainImageUrl = "' . $uploadMainImageUrl . '";';
        $this->view->headScript()->appendScript($script);
        
    }

    /**
     *  Загрузка картинок
     *
     *  @return void
     */
    public function uploadImageAction()
    {
        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormProductImage($postData);
                if ($result) {
                    echo $result;
                } else {
                    echo "error";
                }
         }
        exit;
    }
    /**
     *  Загрузка картинок
     *
     *  @return void
     */
    public function uploadMainImageAction()
    {
        if ($this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $this->_serviceLayer->findModelById($this->_request->getParam('id'));
                $result = $this->_serviceLayer->processFormProductMainImage($postData);
                if ($result) {
                    echo $result;
                } else {
                    echo "error";
                }
         }
        exit;
    }

    /**
     *  Удаление картинок
     *
     *  @return void
     */
   public function deleteImageAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
                $postData = $this->_request->getPost();
                $result = $this->_serviceLayer->processFormDeleteProductImage($postData);
                if ($result) {
                    echo "true";
                } else {
                    echo "false";
                }
         }
        exit;
    }

    /**
     *  Удаление картинок
     *
     *  @return void
     */
   public function deleteMainImageAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
                $postData = $this->_request->getPost();
                try {
                    $this->_serviceLayer->findModelById($this->_request->getParam('id'));
                    $this->_serviceLayer->getModel()->image_filename = '';
                    $this->_serviceLayer->getModel()->save();
                    echo "true";
                } catch (Exception $e) {
                    echo "false";
                }
         }
        exit;
    }

    /**
     *  Список популярных товаров
     *
     *  @return void
     */
    public function popularAdminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle(_('Популярные товары'));
        $this->view->products = $this->_serviceLayer->getMapper()->findAllPopularWithLimit(21, $this->_request->visible, false);
        $this->view->visible = $this->_request->visible;
    }

    /**
     *  Список новых товаров
     *
     *  @return void
     */
    public function newAdminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle(_('Новые товары'));
        $this->view->products = $this->_serviceLayer->getMapper()->findAllNewWithLimit(21, $this->_request->visible, false);
        $this->view->visible = $this->_request->visible;
    }

    /**
     *  Список товаров в админке
     *
     *  @return void
     */
    public function listAdminAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->setTitle(_('Товары / модели'));

        $keywords = $this->_request->getParam('keywords');
        $city = $this->_request->getParam('city');
        $selectedCategory = null;
        if (is_array($this->_request->category)) {
            foreach ($this->_request->category as $rawCategory) {
                if (!empty ($rawCategory)) {
                    $selectedCategory = $rawCategory;
                }
            }
        } else {
            $selectedCategory = $this->_request->category;
        }

        $search = new Catalog_Model_SearchService();
        $this->view->keywords = $keywords;
        $productsIdArray = array();
        if ($keywords !== null) {
            $hits = $search->search($keywords);
            foreach ($hits as $hit){
                $productsIdArray[] = $hit->product_id;
            }
        }
        if (!empty($keywords) && count($productsIdArray) == 0) {
            $productsIdArray[] = 0;
        }
        // @todo refact
        switch ($this->_request->tab) {
            case 'no-filled':
                $query = $this->_serviceLayer->getMapper()->findAllNoFilledAsQuery($productsIdArray, $city, $selectedCategory);
                break;
            case 'filled':
                $query = $this->_serviceLayer->getMapper()->findAllFilledAsQuery($productsIdArray, $city, $selectedCategory);
                break;
            case 'new':
            default:
                $query = $this->_serviceLayer->getMapper()->findAllNewAsQuery($productsIdArray, $city, $selectedCategory);
                break;
        }
        $paginator =  $this->_helper->paginator->getPaginator($query);
        $this->view->paginator = $paginator;
        $this->view->products = $paginator->getCurrentItems();
        $this->view->tab = $this->_request->tab;


    }

    /**
     *  Измененние статуса отображения товара
     *
     *  @return void
     */
    public function visibleAdminAction()
    {
        if (($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost())) {
            $this->_helper->layout->disableLayout();
            $this->_serviceLayer->findModelById($this->_request->getParam('id'));
            echo $this->_serviceLayer->processFormVisible($this->_request->visible);
            // @todo заюзати ajaxContent
            exit;
        }

    }

    /**
     *  Просмотр товара
     *
     *  @return void
     */
    public function viewAction() {
        parent::viewAction();
        $this->view->CategoriesChain($this->_serviceLayer->getModel()->Categories->getFirst()->id);
        $features = array();
        // формирование массива всех характеристик и значений для передачи в скрипт представления
        foreach ($this->_serviceLayer->getModel()->FeatureProduct as $featureOfProduct) {
            $features[$featureOfProduct->Value->Field->Set->title][$featureOfProduct->Value->Field->title] = $featureOfProduct->Value->title . ' ' . $featureOfProduct->Value->Field->unit;
        }
        $this->view->features = $features;

        $price = $this->_helper->getServiceLayer($this->_request->getModuleName(),'price');
        $this->view->prices = $price->getMapper()->findAllByProductId($this->_serviceLayer->getModel()->id);
        // форма заказа товара
        $this->_serviceLayer->getForm('buy')->product_id->setValue($this->_serviceLayer->getModel()->id);

        $buyForm = $this->_serviceLayer->getForm('buy');
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $user = $this->_helper->getServiceLayer('users', 'user');
            $user->findUserByAuth();
            $buyForm->getElement('name')->setValue($user->getModel()->name);
            $buyForm->getElement('email')->setValue($user->getModel()->email);
        }
        $this->view->buyForm = $buyForm;
        $namespace = new Zend_Session_Namespace('product-to-compare');
        $this->view->product_ids = $namespace->product_ids;

        $this->_serviceLayer->setInViewCompareUrl($this->_serviceLayer->getModel()->Categories->getFirst()->id,
                $this->_request->getRequestUri());
        
        $this->view->headLink()->appendStylesheet('/stylesheets/notebooks-inner.css');
    }

    /**
     *  get prices of product
     *
     *  @return void
     */
    public function getPricesAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            //$this->_serviceLayer->findModelById($this->_request->getParam('product_id'));
         
            Zend_Layout::getMvcInstance()->disableLayout();
            echo $this->view->showPrices($this->_request->product_id,
                                         $this->_request->order_by,
                                         $this->_request->sort_direction);
            exit;
        }
    }

    /**
     *  Заказ товара
     *
     *  @return void
     */
    public function buyAction(){

        $form = $this->_serviceLayer->getForm('buy');
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getParams();

            if ($form->isValid($formData)) {
                $formValues = $form->getValues();
                $this->_serviceLayer->findModelById($formValues['product_id']);
                $price = $this->_helper->getServiceLayer('catalog', 'price');
                $price->findModelById($formValues['price_id']);
                try {
                    $mail = new Zend_Mail('UTF-8');

                    $mail->addTo($price->getModel()->Shop->Manager->email);

                    $mail->setSubject($this->view->translate('Заказ товара c eprice.kz'));
                    $mail->setFrom($formValues['email']);

                    $this->view->product = $this->_serviceLayer->getModel()->name . ' ' . $price->getModel()->description;
                    $this->view->formValues = $formValues;
                    $text = $this->view->render('mails/buy.phtml');

                    $mail->setBodyHtml($text, 'UTF-8', Zend_Mime::ENCODING_BASE64);
                    $mail->send();
                } catch (Zend_Exception $e) {
                    $content = array(
                        'success'=> false,
                        'message' => $this->view->translate('Произошла ошибка при отправке почты!')
                    );
                    $this->_helper->json($content);
                    return;
                }

                $content = array(
                    'success'=> true,
                    'message' => $this->view->translate('Ваш заказ успешно отправлен!')
                );
                $this->_helper->json($content);
                return;
            } else {
                $formError = $form->getMessages();
                $title = array(
                    'message' => $this->view->translate('Сообщение')
                );
                $message = '';
                foreach ($formError as $element => $error) {
                    $message .= $form->getElement($element)->getLabel() . ': ' . implode("; ", $error) . ".\n";
                }
                $content = array(
                    'success'=> false,
                    'message' => $message
                );
                $this->_helper->json($content);
                return;
            }
        }
    }

    /**
     *  Добавление товара в мой список
     *
     *  @return void
     */
    public function addToMyListAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()
                && Zend_Auth::getInstance()->hasIdentity()) {
            $productIds = $this->_serviceLayer->parseProductIds($this->_request->products);
            $result = $this->_serviceLayer->processAddToMyList($productIds, Zend_Auth::getInstance()->getIdentity()->id);
            if ($result) {
                $this->view->result = array('success' => true,
                                            'message' => $this->view->translate('Товары добавлены в Ваш список.'),
                                            'htmlLink' => $this->view->MyListProductsCountLink());
            } else {
                $this->view->result = array('success' => false,
                                            'message' => $this->view->translate('Произошла ошибка, свяжитесь с администрацией.'));
            }
         } else {
             $productIds = $this->_serviceLayer->parseProductIds($this->_request->products);
             $namespace = new Zend_Session_Namespace('product-to-mylist');
             if (!is_array($namespace->product_ids)) {
                 $namespace->product_ids = array();
             }
             $productIds = array_unique(array_merge($productIds, $namespace->product_ids));
             $namespace->product_ids = $productIds;
             $this->view->result = array('success' => true,
                                         'message' => $this->view->translate('Товары добавлены в Ваш список.'),
                                         'htmlLink' => $this->view->MyListProductsCountLink());
         }
    }

    /**
     *  Удаление товара из моего списока
     *
     *  @return void
     */
    public function deleteFromMyListAction() {
        if ($this->getRequest()->isXmlHttpRequest() && $this->_request->isPost()) {
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $productsList = $this->_helper->getServiceLayer($this->_request->getModuleName(),'myProductsList');
                $result = $productsList->getMapper()->deleteByProductIdAndUserId($this->_request->product_id,
                        Zend_Auth::getInstance()->getIdentity()->id);
                if ($result) {
                    echo "true";
                } else {
                    echo "false";
                }
            } else {
                $namespace = new Zend_Session_Namespace('product-to-mylist');
                $index = array_search($this->_request->product_id, $namespace->product_ids);
                unset($namespace->product_ids[$index]);
                echo "true";
            }
         } else {
            echo "false";
         }
        exit;
    }

    /**
     *  Просмотр товаров в моем списке
     *
     *  @return void
     */
    public function myListAction() {
        $this->view->setTitle(_('Мой список'));
        $this->view->BreadCrumbs()->appendBreadCrumb('Профиль', $this->view->url(array(
                 'module'=>'users',
                 'controller'=>'index',
                 'action'=>'profile',
             ), 'default', true));
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $user = $this->_helper->getServiceLayer('users', 'user');
            $user->findUserByAuth();
            $productsList = $this->_helper->getServiceLayer($this->_request->getModuleName(),'myProductsList');
            $query = $productsList->getMapper()->findProductsFromList($user->getModel()->id);
            $this->view->user = $user->getModel();
        } else {
            $namespace = new Zend_Session_Namespace('product-to-mylist');
            $productIds = $namespace->product_ids;
            if (count($productIds) == 0) {
                $productIds = array(0 => 0); // добавил 0 в массив что бы запрос ничего не нашел, нужно если массив пустой
            }
            $query = $this->_serviceLayer->getMapper()->findByProductIds($productIds, true);
        }

        $paginator =  $this->_helper->paginator->getPaginator($query);
        $this->view->paginator = $paginator;
        $this->view->products = $paginator->getCurrentItems();
    }

    /**
     *  Просмотр товаров в моем списке
     *
     *  @return void
     */
    public function shopListAction() {
        if ($this->_request->id) {
            $shop = new Shops_Model_ShopService();
            $shop->findModelById($this->_request->id);
            $this->view->BreadCrumbs()->appendBreadCrumb('Продавцы', $this->view->url(array(
                             'module'=>'shops',
                             'controller'=>'index',
                             'action'=>'index'
                         ), 'default', true));
            $this->view->BreadCrumbs()->appendBreadCrumb($shop->getModel()->name, $this->view->url(array(
                             'module'=>'shops',
                             'controller'=>'index',
                             'action'=>'view',
                             'id' => $shop->getModel()->id
                         ), 'default', true));
            $this->view->setTitle(_('Прайс-лист %s'), array($shop->getModel()->name));



            $pricesServiceLayer = new Catalog_Model_PriceService();
            
            /* @var $query Doctrine_Query */
            $query = $pricesServiceLayer->getMapper()->findAllByShopId($shop->getModel()->id, true);

            $productsIds = $pricesServiceLayer->getProductIds(clone $query);
            
            // Подтягиваем категории для фильтрации
            $category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
            if ($this->_request->categories) {
                $categoriesIds = $category->parseCategoriesIds($this->_request->categories);
                $categories = $category->getMapper()->findAllByCategoriesIds($categoriesIds);
                $pricesServiceLayer->addCategoryFilter($query, $categories);
            }

            $paginator =  $this->_helper->paginator->getPaginator($query);
            $this->view->paginator = $paginator;
            $this->view->prices = $paginator->getCurrentItems();

            $category = $this->_helper->getServiceLayer($this->_request->getModuleName(),'category');
            if (!empty ($productsIds)) {
                $this->view->categories = $category->getMapper()->findAllByProductIds($productsIds);
            }

            if (!$this->getRequest()->isXmlHttpRequest()) {
                // вставляем ссылку для аякс запросов для получения списка товаров
                $categoryUrl = $this->view->url(array(
                            'module' => $this->_request->getModuleName(),
                            'controller' => $this->_request->getControllerName(),
                            'action' => 'shop-list',
                            'id' => $shop->getModel()->id,
                            'format' => 'html'),
                         'default', true);
                $script = 'var catalogCategoriesViewUrl = "' . $categoryUrl . '?' . $_SERVER['QUERY_STRING'] . '";';
                $this->view->headScript()->appendScript($script);
                $this->view->headScript()->appendFile('/javascripts/product.filtering.js');
            } else {
                echo $this->view->render('/products/shop-list.ajax.phtml');
                exit;
            }

        } else {
            $this->_forward('not-found', 'error', 'default');
        }
    }

    public function addToCompareAction() {
        $namespace = new Zend_Session_Namespace('product-to-compare');
        if ($this->_request->products) {
            $productIds = $this->_serviceLayer->parseProductIds($this->_request->products);
            if (!$namespace->product_ids) {
                $namespace->product_ids = array();
            }
            $productIds = array_unique(array_merge($namespace->product_ids, $productIds));
            $namespace->product_ids = $productIds;
            $this->view->result = array('success' => true,
                                        'url' => $this->view->url(array(
                                                'module' => $this->_request->getModuleName(),
                                                'controller' => 'products',
                                                'action'=>'compare',
                                                'products'=> implode('-', $namespace->product_ids ? $namespace->product_ids : array())),
                                             'default', true),
                                         'htmlLink' => $this->view->CompareProductsCountLink());
        } else {
            $namespace->__unset('product_ids');
            $this->view->result = array('success' => true);
        }
    }

    /**
     *  Просмотр товаров в моем списке
     *
     *  @return void
     */
    public function compareAction() {
        $this->view->setTitle(_('Сравнение товаров'));
        $namespace = new Zend_Session_Namespace('product-to-compare');
        if ($this->_request->products) {
            $productIds = $this->_serviceLayer->parseProductIds($this->_request->products);
            $productIds = array_unique($productIds);
            $namespace->product_ids = $productIds;
        } elseif ($this->_request->delete) {
            $namespace->product_ids = NULL;
            $productIds = NULL;
        } else {
            $productIds = $namespace->product_ids;
        }

        if (!empty ($productIds)) {
            $products = $this->_serviceLayer->getMapper()->findByProductIds($productIds);
            if ($products->count()) {
                $featuresProducts = array();
                $features = array();
                foreach ($products as $product) {
                    if ($product->FeatureProduct->count()) {
                        foreach ($product->FeatureProduct as $featureOfProduct) {
                            $features[$featureOfProduct->Value->Field->Set->title][$featureOfProduct->Value->Field->title][$product->id] = $featureOfProduct->Value->title;
                            $featuresProducts[$product->id][$featureOfProduct->Value->Field->Set->title][$featureOfProduct->Value->Field->title] = $featureOfProduct->Value->title;
                        }
                    } else {
                        $featuresProducts[$product->id] = array();
                    }
                    $categoryAlias = $product->Categories->getFirst()->alias;
                }
                $this->view->products = $products;
                $this->view->features = $features;
                $this->view->featuresProducts = $featuresProducts;
                $this->view->productIds = $productIds;
            }
        }
        if ($namespace->back_url) {
            $this->view->backUrl = $namespace->back_url;
        } else {
            $this->view->backUrl = $this->view->url(array(
                    'module' => $this->_request->getModuleName(),
                    'controller' => 'categories',
                    'action'=>'view',
                    'alias'=> $categoryAlias),
                 'default', true);
        }
    }

    /**
     *  Поиск данных для автокмплита
     *
     *  @return void
     */
    public function searchAutocompleteAction()
    {
        $products = $this->_serviceLayer->getMapper()->findAllByKeywordsAndCity($this->_request->q, $this->_request->city);
        $response = '';
        foreach ($products as $product)
        {
            $response .= $product->name . '|' . $product->Prices->count() . ' ' . $this->view->translate(array('предложение', 'предложения', 'предложений', $product->Prices->count(), $this->locale)) . "\n";
        }
        $this->view->clearVars();
        $this->view->response = $response;
    }
}