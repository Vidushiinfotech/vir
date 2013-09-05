<?php
/**
 * Base configurations file.
 * 
 */

/* set error reporting */
error_reporting(E_ALL);
//error_reporting(0);

/* define the site path */
define ('EZ_BASE_PATH', realpath(dirname(__FILE__)));

/* define the site path */
define ('EZ_ADMIN_PATH', realpath(dirname(__FILE__)) . '/admin');