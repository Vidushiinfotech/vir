<?php
/**
 * ezIGBT Admin index file
 * This file load the admin panal
 */

/* Global variables */
global $EZ_DB;

/* include files */
require_once '../config.php';
require_once EZ_BASE_PATH . '/includes/EZ_DB.php';

/* Create instance */
$EZ_DB = new EZ_DB();

// Die if there are database errors
if ($EZ_DB->error['error'])
    die($EZ_DB->error['msg']);

// Check if it is first run
if ($EZ_DB->admin_exist()) {
    include './login.php';
} else {
    include EZ_ADMIN_PATH . '/view/setup.php';
}