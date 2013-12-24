<?php
/*
 * Dashboard content
 */

if (!defined('EZ_BASE_PATH'))
    die('No access!'); //exit if access directly!

//Get header
admin_header('Admin Panel');

$error = '';
$msg = ''; ?>

<div id="wrapper">

    <div class="clearfix admin-panel">
        <h1 class="admin-title"><a href="<?php echo return_site_url().'admin' ?>">ezIGBT Admin Panel</a></h1><?php

        $class = '';
        if ($error)
            $class = 'error';

        if (!empty($msg)) { ?>
            <div class='msg <?php echo $class; ?>'><?php echo $msg; ?></div><?php
        } ?>

        <div id="admin-sidebar">

            <ul class="admin-options">
                <li><a href="./index.php?page=import-models">To upload new models</a></li>
                <li><a href="./index.php?page=download-emails&format=csv">Download all emails (CSV)</a></li>
                <li><a href="./index.php?page=download-emails&format=xls">Download all emails (XLS)</a></li>
            </ul>

        </div>

        <div id="admin-content">
            <?php load_admin_template(); ?>
            <div class="loading-div">
                <img class="ajax-loader" src="<?php echo VIT_IMG.'/loader.gif' ?>" alt="ajax-loader" />
            </div>
        </div>

    </div>
</div><?php

//Get footer
admin_footer();