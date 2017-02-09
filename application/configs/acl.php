<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

$acl = new Zend_Acl();

/**
 * Creating roles
 */
$acl->addRole(new Zend_Acl_Role(Users_Model_User::ROLE_GUEST))
    ->addRole(new Zend_Acl_Role(Users_Model_User::ROLE_MEMBER), Users_Model_User::ROLE_GUEST)
    ->addRole(new Zend_Acl_Role(Users_Model_User::ROLE_MERCHANT), Users_Model_User::ROLE_MEMBER)
    ->addRole(new Zend_Acl_Role(Users_Model_User::ROLE_MANAGER), Users_Model_User::ROLE_MEMBER)
    ->addRole(new Zend_Acl_Role(Users_Model_User::ROLE_ADMINISTRATOR), Users_Model_User::ROLE_MERCHANT);

return $acl;
