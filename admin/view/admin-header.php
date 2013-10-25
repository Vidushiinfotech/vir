<?php
/*
 * Admin header tamplate
 */
if (!defined('EZ_BASE_PATH')) die('No access!'); //exit if access directly! ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo get_site_title(); echo !empty($page_title) ? ' - ' . $page_title : ''; ?></title>
        <link rel="icon" href="<?php echo return_site_url(); ?>images/favicon.ico" type="image/x-icon"/>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <link href="<?php echo return_site_url(); ?>admin/css/admin-css.css" rel="stylesheet" type="text/css" />
        <!-- Add All JS files here -->
        <script type="text/javascript">
            var active  = "<?php echo VIT_IMG.'/active.png' ?>";
            var inactive= "<?php echo VIT_IMG.'/inactive.png' ?>";
            var showimg = "<?php echo VIT_IMG.'/show.png' ?>";
            var hideimg = "<?php echo VIT_IMG.'/hide.png' ?>";
            var siteurl = '<?php echo return_site_url() ?>';
            var loader  = '<?php echo VIT_IMG.'/loader.gif' ?>';
            var ajaxurl = '<?php echo return_site_url()."admin/ajax.php" ?>';
        </script>

        <script type="text/javascript" src="<?php echo return_site_url().'admin/js/jQuery.js' ?>"></script>
        <script type="text/javascript" src="<?php echo return_site_url().'admin/external-libs/tinymce/tinymce.min.js' ?>"></script>
        <script type="text/javascript" src="<?php echo return_site_url().'admin/js/vit-admin-script.js' ?>"></script>

        <!--[if lt IE 9]>
            <script src="<?php echo return_site_url().'admin/js/html5.js' ?>"></script>
        <![endif]-->

    </head>

    <body class="<?php echo get_body_class() ?>">

        <header id="admin-header" class="<?php echo ( !get_current_admin_id() ) ? 'not-login ' : '' ?>clearfix">

            <hgroup class="<?php echo ( !get_current_admin_id() ) ? 'not-login ' : 'logged-in' ?>">

                <h1 class="logo"><a href="<?php echo return_site_url() ?>">
                    <img alt="<?php echo get_site_title(); ?>" src="<?php echo VIT_IMG ?>/logo.png" width="150" height="48" border="0" /></a>
                </h1>

            </hgroup><?php
            if ( get_current_admin_id() ) { ?>
                <div class="right-links">
                    <a class="button" href="./index.php?page=cms" title="Manage CMS">Manage CMS</a>
                    <a class="button" href="./logout.php" title="Logout">Logout</a>
                </div><?php
            } ?>
        </header>