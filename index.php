<?php
/**
 * ezIGBT index file
 * This file load the whole site
 */

/* Load all connection and other files */
ini_set('display_errors', 1);
require_once 'includes/init.php';

//Dispaly header
get_site_header();

//Display template
$Router->loader();

//Display footer
get_site_footer();