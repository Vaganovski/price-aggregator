<?php
/**
 */
class Users_Model_UserTable extends Users_Model_UserExtendedTable
{

    /**
     * Returns an instance of this class.
     *
     * @return object Users_Model_UserTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Users_Model_User');
    }

   /**
     * get users from database which have role in $roles
     *
     *  @param  array   $roles
     *  @return Doctrine_Collection
     */
    public function getAllUsersAssociateWithRoles($roles)
    {
        $query = $this->createQuery()
                ->select('DISTINCT *')
                ->from('Users_Model_User u')
                ->whereIn('u.role', $roles);
        return $query->execute();
    }

    /**
     * get all roles from database
     *
     * @return  Doctrine_Collection
     */
    public function getAllRoles()
    {
        $query = $this->createQuery()
                        ->select('DISTINCT(role) role')
                        ->from('Users_Model_User u');
        return $query->execute();
    }

}