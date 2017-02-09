<?php

class Categories_Model_CategoryService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Categories_Model_Category';
    }


     /**
     * Проверка или существует рутовска категория для данного типа модели
     * если не найдена, создается новая рутовская категория
     *
     * @param string $calledClassName
     * @return Doctrine_Record
     */
    protected function _checkAndCreateRootRecord()
    {
       $rootCategory = $this->getMapper()->findOneByLevel(0);

       if ($rootCategory) {
           return $rootCategory;
       } else {
           $newCategory = new $this->_modelName;
           $newCategory->title = 'Root';
           $newCategory->save();
           $treeObject = $this->getMapper()->getTree();
           $treeObject->createRoot($newCategory);
           return $newCategory;
       }
    }

    
     /**
     * Обработка формы добавление категории,
     *
     * @param array $rawData
     * @param string $calledClassName
     * @return boolean|int
     */
    public function processFormNew($rawData)
    {
        $form = $this->getForm('new');

        $form->getSubForm('category')
                ->getElement('0')
                ->addMultiOptions(
                        $this->getMapper()->findAll()->toKeyValueArray('id', 'title')
                );

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            $parentCategory = null;
            try {
                // @todo refact: clone code
                if (array_key_exists('category', $rawData)) {
                    $selectedCategory = 0;
                    foreach ($rawData['category'] as $category) {
                        if ($category != '') {
                            $selectedCategory = (int)$category;
                        }
                    }
                }
                $parentCategory = $this->getMapper()->find($selectedCategory);
                // если переданная родительская катгория не существует,
                // то получаем рутовскую категорию
                if (!$parentCategory) {
                    $parentCategory = $this->_checkAndCreateRootRecord();
                }
                
                // записываем данные о категории
                $this->getModel()->fromArray($form->getValues());

                $this->getModel()->save();
                // привязываем к категории предку
                $this->getModel()->getNode()->insertAsLastChildOf($parentCategory);
                return $this->getModel()->getIncremented();

            } catch (Exception $e) {
                // @todo show forms errors
                //echo $e->getMessage();
                $form->addError($this->getView()->translate('Произошла ошибка при добавлении страницы:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }




     /**
     * Обработка формы добавление категории,
     *
     * @param array $rawData
     * @param string $calledClassName
     * @return boolean|int
     */
    public function processFormEdit($rawData)
    {
        $form = $this->getForm('edit');
        $form->getSubForm('category')
                ->getElement('0')
                ->addMultiOptions(
                        $this->getMapper()->findAll()->toKeyValueArray('id', 'title')
                );

        if ($form->isValid($rawData)) {
            $formValues = $form->getValues();
            try {
                $selectedCategory = 0;
                if (array_key_exists('category', $rawData)) {
                    foreach ($rawData['category'] as $category) {
                        if ($category != '') {
                            $selectedCategory = (int)$category;
                        }
                    }
                }
                $parentCategory = $this->getMapper()->find($selectedCategory);
                // если переданная родительская катгория не существует,
                // то получаем рутовскую категорию
                if (!$parentCategory) {
                    $parentCategory = $this->_checkAndCreateRootRecord();
                }
                
                // записываем данные о категории
                $this->getModel()->fromArray($form->getValues());
                
                $this->getModel()->save();
                // привязываем к категории предку
                if ($this->getModel()->id != $parentCategory->id &&
                        $this->getModel()->getNode()->getParent()->id != $parentCategory->id) {
                    $this->getModel()->getNode()->moveAsLastChildOf($parentCategory);
                }
                return $this->getModel()->getIncremented();

            } catch (Exception $e) {
                // @todo show forms errors
                //echo $e->getMessage();
                $view = Zend_Layout::getMvcInstance()->getView();
                $form->addError($view->translate('Произошла ошибка при добавлении страницы:') . $e->getMessage());
                $form->populate($rawData);
                return false;
            }
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Processing delete page form
     */
    public function processFormDelete($rawData)
    {
        if (array_key_exists('submit_ok',$rawData)) {
            $this->getModel()->getNode()->delete();
            return true;
        }
        return false;
    }

     /**
     * Processing movenment of category
     */
    public function processFormMove($sourceId, $destinationId, $position)
    {
        if ($sourceId != 0) {
            $sourceCategory = $this->getCategoryById($sourceId);
            $destinationCategory = $this->getCategoryById($destinationId);
            $sourceCategory->getNode()->moveAsLastChildOf($destinationCategory);
            $siblings = $sourceCategory->getNode()->getSiblings();
            $i = 0;
            foreach ($siblings as $sibling) {
                if ($i == $position) {
                    $sourceCategory->getNode()->moveAsPrevSiblingOf($sibling);
                    break;
                }
                $i++;
            }
            return true;
        }
        return false;
    }

     /**
     * Get categories array for cascade select
     *
     * @param string $categories
     * @return array
     */
    public function getCategoriesArray($categories, $level, $selected)
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        // формируем массив для каскадных селектов
        $result = array();
        $list = array();
        $result[] = array('id' => '',
                          'text' => $view->translate('Выберите...'));
        if ($categories) {
            foreach ($categories as $entity) {
                if ($entity->id == $selected) {
                    continue;
                }
                $result[] = array('id' => $entity->id,
                                  'text' => $entity->title);
            }
        }
        $list['item'] = $result;

        $list['count'] = count($result);
        $list['level'] = $level;

        return $list;
    }

     /**
     * Get selected categories array for cascade select
     *
     * @param boolean $selectedToDeleteFlag
     * @return array
     */
    public function getSelectedCategoriesArray($selectedToDeleteFlag = true, $lastLevel = false, $category = NULL)
    {
         // выбор всех предков
         if ($category) {
            $ancestors = $category->getNode()->getAncestors();
         } else{
            $ancestors = $this->getModel()->getNode()->getAncestors();
            $category = $this->getModel();
         }
         $selectedPath = array();
         $i = 0; $parent = 0;
         // формирование массива для загрузки каскдных селектов
         if ($ancestors){
             foreach ($ancestors as $ancestor) {
                 if ($i == 0) {
                     $i++;
                     continue;
                 }
                 $selectedPath[] = array('id'=> $parent,
                                         'level' => $i,
                                         'selected' => $ancestor->id,
                                         'selectedToDelete' => 0);
                 $parent = $ancestor->id;
                 $i++;
             }
             $selectedPath[] = array('id'=> $parent,
                                     'level' => $i,
                                     'selected' => $category->id,
                                     'selectedToDelete' => $selectedToDeleteFlag == true ? $this->getModel()->id : 0);
             if ($lastLevel) {
                 $selectedPath[] = array('id'=> $category->id,
                                     'level' => $i + 1,
                                     'selected' => 0,
                                     'selectedToDelete' => 0);
             }
         } else {
              $selectedPath[] = array('id'=> 0,
                                 'level' => 1,
                                 'selected' => 0,
                                 'selectedToDelete' => 0);
         }
        return $selectedPath;
    }

     /**
     * Get children categories of current category
     *
     * @return mixed
     */
    public function getCategoryCildren($id, $alias, $limit = NUll)
    {
        $this->findCategoryByIdOrAlias($id, $alias);
        if ($limit) {
            return $this->getModel()->getChildren($limit);
        } else {
            return $this->getModel()->getNode()->getChildren();
        }
    }

    /**
     * transform array to Javascript array
     *
     * @param array $array
     */
    public function transformArrayToJavascriptArray($array = NULL)
    {
        if ($array) {
            $jsArray = json_encode($array);
            $script = 'var dataArray = ' . $jsArray . ';';
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->headScript()->appendScript($script);
        }
    }

    /**
     * Get category by id
     *
     * @param string $id
     * @return Categories_Model_Category
     */
    public function getCategoryById($id)
    {
        if ($id != 0) {
            $category =$this->getMapper()->findOneById($id);
        } else {
            $category =$this->getMapper()->findOneByLevel(0);
        }

        if (!$category) {
            throw new Exception($this->getView()->translate('Category not founds.'));
        }

        return $category;
    }

    /**
     * find category by id and set model object for service layer
     *
     * @param integer $id
     * @return Categories_Model_CategoryService
     */
    public function findCategoryById($id)
    {
        $this->setModel($this->getCategoryByid($id));
        return $this;
    }



     /**
     * Get category by request id or alias param
     *
     * @return Categories_Model_Category
     */
    public function findCategoryByIdOrAlias($id, $alias)
    {
        if($alias) {
            $category = $this->getMapper()->findOneByAlias($alias);
        } elseif (isset($id)) {
            $id = (int)$id;
            if ($id != 0) {
                $category = $this->getMapper()->findOneById($id);
            } else {
                $category = $this->getMapper()->findOneByLevel(0);
            }
        }

        if ($category) {
            $this->setModel($category);
        }
        return $this;
    }


}