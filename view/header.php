<?php
/*
 * Header file
 */
global $EZ_DB;
$analytics  =   "SELECT key_value from config where key_name='analytics'";
$analytics  =   $EZ_DB->run_query($analytics);
$analytics  =   !empty($analytics) ? ($analytics['key_value']) : '';

if( !session_id() )
    session_start(); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=1024" />

        <title><?php echo get_page_title() ? get_page_title() . " - " : ''; echo get_site_title();  ?></title>

        <link rel="shortcut icon" href="<?php echo VIT_IMG.'/favicon.ico'; ?>" />
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <link href="<?php echo return_site_url(); ?>assets/css/style_old.css" rel="stylesheet" type="text/css"  /><!-- Remove after development -->
        <link href="<?php echo return_site_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/jquery-1.10.2.js"></script><!-- Remove after development -->
        <script type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/canvas2image.js"></script>
        <script type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/base64.js"></script>
        <script type="text/javascript" src="http://html2canvas.hertzen.com/build/html2canvas.js"></script>
        <!-- Scripts in development mode -->
        <!--[if lt IE 9]>
            <script src="<?php echo return_site_url().'admin/js/html5.js' ?>"></script>
        <![endif]--><?php

        if ( is_graph_page() ){ ?>

            <!-- Style sheet for selectbox -->
            <link href="<?php echo return_site_url(); ?>admin/external-libs/fancyselect/chosen.css" rel="stylesheet" type="text/css" />

            <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/excanvas.min.js"></script><![endif]-->
            <script type="text/javascript" src="<?php echo return_site_url(); ?>admin/external-libs/flot/jquery.flot.js"></script> 
            <script type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/graph-custom-script.js"></script>
            <script type="text/javascript" src="<?php echo return_site_url(); ?>admin/external-libs/fancyselect/chosen.jquery.min.js"></script> 
            <script type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/action-buttons.js"></script><?php 

        } ?>

        <script type="text/javascript" src="<?php echo return_site_url(); ?>assets/js/custom-script.js"></script>

        <script type="text/javascript">
            var ajaxurl = '<?php echo return_site_url().'admin/ajax.php'; ?>';
            var graph_ajaxurl = '<?php echo return_site_url().'admin/graph-ajax.php'; ?>';
            var savePDF = '<?php echo return_site_url().'additional/testSave.php'; ?>';
            var loader  = '<?php echo VIT_IMG.'/loader.gif' ?>';
        </script>

        <!-- Google Analytics -->
        <?php echo $analytics ?>

    </head>
    <body class="<?php echo get_body_class(); ?>">
        <div id="ez-ajax-loader"><span></span></div>
        <div class="container clearfix"><!-- Container Wrapper --> <!-- End in footer -->
            
            <header id="header-wrapper" class="clearfix"><!-- Header -->
                <div class="header-content clearfix">
                    <h1 class="logo">
                        <a title="<?php echo get_site_title(); ?>" href="<?php echo return_site_url(); ?>"><img alt="<?php echo get_site_title(); ?>" src="<?php echo return_site_url(); ?>assets/img/logo.png" width="150" height="48" /></a>
                    </h1>
                    <nav class="main-nav alignleft">
                        <ul class="clearfix">
                            <li class="<?php echo is_current_page('analyze'); ?>"><a href="<?php echo return_site_url() . 'analyze'; ?>">Analyze</a></li>
                            <li class="<?php echo is_current_page('compare'); ?>"><a href="<?php echo return_site_url() . 'compare'; ?>">Compare</a></li>
                            <li class="<?php echo is_current_page('recommend'); ?>"><a href="<?php echo return_site_url() . 'recommend'; ?>">Recommend</a></li>
                        </ul>
                    </nav>
                    <div class="top-right alignright">
                        <ul class="topmenu clearfix"><?php

                            if( empty( $_SESSION['user_id'] ) ){ ?>
                                <li class="login-btn <?php echo is_current_page('login'); ?>"><a href="<?php echo return_site_url() . 'login.php?action=login'; ?>">Login</a></li>
                                <li class="sign-up-btn <?php echo is_current_page('signup'); ?>"><a href="<?php echo return_site_url() . 'login.php?action=signup'; ?>">Signup</a></li><?php
                            }else{ ?>
                                <li class="logout-btn <?php echo is_current_page('login'); ?>"><a href="<?php echo return_site_url() . 'login.php?action=logout'; ?>">Logout</a></li><?php
                            } ?>
                        </ul>
                    </div>
                </div>
            </header><!-- End Header -->
            
            <div id="content-wrapper"><!-- Content Wrapper --> <!-- End in footer -->
