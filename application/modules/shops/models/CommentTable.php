<?php
/**
 */
class Shops_Model_CommentTable extends Comments_Model_CommentTable
{
    public function findAll($type, $asQuery = true)
    {
        $query = $this->createQuery()
                      ->where('level = 1 OR level = 2');
        if ($type == 'new') {
            $query->andWhere('deleted_at IS NULL');
        } else {
            $query->andWhere('deleted_at IS NOT NULL');
        }

        $query->orderBy('created_at DESC');
        if ($asQuery) {
            return $query;
        } else {
            return $query->execute();
        }
    }

    public function countByShopId($shopId = NULL)
    {
        $query = $this->createQuery()
                      ->select('COUNT(*) as count');
        if ($shopId) {
            $query->where('entity_id = ?', $shopId)
                  ->andWhere('level = ?', 1)
                  ->andWhere('deleted_at IS NULL');
        }
        $query = $query->fetchArray();
        return (int)$query[0]['count'];
    }
}