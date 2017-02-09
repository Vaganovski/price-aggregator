<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:categories:index'));

/**
 * Set permissionsz
 */
$acl->allow(Users_Model_User::ROLE_GUEST, array('mvc:categories:index'));


return $acl;