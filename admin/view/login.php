<?php
/**
 * ezIGBT Display login page
 */
if ( !defined('EZ_BASE_PATH') ) die('No access!'); //exit if access directly!

ob_start();
admin_header("Login");

global $EZ_DB;

// process form
$msg = '';
$error = false;
if (isset($_POST['submit'])) {

    $fields = array(
        'username' => 'Username',
        'password' => 'Password',
    );

    foreach ($fields as $key => $val) {
        if (!isset($_POST[$key]) || empty($_POST[$key])) {
            $error = true;
            $msg .= "$val can not be empty.<br />";
        }
    }

    //extra validation
    if (!$error) {

        $username = mysqli_real_escape_string( $EZ_DB->connect, $_POST['username'] );
        $password = md5( mysqli_real_escape_string( $EZ_DB->connect, $_POST['password'] ) );
        $result = $EZ_DB->run_query("SELECT * FROM users WHERE username ='".$username."' AND password = '". $password ."'");

        if ($result) {

            if( !session_id() )
                session_start();

            if (isset($result['ID'])) {

                $_SESSION['user_id'] = $result['ID'];
                $_SESSION['capability'] = 'admin';
                
                $admin = return_site_url().'admin';

                header("Location:$admin");
                header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
                header('Pragma: no-cache'); // HTTP 1.0.
                header('Expires: 0'); // Proxies.
                die(1);

            }
        } else {

            $error = true;
            $msg .= 'Username or Password is incorrect!<br />';

        }
    }

} ?>

        <div id="wrapper">
            <div class="login-form"><?php
                $class = '';
                if ($error)
                    $class = 'error';

                if (!empty($msg)) { ?>
                    <div class='msg <?php echo $class; ?>'><?php echo $msg; ?></div><?php
                } ?>
                <form action="./" method="POST">
                    <p><label for="username">Username</label><br />
                        <input type="text" value="" required="required" name="username" id="username" />
                    </p>
                    <p><label for="password">Password</label><br />
                        <input type="password" value="" required="required" name="password" id="password" />
                    </p>
                    <p><input type="submit" value="Login" name="submit" /></p>
                </form>
            </div>
        </div>
    </body>
</html>
<?php echo ob_get_clean(); ?>