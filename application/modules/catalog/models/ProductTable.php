<?php
/**
 */
class Catalog_Model_ProductTable extends Products_Model_ProductTable
{

    /**
     * Returns an instance of this class.
     *
     * @return Catalog_Model_ProductTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Catalog_Model_Product');
    }

    /**
     * @return array
     */
    public function findAllForSearchIndexAsArray()
    {

        $query = $this->createQuery('p')
                ->select('p.id, p.name, p.description, b.name brand, c.title category, c.short_description keywords')
                ->leftJoin('p.Brand b')
                ->leftJoin('p.Categories c');
        return $query->fetchArray();
    }


    /**
     * Find one product by full name and category id
     *
     * @param string $productName
     * @param int $categoryId
     * @param int $brandId
     *
     * @return Doctrine_Record
     */
    public function findOneByFullNameAndCategoryId($productName, $brandId, $categoryId)
    {
        $query = $this->createQuery('p')
                        ->leftJoin('p.CategoryProduct cp')
                        ->where('p.brand_id = ?', $brandId)
                        ->addWhere('p.name = ?', $productName)
                        ->addWhere('cp.category_id = ?', $categoryId);
        return $query->fetchOne();
    }


    /**
     * Find all products ids by name
     *
     * @param string $productName
     * @param int $brandId
     * @param array $exclude
     * @return array
     */
    public function findIdsToDelete($productName, $brandId, $exclude = array())
    {
        $query = $this->createQuery('p')
            ->select('p.id')
            ->addWhere('p.brand_id = ?', $brandId)
            ->addWhere('p.name = ?', $productName);
        if (!empty($exclude)) {
            $query->whereNotIn('p.id', $exclude);
        }
        $result = array();
        foreach ($query->fetchArray() as $item) {
            $result[] = $item['id'];
        }
    }

    /**
     * Картинки на загрузку
     *
     * @param int $limit
     * @return Doctrine_Collection
     */
    public function findMustBeDownloadImages($limit = 100)
    {
        $query = $this->createQuery('p')
                        ->where('p.image_to_download IS NOT NULL')
                        ->orderBy('created_at')
                        ->limit($limit);
        return $query->execute();
    }

    /**
     * Find all popular products with limit
     *
     * @param $limit
     * @param int $visible
     * @param bool $withSellers
     * @param null $brandId
     * @return Doctrine_Collection
     */
    public function findAllPopularWithLimit($limit, $visible = 0, $withSellers = true, $brandId = NULL)
    {
        $query = $this->createQuery('p')
            ->orderBy('p.clicks DESC')
            ->addWhere("p.image_filename <> ''")
            ->limit($limit);
        if ($visible == -1) {
            $query->addWhere('p.visible = ?', 0);
        } elseif ($visible == 1) {
            $query->addWhere('p.visible = ?', 1);
        }
        if ($withSellers) {
            $query->leftJoin('p.Prices pr')
                ->leftJoin('pr.Shop s')
                ->addWhere('pr.id IS NOT NULL')
                ->addWhere('s.status = ?', 'available');
        }
        if ($brandId) {
            $query->leftJoin('p.Brand b')
                  ->addWhere('b.id = ?', $brandId);
        }
        return $query->execute();
    }

    /**
     * Find all popular products with limit
     *
     * @param $limit
     * @param int $visible
     * @param bool $withSellers
     * @param null $brandId
     * @return Doctrine_Collection
     */
    public function findAllNewWithLimit($limit, $visible = 0, $withSellers = true, $brandId = NULL)
    {
        $query = $this->createQuery('p')
            ->orderBy('p.created_at DESC')
            ->addWhere("p.image_filename <> ''")
            ->limit($limit);
        if ($visible == -1) {
            $query->addWhere('p.visible = ?', 0);
        } elseif ($visible == 1) {
            $query->addWhere('p.visible = ?', 1);
        }
        if ($withSellers) {
            $query->leftJoin('p.Prices pr')
                ->leftJoin('pr.Shop s')
                ->addWhere('pr.id IS NOT NULL')
                ->addWhere('s.status = ?', 'available');
        }
        if ($brandId) {
            $query->leftJoin('p.Brand b')
                  ->addWhere('b.id = ?', $brandId);
        }
        return $query->execute();
    }

