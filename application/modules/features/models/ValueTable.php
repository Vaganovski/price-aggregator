<?php
/**
 */
class Features_Model_ValueTable extends Doctrine_Table
{
    /**
     * Удаление всех значений полей которые не включаются в массив $existsValues и
     * привязка к id поля
     *
     * @param array $existsValues
     * @param integer $fieldId
     * @return Doctrine_Collection
     */
    public function deleteValues($existsValues, $fieldId)
    {
        return $this->createQuery('f')
                    ->delete()
                    ->where('f.features_field_id = ?', $fieldId)
                    ->andWhereNotIn('f.id', $existsValues)
                    ->execute();
    }


    public function findMinAndMaxByFieldId($fieldId)
    {
        return $this->createQuery('f')
                    ->select('MAX(f.title) as max, MIN(f.title) as min')
                    ->where('f.features_field_id = ?', $fieldId)
                    ->groupBy('f.features_field_id')
                    ->execute();
    }

}