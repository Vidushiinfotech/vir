<?php
/**
 * ezIGBT init file
 * initialize and create objects
 */

global $Router, $EZ_DB;

/* Include the classes */
require './config.php';
require_once EZ_BASE_PATH . 'includes/EZ_DB.php';
require_once EZ_BASE_PATH . 'includes/Router.php';

/* Create instance */
$EZ_DB = new EZ_DB();
$Router = new Router();

//Include function file
require_once EZ_BASE_PATH . 'includes/global-functions.php';
