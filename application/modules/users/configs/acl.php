<?php

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:users:index'));
$acl->add(new Zend_Acl_Resource('mvc:users:mailer'));
$acl->add(new Zend_Acl_Resource('mvc:users:profile'));
$acl->add(new Zend_Acl_Resource('mvc:users:list'));

$acl->add(new Zend_Acl_Resource('object:users:user:my'));
$acl->add(new Zend_Acl_Resource('object:users:user:foreign'));


$acl->add(new Zend_Acl_Resource('mvc:users:comments'));
$acl->add(new Zend_Acl_Resource('mvc:users:object:my-comment'));
$acl->add(new Zend_Acl_Resource('mvc:users:object:foreign-comment'));
/**
 * Set permissions
 */
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:users:index', array('login', 'registration', 'password-restore', 'password-reset', 'profile', 'activation'));
$acl->deny(Users_Model_User::ROLE_MEMBER, 'mvc:users:index', array('login', 'registration', 'password-restore'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'mvc:users:index', array('logout', 'profile-edit', 'password-change', 'activation'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:users:index', array('list', 'delete', 'password-change-admin', 'view', 'create-user', 'edit'));

$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:users:list');//!!!!!!!!!!
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:users:mailer');
$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:users:profile');

$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:users:comments',  array('list'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'mvc:users:comments',  array('new'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'mvc:users:object:my-comment', array('delete'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:users:comments', array('delete', 'edit'));

$acl->allow(Users_Model_User::ROLE_MEMBER, 'object:users:user:my', array('view-email'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'object:users:user:my', array('edit'));
$acl->allow(Users_Model_User::ROLE_MEMBER, 'object:users:user:foreign', array('new-comment'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'object:users:user:foreign', array('edit', 'delete', 'view-email'));


return $acl;