    protected function _addWhereProductsIdsAndCity($query, $productIds = NULL, $city = NULL, $categoryId = NULL) {
        if ($city) {
           $query->leftJoin('p.Prices pr')
                 ->leftJoin('pr.Shop s')
                 ->addWhere('s.city = ?', $city);
        }
        if ($productIds) {
           $query->andWhereIn('p.id', $productIds);
        }
        if ($categoryId) {
            $query->leftJoin('p.CategoryProduct cp')
                  ->addWhere('cp.category_id = ?', $categoryId);
        }
        return $query;
    }

    /**
     * Find all new products
     *
     * @param null $productIds
     * @param null $city
     * @param null $categoryId
     * @return Doctrine_Query
     */
    public function findAllNewAsQuery($productIds = NULL, $city = NULL, $categoryId = NULL)
    {
        $query = $this->createQuery('p')
            ->orderBy('p.created_at DESC');
        return $this->_addWhereProductsIdsAndCity($query, $productIds, $city, $categoryId);
    }

    /**
     * Find all popular products with limit
     *
     * @param null $productIds
     * @param null $city
     * @param null $categoryId
     * @return Doctrine_Query
     */
    public function findAllFilledAsQuery($productIds = NULL, $city = NULL, $categoryId = NULL)
    {
        $query = $this->createQuery('p')
            ->innerJoin('p.FeatureProduct fp')
            ->groupBy('p.id')
            ->orderBy('p.created_at DESC');

        return $this->_addWhereProductsIdsAndCity($query, $productIds, $city, $categoryId);
    }

    /**
     * Find all popular products with limit
     *
     * @param null $productIds
     * @param null $city
     * @param null $categoryId
     * @return Doctrine_Query
     */
    public function findAllNoFilledAsQuery($productIds = NULL, $city = NULL, $categoryId = NULL)
    {
        $query = $this->createQuery('p')
            ->leftJoin('p.FeatureProduct fp')
            ->where('fp.id IS NULL')
            ->groupBy('p.id')
            ->orderBy('p.created_at DESC');
        return $this->_addWhereProductsIdsAndCity($query, $productIds, $city, $categoryId);
    }

    /**
     * Find all similar products with limit
     *
     * @param $categoryId
     * @param $productId
     * @param $limit
     * @return Doctrine_Collection
     */
    public function findAllSimilarWithLimit($categoryId, $productId, $limit)
    {
        $query = $this->createQuery('p')
            ->leftJoin('p.CategoryProduct cp')
            ->leftJoin('cp.Category c')
            ->leftJoin('p.Prices pr')
            ->leftJoin('pr.Shop s')
            ->where('pr.id IS NOT NULL')
            ->andWhere('s.status = ?', 'available')
            ->andWhere('c.id = ?', $categoryId)
            ->andWhere('p.id <> ?', $productId)
            ->andWhere('p.visible = ?', 1)
            ->orderBy('p.created_at DESC')
            ->limit($limit);

        return $query->execute();
    }

    /**
     * Find max and min price
     *
     * @param $categoryIds
     *
     * @internal param int $categoryId
     * @return Doctrine_Query
     */
    public function findMinAndMaxPrices($categoryIds)
    {
        $query = $this->createQuery('p')
                ->select('MAX(p.avg_price) as max_price, MIN(p.avg_price) as min_price')
                ->leftJoin('p.CategoryProduct cp')
                ->leftJoin('cp.Category c')
                ->leftJoin('p.Prices pr')
                ->leftJoin('pr.Shop s')
                ->whereIn('c.id', $categoryIds)
                ->andWhere('pr.id IS NOT NULL')
                ->andWhere('s.status = ?', 'available');
        return $query->execute();
    }


