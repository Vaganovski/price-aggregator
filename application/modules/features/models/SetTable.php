<?php
/**
 */
class Features_Model_SetTable extends Doctrine_Table
{
    /**
     * Удаление всех наборов хар-к которые не включаются в массив $existsSets и
     * привязка к id группы
     *
     * @param array $existsSets
     * @param integer $groupId
     * @return Doctrine_Collection
     */
    public function deleteSets($existsSets, $groupId)
    {
        return $this->createQuery('s')
                    ->delete()
                    ->where('s.features_group_id = ?', $groupId)
                    ->andWhereNotIn('s.id', $existsSets)
                    ->execute();
    }
}