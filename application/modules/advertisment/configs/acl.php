<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:advertisment:index'));


/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:advertisment:index');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:advertisment:index', array('go'));



return $acl;