    /**
     * Find all by categoryId as query
     *
     * @param null $categoriesIds
     * @param string $orderBy
     * @param string $sortDirection
     * @param null $city
     * @param null $keywords
     * @param null $brands
     * @param null $features
     * @param null $price_from
     * @param null $price_to
     * @param null $productIds
     * @param null $categories
     * @param null $featuresMinMax
     *
     * @internal param int $categoryId
     * @return Doctrine_Query
     */
    // @todo !!! вместо кучи параметров принимать ассоциативный массив
    public function findAllByParamsAsQuery($categoriesIds = NULL, $orderBy = NULL, $sortDirection = 'ASC', $city = NULL,
            $keywords = NULL, $brands = NULL, $features = NULL, $price_from = NULL, $price_to = NULL, 
            $productIds = NULL, $categories = NULL, $featuresMinMax = NULL)
    {
        $query = $this->createQuery('p')
                      ->leftJoin('p.Brand b')
                      ->leftJoin('p.Prices pr')
                      ->leftJoin('pr.Shop s')
                      ->where('pr.id IS NOT NULL')
                      ->andWhere('s.status = ?', 'available');
        if ($categoriesIds && count($categoriesIds) > 0) {
            $query->leftJoin('p.CategoryProduct cp')
                  ->leftJoin('cp.Category c')
                  ->whereIn('c.id', $categoriesIds);
        }

        // добавление сортировки к запросу
        switch ($orderBy) {
            case 'price':
                $query->orderBy('p.avg_price ' . $sortDirection);
                break;
            case 'popular':
                $query->orderBy('p.clicks ' . $sortDirection);
                break;
            case 'new':
                $query->orderBy('p.created_at ' . $sortDirection);
                break;
            default:
                $query->orderBy('p.name ' . $sortDirection);
                break;
        }

        // добавление фильтра по городу
        if ($city) {
           $query->addWhere('s.city = ?', $city);
        }

        // добавление фильтра по ключевым словам
        if ($keywords) {
           $keywords = '%' . $keywords . '%';
           $query->andWhere("CONCAT(b.name, ' ', p.name) LIKE ?", $keywords);
        }

        // добавление фильтра по брэндам
        if ($brands) {
           $query->andWhereIn('b.id', $brands);
        }

        // добавление фильтра по цене
        if ($price_from && $price_to) {
           $query->andWhere('p.avg_price >= ? AND p.avg_price <= ?', array($price_from, $price_to));
        }

        // добавление фильтра по характеристикам
        if ($featuresMinMax || $features) {
            $queryFeatures = $this->createQuery('product')
                                  ->select('product.id')
                                  ->leftJoin('product.FeatureProduct feature_product');
        }
        if ($featuresMinMax) {
            $queryTmp = $this->createQuery()
                     ->from('Features_Model_Value');
            foreach ($featuresMinMax as $key => $value) {
//                $features[] = $key;
                $queryTmp->andWhere('features_field_id = ? AND title >= ? AND title <= ?', array($key, $value['min'], $value['max']));
            }
            $featuresSliderValues = $queryTmp->execute();
            if (!$features) {
                $features = array();
            }
        }
        if ($features !== NULL) {
           $queryString = ''; $i = 1;
           $featuresValues = array();
           foreach ($features as $values) {
               if (empty ($values)) {
                   continue;
               }
               $params = array();
               foreach ($values as $value) {
                   $params[] = '?';
               }
               $queryString .= 'feature_product.features_value_id IN (' . implode(',', $params) . ')' . ($i == count($features) ? '' : ' OR ');
               $i++;
               $featuresValues = array_merge($featuresValues, $values);
           }
           if (!empty ($queryString)) {
                $queryFeatures->andWhere($queryString, $featuresValues);
           }
           $countFeatures = count($features);
           if (isset($featuresSliderValues)) {
               $queryString = ''; $sliderValues = array(); $i = 1;
               foreach ($featuresSliderValues as $featureValue) {
                   $queryString .= 'feature_product.features_value_id = ?' . ($i == $featuresSliderValues->count() ? '' : ' OR ');
                   $sliderValues[] = $featureValue->id;
                   $i++;
               }
               $queryFeatures->orWhere($queryString,  $sliderValues);
               $countFeatures += 1;
           }

           if ($countFeatures > 0) {
               $queryFeatures->groupBy('feature_product.product_id')
                     ->having('count(*) = ?', $countFeatures);
               if (isset($sliderValues) && count($sliderValues) > 0) {
                   $params = array_merge($featuresValues, $sliderValues, array($countFeatures));
               } else {
                   $params = array_merge($featuresValues, array($countFeatures));
               }
               $query->andWhere('p.id IN (' . $queryFeatures->getDql() . ')', $params);
           }
        }


        if ($productIds) {
            $query->whereIn('p.id', $productIds);
        }
        
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

        return $query;
    }

