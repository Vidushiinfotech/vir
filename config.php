<?php
/**
 * Base configurations file.
 * 
 */

/* set error reporting */

define('EZ_SLASHES', '/' );

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
