<?php
/**
 * ezIGBT Admin index file
 * This file load the admin panal
 */

/* include files */
require_once '../config.php';
require_once EZ_BASE_PATH . 'includes/EZ_DB.php';
require_once EZ_ADMIN_PATH . 'includes/admin-functions.php';

/* Global variables */
global $EZ_DB;

// Die if there are database errors
if ( $EZ_DB->error['error'] )
    die($EZ_DB->error['msg']);

if( !empty($_GET['page']) && $_GET['page'] == 'cms' ){
    require_once EZ_ADMIN_PATH . 'includes/manage-cms.php';// Only include for cms module
}

// Check if it is first run or dashboard or login request
if ( $EZ_DB->admin_exist() ) {

    if ( get_current_admin_id() ) {

        if( !empty( $_SESSION['capability'] ) && $_SESSION['capability'] == 'admin' )
            include 'view/panel.php';
        else{

            $home = return_site_url();
            header("Location:".$home);
            die(1);

        }

    } else {

        include 'view/login.php';

    }

}else {

    include EZ_ADMIN_PATH . 'view/setup.php';

}