<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Creating resources
 */
$acl->add(new Zend_Acl_Resource('mvc:feedback:index'));

/**
 * Set permissions
 */

$acl->allow(Users_Model_User::ROLE_GUEST, 'mvc:feedback:index', array('new', 'get-find-error-form', 'find-error'));

return $acl;