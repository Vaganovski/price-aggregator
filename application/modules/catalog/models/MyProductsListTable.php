<?php
/**
 */
class Catalog_Model_MyProductsListTable extends Doctrine_Table
{
    /**
     * Find max price by product id
     *
     * @return Doctrine_Record
     */
    public function findExistingProductsIds($userId)
    {
        $query = $this->createQuery()
            ->select('product_id')
            ->where('user_id = ?', $userId);
        return $query->execute();
    }

    /**
     * Find all products from users's products list
     *
     * @return Doctrine_Query
     */
    public function findProductsFromList($userId)
    {
        $query = $this->createQuery()
            ->from('Catalog_Model_Product p')
            ->innerJoin('p.ProductsList pl')
            ->where('pl.user_id = ?', $userId);
        return $query;
    }

    /**
     * Find all products from users's products list
     *
     * @return Doctrine_Query
     */
    public function countProductsFromList($userId)
    {
        $query = $this->createQuery()
            ->select('COUNT(*) as count')
            ->from('Catalog_Model_Product p')
            ->innerJoin('p.ProductsList pl')
            ->where('pl.user_id = ?', $userId)
            ->fetchArray();
        return $query[0]['count'];
    }

    /**
     * Delete by product id and user id param
     *
     * @return Doctrine_Query
     */
    public function deleteByProductIdAndUserId($productId, $userId)
    {
        $query = $this->createQuery()
            ->delete()
            ->where('user_id = ?', $userId)
            ->andWhere('product_id = ?', $productId);
        return $query->execute();
    }
}