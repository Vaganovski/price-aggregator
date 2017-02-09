<?php
/**
 */
class Advertisment_Model_BannerTable extends Doctrine_Table
{

    public function findAll($hydrationMode = null)
    {
        return $this->createQuery()->orderBy('id DESC')
            ->execute(array(), $hydrationMode);
    }

    /**
     * Find one banner for show
     *
     * @param $showOn
     * @param string $type
     * @param null $page
     * @return Doctrine_Collection
     */
    public function findOneByShowOn($showOn, $type, $page = NULL)
    {
        $query = $this->createQuery();
        if ($showOn == 'on-main') {
            $query->where("show_on = ? OR show_on = ?", array('on-main', 'anywhere'));
        } else {
            $query->where("show_on = ?", $showOn);
        }
        if ($showOn == 'in-place' && $page) {
            $query->andWhere("page_placement = ?", $page);
        }
        $query->andWhere('type = ?', $type)
              ->andWhere('status = ?', 'available')
              ->orderBy('updated_at ASC');
        return $query->fetchOne();
    }

    /**
     * Find all banner which must be deleted
     *
     * @return Doctrine_Collection
     */
    public function findAllMustBeDisabled()
    {

        return $this->createQuery()
                    ->where('untill_date <= NOW()')
                    ->execute();
    }
}