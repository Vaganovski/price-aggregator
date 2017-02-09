<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:catalog:index'));
$acl->add(new Zend_Acl_Resource('mvc:catalog:brands'));
$acl->add(new Zend_Acl_Resource('mvc:catalog:categories'));
$acl->add(new Zend_Acl_Resource('mvc:catalog:products'));
$acl->add(new Zend_Acl_Resource('mvc:catalog:reviews'));
$acl->add(new Zend_Acl_Resource('mvc:catalog:search'));


/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:catalog:index');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:catalog:search');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:catalog:brands');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:catalog:categories', array('view', 'list', 'list-json'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:catalog:categories', array('new', 'edit', 'delete', 'list-admin', 'tree', 'move'));
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:catalog:products', array('view', 'buy', 'get-prices', 'search-autocomplete', 'shop-list', 'add-to-compare', 'compare', 'add-to-my-list', 'delete-from-my-list','my-list'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:catalog:products', array('new', 'edit', 'delete', 'edit-features', 'upload-image', 'upload-main-image', 'delete-image', 'delete-main-image', 'popular-admin', 'new-admin', 'list-admin','visible-admin'));
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:catalog:reviews', array('view', 'list', 'approve', 'edit'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'mvc:catalog:reviews', array('new'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:catalog:reviews', array('delete', 'list-admin'));



return $acl;