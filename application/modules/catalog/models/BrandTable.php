<?php
/**
 */
class Catalog_Model_BrandTable extends Doctrine_Table
{

    /**
     * Кеш (ключ - заговолок, значение - ID)
     *
     * @var array
     */
    protected $_idsByNamesCache = array();

    /**
     * Find all brands of products with keywords param
     *
     * @param $productIds
     * @return Doctrine_Collection
     */
    public function findAllByProductIds($productIds)
    {
        if (!empty($productIds)) {
            $query = $this->createQuery('b')
                ->leftJoin('b.Products p')
                ->whereIn("p.id", $productIds)
                ->groupBy('b.id');
        // Игнор бренда
        $query->addWhere('b.id != ?', Zend_Registry::get('settings')->unknown_brand);
            return $query->execute();
        } else {
            return new Doctrine_Collection($this);
        }
    }

    /**
     * Find all brands in category
     *
     * @param $categoryIds
     * @return Doctrine_Collection
     */
    public function findAllByCategoryId($categoryIds)
    {
        $query = $this->createQuery('b')
            ->select('b.*')
            ->leftJoin('b.Products p')
            ->leftJoin('p.CategoryProduct cp')
            ->leftJoin('p.Prices pr')
            ->leftJoin('pr.Shop s')
            ->whereIn('cp.category_id', $categoryIds)
            ->andWhere('pr.id IS NOT NULL')
            ->andWhere('s.status = ?', 'available')
            ->groupBy('b.id');

        // Игнор бренда
        $query->addWhere('b.id != ?', Zend_Registry::get('settings')->unknown_brand);
        
        return $query->execute();
    }

    /**
     * Find all brands in category
     *
     * @param $brandId
     * @return Doctrine_Collection
     */
    public function findCategoriesOfThisBrand($brandId)
    {
        $query = $this->createQuery()
            ->from('Catalog_Model_Category c')
            ->leftJoin('c.CategoryProduct cp')
            ->leftJoin('cp.Product p')
            ->leftJoin('p.Brand b')
            ->where('b.id = ?', $brandId)
            ->groupBy('c.id');
        return $query->execute();
    }
    /**
     * Find all order by name
     *
     * @return Doctrine_Collection
     */
    public function findAllOrderByName()
    {
        $query = $this->createQuery()
            ->orderBy('name');
        // Игнор бренда
        $query->addWhere('id != ?', Zend_Registry::get('settings')->unknown_brand);
        return $query->execute();
    }

     /**
     * Count all
     *
     * @return integer
     */
    public function countAll()
    {
        $query = $this->createQuery()
                ->select('COUNT(*) as count');
        // Игнор бренда
        $query->addWhere('id != ?', Zend_Registry::get('settings')->unknown_brand);
        $result = $query->fetchArray();
        return (int)$result[0]['count'];
    }

    /**
     * Идентификатор по заголовку
     *
     * @param $name
     *
     * @return int|null
     */
    public function getIdByName($name)
    {
        // Если нет в кеше - достаем из БД и добавляем
        if (!isset($this->_idsByNamesCache[$name])) {
            $brand = $this->createQuery()
                ->select('id')
                ->where("name = ?", $name)
                ->fetchArray();
            // Если такая запись нет - возвращаем null
            if (!empty ($brand)) {
                $this->_idsByNamesCache[$name] = $brand[0]['id'];
            } else {
                return null;
            }
        }
        return $this->_idsByNamesCache[$name];
    }
}