<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:reviews:index'));


/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:reviews:index');



return $acl;