<?php
/**
 */
class Catalog_Model_FeatureProductTable extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Catalog_Model_FeatureProductTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Catalog_Model_FeatureProduct');
    }

    /**
     * Удаление всех значений хар-к которые связаны с товаром
     *
     * @param integer $product_id
     * @return Doctrine_Collection
     */
    public function deleteAllByProductId($product_id)
    {
        return $this->createQuery()
                    ->delete()
                    ->where('product_id = ?', $product_id)
                    ->execute();
    }
    
    /**
     * @param integer $product_id
     * @return array
     */
    public function findAllForSearchIndexAsArray()
    {

        $query = $this->createQuery('f')
                    ->select('f.id id, f.product_id product_id, v.title value')
                    ->leftJoin('f.Value v');
        $result = array();
        foreach ($query->fetchArray() as $value) {
echo "123";
            $result[$value['product_id']][] = $value;
        }
        return $result;
    }
}
