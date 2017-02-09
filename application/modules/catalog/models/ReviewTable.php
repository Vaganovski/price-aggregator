<?php
/**
 */
class Catalog_Model_ReviewTable extends Reviews_Model_ReviewTable
{
    /**
     * Find all reviews for product
     *
     * @return Doctrine_Collection
     */
    public function findAllProductsReviews($productId, $limit = NULL, $asQuery = false)
    {
        $query = $this->createQuery()
            ->addWhere('product_id = ?', $productId)
            ->addWhere('approve = ?', 2)
            ->orderBy('created_at DESC');
        if ($limit) {
            $query->limit($limit);
        }
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
        
    }

    public function findAll($approve, $asQuery = false)
    {
        $query = $this->createQuery()
            ->orderBy('created_at DESC');
        if ($approve !== NULL) {
            $query->where('approve = ?', $approve);
        }
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }

    }

    public function countAllByProductId($productId = NULL)
    {
        $query = $this->createQuery()
                      ->select('COUNT(*) as count');
        if ($productId) {
            $query->addWhere('product_id = ?', $productId)
                ->addWhere('approve = ?', 2);
        }
        $query = $query->fetchArray();
        return (int)$query[0]['count'];
    }
}