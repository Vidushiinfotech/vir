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
define( 'EZ_SMTP_PORT', 587 );

/* SMTP HOST */
define( 'EZ_SMTP_HOST', "smtp.live.com" );

/* SMTP user */
define( 'EZ_SMTP_USER', 'support@ezigbt.com' );

/* SMTP Password */
define( 'EZ_SMTP_PASS', 'Goigbtgo!' );

/* SMTP Reply To */
define( 'EZ_SMTP_REPLY', 'support@ezigbt.com' ); // If user will reply, to whom it should go ?

/* SMTP Reply Name */
define( 'EZ_SMTP_REPLY_NAME', 'EzIGBT' );

/* SMTP Reply FROM */
// From which address email is getting sent, On same address you will receive all emails
define( 'EZ_SMTP_FROM', 'support@ezigbt.com' );

/* SMTP Reply FROM Name */
define( 'EZ_SMTP_REPLY_FROM_NAME', 'EzIGBT' );