    /**
     * Find by product ids array
     * 
     * @param int $productId
     * @param boolean $asQuery
     * @return Doctrine_Collection|false
     */
    public function findByProductIds($productId, $asQuery = false)
    {
        if (empty($productId)) {
            return false;
        }
        $query = $this->createQuery('p')
            ->whereIn('p.id', $productId);
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
    }

    /**
     * Поиск по ключевому слову и городу
     *
     * @param string $keywords
     * @param string $city
     * @param boolean $withSellers
     * @param int|null $limit
     * @return Doctrine_Collection|Doctrine_Record
     */
    public function findAllByKeywordsAndCity($keywords, $city, $withSellers = true, $limit = null)
    {
        // Игнор бренда "unknown"
        $keywords = str_replace('unknown', '', $keywords);

        $keywords = '%' . $keywords . '%';
        $query = $this->createQuery('p')
                      ->leftJoin('p.Brand b')
                      ->where("CONCAT(b.name, ' ', p.name) LIKE ?", $keywords);
        if ($withSellers || $city) {
            $query->leftJoin('p.Prices pr')
                  ->leftJoin('pr.Shop s');
        }
        if ($withSellers) {
            $query->andWhere('pr.id IS NOT NULL')
                  ->andWhere('s.status = ?', 'available');
        }
        if ($city) {
            $query->andWhere('s.city = ?', $city);
        }
        if ($limit) {
            $query->limit($limit);
        }
        return $query->execute();
    }


    /**
     * Count all by category id
     *
     * @param null $categoryLft
     * @param null $categoryRgt
     * @return integer
     */
    public function countAllByCategoryId($categoryLft = NULL, $categoryRgt = NULL)
    {
        $query = $this->createQuery('p')
                ->select('COUNT(DISTINCT p.id) as count');
        if ($categoryLft && $categoryRgt) {
            $query->leftJoin('p.CategoryProduct cp')
                  ->leftJoin('cp.Category c')
                  ->where('c.lft >= ? AND c.rgt <= ?', array($categoryLft, $categoryRgt));
        }
        // Проверка, есть ли товар хотя бы в обном магазине
        $query->leftJoin('p.Prices pr')
            ->leftJoin('pr.Shop s')
            ->andWhere('s.status = ?', 'available');
        $result = $query->fetchArray();
        return (int)$result[0]['count'];
    }

    /**
     *
     * @param int $productId
     * @param array $newPrices
     * @return int
     */
    public function updatePriceByProductId($productId, $newPrices)
    {
        return $this->createQuery('p')
            ->update()
            ->set('p.min_price', '?', $newPrices['MIN'])
            ->set('p.max_price', '?', $newPrices['MAX'])
            ->set('p.avg_price', '?', $newPrices['AVG'])
            ->addWhere('p.id = ?', $productId)
            ->execute();
    }

}
