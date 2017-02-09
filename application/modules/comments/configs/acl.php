<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:comments:index'));
$acl->add(new Zend_Acl_Resource('object:comments:comment:my'));
$acl->add(new Zend_Acl_Resource('object:comments:comment:foreign'));

/**
 * Set permissionsz
 */
$acl->allow(Users_Model_User::ROLE_GUEST, array('mvc:comments:index'));


return $acl;