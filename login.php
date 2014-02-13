<?php
/**
 * Handle Login and Signup functionality.
 */
session_start();
require_once './includes/init.php';

/* For logging the user out of the website */
if( !empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'logout' ){

    session_destroy();
    vit_redirect( return_site_url() );

}

if( !empty( $_SESSION['user_id'] ) ){

    vit_redirect( return_site_url() );
}

if( isset( $_POST['login_submit'] ) ):
        $response = vit_login();
    endif;
if( isset( $_POST['signup_submit'] ) ){
    $response = vit_signup();
    $_SESSION['security_number']=rand(10000,99999);
}else
    $_SESSION['security_number']=rand(10000,99999);

//Dispaly header
get_site_header(); ?>

<div class="login-wrapper clearfix"><?php

    if( empty( $_REQUEST['action'] ) || $_REQUEST['action'] == 'login' ){

        if( !empty( $_POST['signup_submit'] ) )
            echo '<div class="msg-div">'.$response.'</div>'; ?>

        <form method="post" action="">

            <h2 class="center"><?php echo 'Login'; ?></h2><?php

            if( !empty( $_POST['login_submit'] ) )
                echo '<div class="msg-div">'.$response.'</div>'; ?>

            <div class="field clearfix">
                <label for="loginmail" class="loginmail">Email</label>
                <input type="email" required="required" name="loginmail" id="loginmail" />
            </div>

            <div class="field clearfix cform-field">
                <input id="login_form" name="login_submit" type="submit" value="Submit">  
            </div>

        </form><?php

    }

    if( !empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'signup' ){ ?>

        <form class="signup-form" method="post" action="">

            <h2 class="center"><?php echo 'Sign Up for Updates'; ?></h2><?php

            if( !empty( $_POST['signup_submit'] ) )
                echo '<div class="msg-div">'.$response.'</div>'; ?>

            <div class="field clearfix">
                <div class="label-wrap"><label for="loginmail" class="loginmail">Email:</label></div>
                <div class="input-wrap"><input type="email" required="required" name="loginmail" id="loginmail" value="<?php !empty( $_POST['loginmail'] ) ? $_POST['loginmail'] : '' ?>" /></div>
            </div>

            <div class="field clearfix">

                <div class="label-wrap"><label class="lebel">My primary application is</label></div>
                <div class="input-wrap">
                    <select name="primary_application" id="primary-application">
                        <option value="Appliance Motor Drives">Appliance Motor Drives</option>
                        <option value="Industrial Motor Drives">Industrial Motor Drives</option>
                        <option value="UPS">UPS</option>
                        <option value="Solar Inverters">Solar Inverters</option>
                        <option value="General Purpose Inverter">General Purpose Inverter</option>
                        <option value="PFC">PFC</option>
                        <option value="Welding">Welding</option>
                        <option value="Power Supplies">Power Supplies</option>
                        <option value="HID">HID</option>
                        <option value="Other Application">Other Application</option>
                    </select>
                </div>

            </div>

            <div class="field clearfix">
                <div class="label-wrap"><label for="logincaptcha" class="logincaptcha">Please enter the word as shown below:</label></div>
                <div class="input-wrap"><input type="text" required="required" name="logincaptcha" id="logincaptcha" value="<?php !empty( $_POST['logincaptcha'] ) ? $_POST['logincaptcha'] : '' ?>" /></div>
            </div>

            <div class="field clearfix">
                <img class="captcha_img" title="Captcha Image" alt="Captcha Image" src="<?php echo return_site_url().'admin/external-libs/captchalib/image.php?pos='.rand() ?>" />
                <img class="captcha-fresh" src="<?php echo return_site_url().'admin/external-libs/captchalib/refresh.png'; ?>" />
            </div>

            <div class="field cform-field">
                <input id="signup_form" name="signup_submit" type="submit" value="Submit">
            </div>

        </form><?php

    } ?>

</div><?php

//Display footer
get_site_footer();