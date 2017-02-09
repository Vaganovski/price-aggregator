<?php

/**
 * Category_Model_Entity
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Categories_Model_Category extends Categories_Model_Base_Category
{
    /**
     * возвращает категории следующего уровня с ограничением по выводу
     *
     * @param integer $limit
     * @return Doctrine_Collection
     */
    public function getChildren($limit)
    {
        $baseAlias = $this->getTable()->getTree()->getBaseAlias();
        $query = $this->getTable()->getTree()->getBaseQuery();
        $params = array($this->lft, $this->rgt);

        $query->addWhere("$baseAlias.lft > ? AND $baseAlias.rgt < ?", $params)->addOrderBy("$baseAlias.lft asc");
        $query->addWhere("$baseAlias.level <= ?", $this->level + 1);
        $query->limit($limit);
        $query = $this->getTable()->getTree()->returnQueryWithRootId($query, $this->getNode()->getRootValue());
        $result = $query->execute();

        if (count($result) <= 0) {
            return false;
        }

        return $result;
    }
}