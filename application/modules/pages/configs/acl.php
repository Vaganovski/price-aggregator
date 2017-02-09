<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:pages:index'));

/**
 * Set permissions
 */

$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:pages:index', array('view', 'prices', 'sitemap'));
$acl->allow(Users_Model_User::ROLE_ADMINISTRATOR, 'mvc:pages:index', array('edit', 'delete','new', 'list'));

return $acl;