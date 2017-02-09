<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:shops:index'));
$acl->add(new Zend_Acl_Resource('mvc:shops:manager'));
$acl->add(new Zend_Acl_Resource('mvc:shops:comments'));
$acl->add(new Zend_Acl_Resource('mvc:shops:chain-shop'));

$acl->add(new Zend_Acl_Resource('object:shops:shop:my'));
$acl->add(new Zend_Acl_Resource('object:shops:shop:foreign'));
$acl->add(new Zend_Acl_Resource('object:shops:manager:my'));
$acl->add(new Zend_Acl_Resource('object:shops:manager:foreign'));

/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:shops:index', array('view', 'index', 'chain-view'));
$acl->allow(Users_Model_User::ROLE_MERCHANT, 'mvc:shops:index', array('new', 'edit', 'profile', 'edit-image', 'disable-and-delete'));
$acl->allow(Users_Model_User::ROLE_MERCHANT, 'mvc:shops:chain-shop', array('new', 'edit', 'list'));
$acl->allow(Users_Model_User::ROLE_MANAGER, 'mvc:shops:index', array('edit', 'profile', 'edit-image'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:shops:index');

$acl->allow(Users_Model_User::ROLE_MERCHANT, 'mvc:shops:manager', array('new', 'edit'));

$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:shops:comments', array('index'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'mvc:shops:comments', array('new', 'reply', 'my', 'edit'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:shops:comments', array('delete', 'list-admin', 'edit-admin'));

$acl->allow(Users_Model_User::ROLE_MEMBER, 'object:shops:shop:foreign', array('new-comment'));
$acl->allow(Users_Model_User::ROLE_MERCHANT, 'object:shops:shop:my', array('reply-comment'));

$acl->allow(Users_Model_User::ROLE_MERCHANT, 'object:shops:shop:my', array('edit', 'delete', 'view-profile'));

$acl->allow(Users_Model_User::ROLE_MANAGER, 'object:shops:shop:my', array('edit', 'view-profile'));

$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'object:shops:shop:foreign', array('edit', 'delete', 'view-profile'));

$acl->allow(Users_Model_User::ROLE_MEMBER, 'object:shops:manager:my', array('edit'));



return $acl;