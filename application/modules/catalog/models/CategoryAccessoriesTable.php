<?php
/**
 */
class Catalog_Model_CategoryAccessoriesTable extends Doctrine_Table
{
    /**
     * Find all accessories
     *
     * @return Doctrine_Collection
     */
    public function findAllByCategoryId($categoryId)
    {
        $query = $this->createQuery()
            ->where("category_id = ?", $categoryId);
        return $query->execute();
    }
}