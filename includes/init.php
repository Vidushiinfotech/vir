<?php
/**
 * ezIGBT init file
 * initialize and create objects
 */

global $Router, $EZ_DB;

/* Include the classes */
include EZ_BASE_PATH . '/includes/EZ_DB.php';
include EZ_BASE_PATH . '/includes/Router.php';


/* Create instance */
$EZ_DB = new EZ_DB();
$Router = new Router();