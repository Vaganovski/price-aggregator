<?php
/**
 * Defined APPLICATION_ENV, APPLICATION_PATH and change
 * include_path for CLI scripts
 */

if (file_exists('common.local.php')) {
    define('APPLICATION_ENV', 'development');

    require_once 'common.local.php';
} else {
    define('APPLICATION_ENV', 'production');
}

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

switch (APPLICATION_ENV) {
    case 'production':
        $_SERVER['HTTP_HOST'] = 'www.eprice.kz';
        break;
    case 'staging':
        $_SERVER['HTTP_HOST'] = 'eprice-kz.dev.stfalcon.com';
        break;
    case 'development':
        $_SERVER['HTTP_HOST'] = 'eprice.kz.work';
        break;
 }