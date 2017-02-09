<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:default:error'));
$acl->add(new Zend_Acl_Resource('mvc:default:index'));
$acl->add(new Zend_Acl_Resource('mvc:default:settings'));

/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:default:index', array('index'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:default:index', array('admin'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:default:settings', array('index'));
$acl->allow(Users_Model_User::ROLE_GUEST, array('mvc:default:error'));

return $acl;