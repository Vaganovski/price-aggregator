<?php
/**
 */
class Users_Model_UserBaseTable extends Doctrine_Table
{

    /**
     * Все пользователи
     *
     * @return Doctrine_Query
     */
    public function findAllAsQuery()
    {
        return $this->createQuery()
            ->from('Users_Model_User');
    }

    
}