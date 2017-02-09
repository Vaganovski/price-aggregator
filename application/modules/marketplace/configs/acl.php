<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:marketplace:index'));
$acl->add(new Zend_Acl_Resource('mvc:marketplace:products'));
$acl->add(new Zend_Acl_Resource('mvc:marketplace:categories'));
$acl->add(new Zend_Acl_Resource('mvc:marketplace:search'));

$acl->add(new Zend_Acl_Resource('object:marketplace:product:my'));
$acl->add(new Zend_Acl_Resource('object:marketplace:product:foreign'));

/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:marketplace:index');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:marketplace:search');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:marketplace:products', array( 'view', 'get-description'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'mvc:marketplace:products', array('list', 'new', 'edit', 'delete'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'object:marketplace:product:my', array('edit', 'delete'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:marketplace:products', array('list-admin', 'approve'));
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:marketplace:categories', array('list', 'view', 'list-json'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:marketplace:categories', array('list-admin', 'new', 'edit', 'delete', 'tree', 'move'));



return $acl;