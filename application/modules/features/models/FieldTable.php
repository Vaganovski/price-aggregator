<?php
/**
 */
class Features_Model_FieldTable extends Doctrine_Table
{
    /**
     * Удаление всех полей хар-к которые не включаются в массив $existsFields и
     * привязка к id набора
     *
     * @param array $existsFields
     * @param integer $setId
     * @return Doctrine_Collection
     */
    public function deleteFields($existsFields, $setId)
    {
        return $this->createQuery('f')
                    ->delete()
                    ->where('f.features_set_id = ?', $setId)
                    ->andWhereNotIn('f.id', $existsFields)
                    ->execute();
    }
}