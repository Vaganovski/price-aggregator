<?php
/**
 */
class Marketplace_Model_ProductTable extends Products_Model_ProductTable
{

    /**
     * Returns an instance of this class.
     *
     * @return Marketplace_Model_ProductTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Marketplace_Model_Product');
    }

    /**
     * @return array
     */
    public function findAllForSearchIndexAsArray()
    {
        $query = $this->createQuery('p')
                    ->select('p.id, p.name, p.description, c.title category, c.short_description keywords')
                    ->leftJoin('p.Categories c');
        return $query->fetchArray();
    }

    /**
     * Find all products with limit
     *
     * @return Doctrine_Collection
     */
    public function findAllByUserId($userId = 0, $limit = NULL, $asQuery = false)
    {
        $query = $this->createQuery()
            ->where('user_id = ?', $userId)
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

    public function findAllByApprove($approve = 0, $asQuery = false, $productIds = NULL, $type = NULL, $city = NULL, $categoryId = NULL)
    {
        $query = $this->createQuery('p')
            ->orderBy('p.created_at ASC');
        if ($approve != 0) {
            $query->addWhere('p.approve = ?', $approve);
        }
        if ($productIds) {
            $query->andWhereIn('p.id', $productIds);
        }
        if ($city) {
            $query->leftJoin('p.user u')
                  ->andWhere('u.city = ?', $city);
        }
        if ($type == 'buy' || $type == 'sell') {
            $query->andWhere('p.type = ?', $type);
        }
        if ($categoryId) {
            $query->leftJoin('p.CategoryProduct cp')
                  ->leftJoin('cp.Category c')
                  ->andWhere('c.id = ?', $categoryId);
        }
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
    }

    public function findAllByCategoryIdAndTypeAndCity($categoryId, $type, $city, $asQuery = false)
    {
        $query = $this->createQuery('p');
        if ($categoryId) {
            $query->leftJoin('p.CategoryProduct cp')
                  ->leftJoin('cp.Category c')
                  ->where('c.id = ?', $categoryId);
        }
        if ($type == 'buy' || $type == 'sell') {
            $query->andWhere('p.type = ?', $type);
        }
        if ($city) {
            $query->leftJoin('p.user u')
                  ->andWhere('u.city = ?', $city);
        }
        $query->addWhere('approve = ?', 2)
              ->orderBy('name ASC');

        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
    }

    public function countByUserId($userId = 0)
    {
        $query = $this->createQuery()
            ->select('COUNT(*) as count')
            ->where('user_id = ?', $userId)
            ->fetchArray();
        return $query[0]['count'];
    }

     /**
     * Count all by category id
     *
     * @return integer
     */
    public function countAllByCategoryIdAndCity($categoryLft = NULL, $categoryRgt = NULL, $city = NULL)
    {
        $query = $this->createQuery('p')
                ->select('COUNT(*) as count');
        if ($categoryLft && $categoryRgt) {
            $query->leftJoin('p.CategoryProduct cp')
                  ->leftJoin('cp.Category c')
                  ->where('c.lft >= ? AND c.rgt <= ?', array($categoryLft, $categoryRgt));
        }
        if ($city) {
           $query->leftJoin('p.user u')
                  ->andWhere('u.city = ?', $city);
        }
        $query->addWhere('approve = ?', 2);
        $result = $query->fetchArray();
        return (int)$result[0]['count'];
    }

    /**
     * Find all popular products with limit
     *
     * @return Doctrine_Collection
     */
    public function findAllNewWithLimit($limit, $visible = 0)
    {
        $query = $this->createQuery()
            ->orderBy('created_at DESC')
            ->limit($limit);
        if ($visible == -1) {
            $query->where('visible = ?', 0);
        } elseif ($visible == 1) {
            $query->where('visible = ?', 1);
        }
        return $query->execute();
    }
}