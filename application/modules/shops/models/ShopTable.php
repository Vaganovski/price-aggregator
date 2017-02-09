<?php
/**
 */
class Shops_Model_ShopTable extends Shops_Model_ShopBaseTable
{

    /**
     * Find all by status as query
     *
     * @return Doctrine_Query
     */
    public function findAllByStatusAsQuery($status, $orderBy = 'created_at', $sortOrder = 'DESC', $keywords = null, $price = null)
    {
        $query = $this->createQuery()
            ->addWhere('status = :status', array(':status'=>$status))
            ->orderBy($orderBy . ' ' . $sortOrder);

        if (!is_null($keywords) && strlen($keywords)) {
            $query->addWhere('name LIKE :keywords OR address LIKE :keywords OR email LIKE :keywords', array(':keywords' => '%' . $keywords . '%'));
        }
        
        if (!is_null($price)) {
            switch ($price) {
                case 'absent':
                    $query->addWhere('price_status = :price', array(':price' => Shops_Model_Shop::PRICE_STATUS_ABSENT));
                    break;
                case 'uploaded':
                    $query->addWhere("price_status IN ('" . Shops_Model_Shop::PRICE_STATUS_QUEUE . "', '" . Shops_Model_Shop::PRICE_STATUS_PROCESSED . "')");
                    break;
                default:
                    break;
            }
        }
        return $query;
    }
    
    /**
     * Find all by status as query
     *
     * @return Doctrine_Query
     */
    public function findAllByUserAsQuery($userId)
    {
        $query = $this->createQuery()
            ->addWhere('user_id = ?', $userId);
        return $query;
    }

    /**
     * Find all order by name
     *
     * @return Doctrine_Collection
     */
    public function findAllAvailableOrderByName($city, $withComment = false, $limit = NULL, $withImage = false)
    {
        $query = $this->createQuery('s')
            ->where("s.status = 'available'")
            ->orderBy('s.chain_name ')
            ->groupBy('s.chain_name, s.chain_shop_id');
                
        if ($withComment == 'true') {
            $query->leftJoin('s.ShopComments c')
                  ->andWhere('c.entity_id IS NOT NULL')
                  ->groupBy('s.id');
        }
        if ($city) {
            $query->andWhere('s.city = ?', $city);
        }
        if ($limit) {
            $query->limit($limit);
        }
        if ($withImage) {
            $query->andWhere("s.image_filename <> ''");
        }
        return $query->execute();
    }

    /**
     * Find all by status as query
     *
     * @param $status
     * @param $limit
     * @return Doctrine_Collection
     */
    public function getAllByStatus($status, $limit = null)
    {
        $query = $this->createQuery()
            ->where('status = ?', $status);
        if ($limit != null) {
            $query->limit($limit);
        }
        return $query->execute();
    }

     /**
     * Find by product ids array
     *
     * @return Doctrine_Collection
     */
    public function countAll($withComment = false)
    {
        if ($withComment) {
            $query = $this->createQuery()
                     ->select('COUNT(DISTINCT s.id) as count')
                     ->from('Shops_Model_Comment sc')
                     ->innerJoin('sc.CommentedShop s')
                     ->where("s.status = 'available'");
        } else {
            $query = $this->createQuery('s')
                ->select('COUNT(s.id) as count')
                ->where("s.status = 'available'");
        }
        $query->groupBy('s.chain_shop_id');
        $result = $query->fetchArray();
        $count = (count($result) > 0) ? $result[0]['count'] + (count($result)-1) : 0 ;
        return $count;
    }

    /**
     * Find all shops which must be deleted
     *
     * @return Doctrine_Collection
     */
    public function findAllMustBeDisabled()
    {

        return $this->createQuery()
                    ->where('untill_date <= NOW()')
                    ->andWhere('status <> ?', 'disable')
                    ->execute();
    }


    /**
     * Find prices by product id and with ordering
     *
     * @return Doctrine_Record
     */
    public function findAllWithPricesByProductId($productId, $orderBy = NULL, $sortDirection = 'ASC')
    {
        $query = $this->createQuery('s')
            ->select('*, MAX(pr.price) as max_price, MIN(pr.price) as min_price')
            ->leftJoin('s.Prices pr')
            ->leftJoin('pr.Product p')
            ->where('p.id = ?', $productId)
            ->andWhere('s.status = ?', 'available');
        if ($orderBy == 'price') {
            $query->orderBy('pr.price ' . $sortDirection);
        } elseif ($orderBy == 'price-available'){
            $query->orderBy('pr.price ' . $sortDirection)
                  ->orderBy('pr.available ' . $sortDirection);
        }
        $query->groupBy('s.chain_name, s.chain_shop_id');
        return $query->execute();
    }
}