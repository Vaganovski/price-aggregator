<?php
/**
 */
class Pages_Model_PageTable extends Doctrine_Table
{
    /**
     * Find all pages as query
     *
     * @return Doctrine_Query
     */
    public function findAllAsQuery()
    {
        return $this->createQuery()
            ->from('Pages_Model_Page');
    }
}