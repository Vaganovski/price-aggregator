<?php
/**
 */
class Catalog_Model_PriceTable extends Doctrine_Table
{

    /**
     * Find max price by product id
     *
     * @param $productId
     * @return array|null
     */
    public function findPriceByProductId($productId)
    {
        $query = $this->createQuery('pr')
            ->select('MAX(pr.price), MIN(pr.price), AVG(pr.price)')
            ->leftJoin('pr.Shop s')
            ->addWhere('pr.product_id = ?', $productId)
            ->addWhere('s.status = ?', 'available')
            ->groupBy('pr.product_id');
        $result = $query->fetchArray();
        if (isset($result[0])) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * Find products
     *
     * @param $shopId
     * @return array|null
     */
    public function findProductsIdsByShopId($shopId)
    {
        $query = $this->createQuery('pr')
            ->select('pr.product_id')
            ->addWhere('pr.shop_id = ?', $shopId)
            ->groupBy('pr.product_id');
        $result = array();
        foreach ($query->fetchArray() as $value) {
            $result[$value['product_id']] = $value['product_id'];
        }
        return $result;
    }

    /**
     * Find max, min, avg price
     *
     * @return Doctrine_Record
     */
    public function findMaxMinAvgPrice()
    {
        $query = $this->createQuery('pr')
            ->select('MAX(pr.price), MIN(pr.price), AVG(pr.price), pr.product_id')
            ->leftJoin('pr.Product p')
            ->leftJoin('pr.Shop s')
            ->andWhere('s.status = ?', 'available')
            ->groupBy('p.id');
        return $query->execute();
    }

    /**
     * Find max, min, avg price
     *
     * @param $products
     * @return Doctrine_Record
     */
    public function findMaxMinAvgPriceByProducts($products)
    {
        $query = $this->createQuery('pr')
            ->select('MAX(pr.price), MIN(pr.price), AVG(pr.price), pr.product_id')
            ->leftJoin('pr.Shop s')
            ->addWhere('s.status = ?', 'available')
            ->whereIn('pr.product_id', $products)
            ->groupBy('pr.product_id');
        $result = array();
        foreach ($query->fetchArray() as $value) {
            $result[$value['product_id']] = $value;
        }
        return $result;
    }

    /**
     * Find prices by product id and with ordering
     *
     * @param null $productId
     * @param null $shopId
     * @param null $orderBy
     * @param string $sortDirection
     * @return Doctrine_Record
     */
    public function findAllByProductId($productId = NULL, $shopId = NULL, $orderBy = NULL, $sortDirection = 'ASC')
    {
        $query = $this->createQuery('pr')
            ->leftJoin('pr.Product p')
            ->leftJoin('pr.Shop s')
            ->where('p.id = ?', $productId)
            ->andWhere('s.status = ?', 'available');
        if ($shopId) {
            $query->andWhere('s.id = ?', $shopId);
        }
        if ($orderBy == 'price') {
            $query->orderBy('pr.price ' . $sortDirection);
        } elseif ($orderBy == 'price-available'){
            $query->orderBy('pr.price ' . $sortDirection)
                  ->orderBy('pr.available ' . $sortDirection);
        }
        return $query->execute();
    }


    /**
     * Find prices by shop id
     *
     * @param $shopId
     * @param bool $asQuery
     * @return Doctrine_Record
     */
    public function findAllByShopId($shopId, $asQuery = false)
    {
        $query = $this->createQuery('pr')
            ->leftJoin('pr.Shop s')
            ->leftJoin('pr.Product p')
            ->addWhere('s.id = ?', $shopId)
            ->addWhere('s.status = ?', 'available')
            ->groupBy('pr.product_id');
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
    }

    /**
     * Find prices by shop id
     *
     * @param $shopId
     * @param bool $asQuery
     * @return Doctrine_Record
     */
    public function findAllRemovedByShopId($shopId, $asQuery = false)
    {
        $query = $this->createQuery('pr')
            ->addWhere('pr.shop_id = ?', $shopId)
            ->addWhere('pr.exist = ?', false);
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
    }

    /**
     * @param $shopId
     * @return int
     */
    public function updateExistFlagByShopId($shopId)
    {
        $query = $this->createQuery('pr')
            ->update()
            ->set('pr.exist', '?', false)
            ->addWhere('pr.shop_id = ?', $shopId);
        return $query->execute();
    }

     /**
     * Count all
     *
     * @return integer
     */
    public function countAll()
    {
        $query = $this->createQuery('pr')
                ->select('COUNT(*) as count')
                ->leftJoin('pr.Shop s')
                ->where('s.status = ?', 'available');
        $result = $query->fetchArray();
        return (int)$result[0]['count'];
    }

    /**
     * Count all
     *
     * @param $productId
     * @return integer
     */
    public function countAllByProductId($productId)
    {
        $query = $this->createQuery('pr')
                ->select('COUNT(DISTINCT(s.id)) as count')
                ->leftJoin('pr.Product p')
                ->leftJoin('pr.Shop s')
                ->where('p.id = ?', $productId)
                ->andWhere('s.status = ?', 'available');
        $result = $query->fetchArray();
        return (int)$result[0]['count'];
    }

    /**
     * Удаление товаров продавца из прайса
     *
     * @param array $productsId
     * @param integer $shopId
     * @return Doctrine_Collection
     */
    public function deletePrices($productsId, $shopId)
    {
        return $this->createQuery('pr')
                    ->delete()
                    ->where('pr.shop_id = ?', $shopId)
                    ->andWhereIn('pr.product_id', $productsId)
                    ->execute();
    }

//
//
//    /**
//     * Find min price by product id
//     *
//     * @return Doctrine_Record
//     */
//    public function findMinPriceByProductId($productId)
//    {
//        $query = $this->createQuery('pr')
//            ->select('MIN(pr.price)')
//            ->leftJoin('pr.Product p')
//            ->where('p.id = ?', $productId)
//            ->groupBy('p.id');
//        return $query->execute();
//    }
}