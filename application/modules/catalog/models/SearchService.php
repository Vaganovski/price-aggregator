<?php

class Catalog_Model_SearchService extends ZFEngine_Model_Service_Abstract
{

    protected $_searchIndexPath;    //путь для папки с файлами поискового индекса
    protected $_name = 'ru_words';  //имя таблицы в БД, для которой создаем поисковой индекс

    public function init()
    {
            $this->_searchIndexPath = APPLICATION_PATH . '/../tmp/search-index';

            Zend_Search_Lucene_Analysis_Analyzer::setDefault(
                    new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());

    }


    /**
     * Создает новый поисковой индекс
     */
    public function updateIndex()
    {
        // Удаляем существующий индекс,
        // в большинстве случае эта операция с последующим созданием нового индекса работает гораздо быстрее
        $this->recursive_remove_directory($this->_searchIndexPath, TRUE);

        try {
            if (!is_dir($this->_searchIndexPath)) {
                mkdir($this->_searchIndexPath, 0777);
                chmod ($this->_searchIndexPath, 0777);
            }
            $index = Zend_Search_Lucene::create($this->_searchIndexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            echo "Не удалось создать поисковой индекс: {$e->getMessage()}";
            // @todo или отсюда по другому нужно выходить?
            return;
        }

        try {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
            
            // Выбираем все поля для индексации
            // Характеристики
            $features = Catalog_Model_FeatureProductTable::getInstance()->findAllForSearchIndexAsArray();
            // Описание товара
            $products = Catalog_Model_ProductTable::getInstance()->findAllForSearchIndexAsArray();
            $i = 0;
            foreach ($products as $product) {
                $doc = new Zend_Search_Lucene_Document();
                $name = ($product['brand'] == 'unknown')
                       ? $product['name']
                       : $product['brand'] . ' ' . $product['name'];
                $doc->addField(Zend_Search_Lucene_Field::keyword('product_id', $product['id']));
                $doc->addField(Zend_Search_Lucene_Field::unStored('name', $name, 'UTF-8'));
                $doc->addField(Zend_Search_Lucene_Field::unStored('category', $product['category'], 'UTF-8'));
                if (strlen($product['keywords'])) {
                    $doc->addField(Zend_Search_Lucene_Field::unStored('keywords', $product['keywords'], 'UTF-8'));
                }
                if (strlen($product['description'])) {
                    $doc->addField(Zend_Search_Lucene_Field::unStored('description', $product['description'], 'UTF-8'));
                }

                if (isset($features[$product['id']])) {
                    foreach ($features[$product['id']] as $featureValue) {
                        if (strlen($featureValue['value'])) {
                            $doc->addField(Zend_Search_Lucene_Field::unStored(
                                'feature-' . $featureValue['id'],
                                $featureValue['value'],
                                'UTF-8'
                            ));
                        }
                    }
                }

                $index->addDocument($doc);
                $i++;
            }
            $index->optimize();
            unset($index);
            chmod ($this->_searchIndexPath, 0777);
        } catch (Zend_Search_Lucene_Exception $e) {
            echo "Ошибки индексации: {$e->getMessage()}";
        }
    }

    /**
     * recursive_remove_directory( directory to delete, empty )
     * expects path to directory and optional TRUE / FALSE to empty
     *
     * @param $directory
     * @param $empty TRUE - just empty directory
     */
    private function recursive_remove_directory($directory, $empty=FALSE)
    {
        if(substr($directory,-1) == '/') {
            $directory = substr($directory,0,-1);
        }
        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(is_readable($directory)) {
            $handle = opendir($directory);
            while (false !== ($item = readdir($handle))) {
                if($item != '.' && $item != '..') {
                    $path = $directory.'/'.$item;
                    if(is_dir($path)) {
                        $this->recursive_remove_directory($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($handle);
            if($empty == false) {
                if(!rmdir($directory)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Search by query
     *
     * @param $query search query
     * @return array Zend_Search_Lucene_Search_QueryHit
     */
    public function search($query)
    {
        try {
            $index = Zend_Search_Lucene::open($this->_searchIndexPath);
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
        } catch (Zend_Search_Lucene_Exception $e) {
            echo "Ошибка:{$e->getMessage()}";
        }
		
		Zend_Search_Lucene_Search_QueryParser::setDefaultOperator(1);
        $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($query, 'UTF-8');

        return $index->find($userQuery);
    }



}
