<?php
/**
 * Base configurations file.
 * 
 */

/* set error reporting */
ini_set('display_errors', 1);
error_reporting(E_ALL);
//error_reporting(0);

/* Decide Which Kind of slash to be use */
$os = PHP_OS;
$os = strtoupper($os);
$os = '~'.$os; // Add a tilt so that strpos should not return offset 0 ;)

if( strpos( $os, 'WIN' ) )
    $slashes = '\\';
else
    $slashes = '/';

define('EZ_SLASHES', $slashes );

/* define the site path */
define ('EZ_BASE_PATH', realpath(dirname(__FILE__)) . EZ_SLASHES );

/* define the site path */
define ('EZ_ADMIN_PATH', realpath(dirname(__FILE__)) . EZ_SLASHES.'admin'.EZ_SLASHES);

/* database name */
define( 'EZ_DB_HOST', 'localhost' );

/* database name */
define( 'EZ_DB_NAME', 'ezigbt' );

/* database user */
define( 'EZ_DB_USER', 'ezigbtProdUser' );

/* Database Password */
define( 'EZ_DB_PASS', 'Goezigbtgo!' );
