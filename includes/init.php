<?php
/**
 * ezIGBT init file
 * initialize and create objects
 */

global $Router;

/* Include the classes */
include EZ_BASE_PATH . '/includes/Router.php';

/* Create instance */
$Router = new Router();