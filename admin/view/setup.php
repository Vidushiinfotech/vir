<?php
/**
 * ezIGBT Setup file
 * Setup user on first time run
 */
if (!defined('EZ_BASE_PATH')) die('No access!'); //exit if access directly!

// Get header
admin_header('First Time Setup', 'admin-setup-header'); 

// process form
$msg = '';
$error = false;
if (isset($_POST['submit'])) {

    $fields = array(

        'username' => 'Username',
        'email' => 'Email',
        'pwd' => 'Password',
        'pwd2' => 'Confirm Password',
        'site-title' => 'Site title'
    );
    
    foreach ($fields as $key => $val) {
        if (!isset($_POST[$key]) || empty($_POST[$key])) {
            $error = true;
            $msg .= "$val can not be empty.<br />";
        }
    }
    
    //extra validation
    if (!$error) {
        if(!preg_match('/^[a-zA-Z0-9_]{4,}$/', $_POST['username'])) { 
            $error = true;
            $msg .= 'Username can only be alphanumeric with/without underscores and minimum length of four characters.<br />';
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $error = true;
            $msg .= 'Please enter a valid email.<br />';
        }

        if ($_POST['pwd'] !== $_POST['pwd2']) {
            $error = true;
            $msg .= 'Please enter the same password in both password fields.<br />';
        }
        
        if (!$error) {
            $username = mysqli_real_escape_string($EZ_DB->connect, $_POST['username']);
            $result = $EZ_DB->run_query("SELECT * FROM users WHERE username ='".$username."'");
            if ($result) {
                $error = true;
                $msg .= 'This username already exist.<br />';
            }
        }
        
        if (!$error) {
            $email = mysqli_real_escape_string($EZ_DB->connect, $_POST['email']);
            $result = $EZ_DB->run_query("SELECT * FROM users WHERE user_email ='".$email."'");
            if ($result) {
                $error = true;
                $msg .= 'This email address already exist.<br />';
            }
        }

        if (!$error) {

            $username = mysqli_real_escape_string( $EZ_DB->connect, $_POST['username'] );
            $email = mysqli_real_escape_string($EZ_DB->connect, $_POST['email']);
            $password = md5(mysqli_real_escape_string( $EZ_DB->connect, $_POST['pwd']) );
            $site_title = strip_tags(mysqli_real_escape_string($EZ_DB->connect, $_POST['site-title']));

            //Compute url
            $temp_url = "http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
            $temp_url2 = str_replace('index.php', '', $temp_url);
            $site_url = (str_replace('admin/', '', $temp_url2));
            //db("INSERT INTO users VALUES ( '', '".$username."', '". $password ."', '', '', '".$email."', '1'  )");
            $result = $EZ_DB->run_query("INSERT INTO users VALUES ( '', '".$username."', '". $password ."', '', '', '".$email."', '1', '0'  )");
            $result2 = $EZ_DB->run_query("INSERT INTO config VALUES ( 'site_title', '".$site_title."')");
            $result3 = $EZ_DB->run_query("INSERT INTO config VALUES ( 'site_url', '".$site_url."')");

            if (!$result || !$result2 || !$result3) {
                $error = true;
                $msg .= 'Error!<br />';
            }
        }
    }

    if (!$error) {
        $msg .= 'Setup Complete! You will be redirect to login page or refresh this page manually.<br />'; ?>
        <script type="text/javascript">
            window.setTimeout('window.location = window.location + "?rl=" + new Date().getTime();',3000);
        </script><?php
    }

} ?>

        <div class='container'><?php

            $class = '';
            if ($error)
                $class = 'error';

            if (!empty($msg)) { ?>
                <div class='msg <?php echo $class; ?>'><?php echo $msg; ?></div><?php
            } ?>

            <form action="./" method="POST">
                <p><label for='username'>User Name</label><br />
                <input required="required" id='username' class='text' type='text' value='<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>' name='username' /></p>

                <p><label for='email'>Email</label><br />
                <input required="required" id='email' class='text' type='email' value='<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>' name='email' /></p>

                <p><label for='pwd'>Password</label><br />
                <input required="required" id='pwd' class='text' type='password' value='<?php echo isset($_POST['pwd']) ? $_POST['pwd'] : ''; ?>' name='pwd' /></p>

                <p><label for='pwd2'>Confirm Password</label><br />
                <input required="required" id='pwd2' class='text' type='password' value='<?php echo isset($_POST['pwd2']) ? $_POST['pwd2'] : ''; ?>' name='pwd2' /></p>

                <p><label for='site-title'>Site Title</label><br />
                <input required="required" id='site-title' class='text' type='text' value='<?php echo isset($_POST['site-title']) ? strip_tags($_POST['site-title']) : ''; ?>' name='site-title' /></p>

                <p><input type='submit' value='Submit' name='submit' /></p>
            </form>
        </div>

    </body>
</html>