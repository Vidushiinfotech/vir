<?php
/*
 * Admin Setup header tamplate
 */
if (!defined('EZ_BASE_PATH')) die('No access!'); //exit if access directly!  ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $page_title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css" media="all">
            * { margin: 0; padding: 0; }
            body { font-family: sans-serif; font-size: 100%; line-height: 150%; color: #444; }
            .container { margin: 10px auto 0; padding: 20px; width: 700px; border: 1px solid #CCCCCC; }
            p { margin: 0 0 10px; }
            label { cursor: pointer; }
            input.text { border: 1px solid #CCCCCC; min-width: 220px; padding: 5px; }
            input[type="submit"] {margin-top: 10px; background: none repeat scroll 0 0 #EEEEEE;border: 1px solid #AAAAAA;cursor: pointer;padding: 5px 20px;}
            input[type="submit"]:hover, input[type="submit"]:active { background: #E2E2E2; }
            .msg { background: none repeat scroll 0 0 #999999; color: #FFFFFF; padding: 5px; margin-bottom: 10px; }
            .msg.error { background: #CC0000; }
            #admin-header { background: none repeat scroll 0 0 #000000; margin-bottom: 50px; }
            .logo { text-align: center; }
            .logo img { margin: 18px 0 17px; }
            .page-heading { text-align: center; }
        </style>
    </head>
    <body>
        <div id="admin-header">
            <h1 class="logo">
                <img src="<?php echo VIT_IMG ?>/logo.png" width="150" height="48" border="0" />
            </h1>
        </div>
        <h2 class="page-heading">Setup Site</h2>