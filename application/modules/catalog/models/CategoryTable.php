<?php
/**
 */
class Catalog_Model_CategoryTable extends Categories_Model_CategoryTable
{


    /**
     * Кеш (ключ - заговолок, значение - ID)
     *
     * @var array
     */
    protected $_idsByTitlesCache = array();

    /**
     * Find all categories of products with keywords param
     *
     * @param $productIds
     * @return Doctrine_Collection
     */
    public function findAllByProductIds($productIds)
    {
        if (!empty($productIds)) {
            $query = $this->createQuery('c')
                ->select('c.*, COUNT(p.id) as products_count')
                ->leftJoin('c.CategoryProduct cp')
                ->leftJoin('cp.Product p')
                ->leftJoin('p.Brand b')
                ->whereIn("p.id", $productIds)
                ->groupBy('c.id');
            return $query->execute();
        } else {
            return new Doctrine_Collection($this);
        }
    }
    
    /**
     * Find all categories categories ids
     *
     * @return array
     */
    public function findAllByCategoriesIds($categories)
    {
        $query = $this->createQuery()
            ->whereIn("id", $categories);
        return $query->fetchArray();
    }
    
    /**
     * Идентификатор по заголовку
     * 
     * @param string
     * @return int|null
     */
    public function getIdByTitle($title)
    {
        // Если нет в кеше - достаем из БД и добавляем
        if (!isset($this->_idsByTitlesCache[$title])) {
            $category = $this->createQuery()
                ->select('id')
                ->where("title = ?", $title)
                ->orWhere('FIND_IN_SET(?,short_description) > 0', $title)
                ->fetchArray();
            // Если такая запись нет - возвращаем null
            if (!empty ($category)) {
                $this->_idsByTitlesCache[$title] = $category[0]['id'];
            } else {
                return null;
            }
        }
        return $this->_idsByTitlesCache[$title];
    }
}