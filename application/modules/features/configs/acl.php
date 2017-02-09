<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:features:index'));


/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:features:index');



return $acl;