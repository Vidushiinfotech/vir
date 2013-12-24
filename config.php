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
define( 'EZ_DB_NAME', 'db_EZIGBT_com' );

/* database user */
define( 'EZ_DB_USER', 'db_EZIGBT_com' );

/* Database Password */
define( 'EZ_DB_PASS', 'DyfJjk4YzFl' );

/* SMTP PORT */
define( 'EZ_SMTP_PORT', 25 );

/* SMTP HOST */
define( 'EZ_SMTP_HOST', "192.168.0.11" );

/* SMTP user */
define( 'EZ_SMTP_USER', 'ankit.gade@vidushigoc.com' );

/* SMTP Password */
define( 'EZ_SMTP_PASS', 'p@ssword' );

/* SMTP Reply To */
define( 'EZ_SMTP_REPLY', 'ankit.gade@vidushigoc.com' ); // If user will reply, to whom it should go ?

/* SMTP Reply Name */
define( 'EZ_SMTP_REPLY_NAME', 'Ankit Gade' );

/* SMTP Reply FROM */
// From which address email is getting sent, On same address you will receive all emails
define( 'EZ_SMTP_FROM', 'ankit.gade@vidushigoc.com' );

/* SMTP Reply FROM Name */
define( 'EZ_SMTP_REPLY_FROM_NAME', 'Ankit Gade' );
