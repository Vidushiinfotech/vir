<?php
/**
 * Commmon global functions
 *
 */

/* Define necessary Constants */
define( 'VIT_IMG', return_site_url().'assets/img' );

/**
 * Return the site url
 * @return mixed site url on success false on failure.
 */
function return_site_url() {

    global $EZ_DB;
    $result = $EZ_DB->run_query("SELECT key_value from config WHERE key_name='site_url'");
    if (isset($result['key_value'])) {
        return $result['key_value'];
    }
    
    return FALSE;
}

/**
 * Get current logged in admin id.
 * @return int admin id or false on failure. 
 */
function get_current_admin_id() {

    if (!session_id())
        session_start();

    if ( session_id() && isset($_SESSION['user_id']) && ((int) $_SESSION['user_id']) ) {
        return $_SESSION['user_id'];
    }
    return FALSE;
}

/**
 * Get site title
 * @return string Title of site.
 */
function get_site_title() {
    global $EZ_DB;
    $result = $EZ_DB->run_query("SELECT key_value from config WHERE key_name='site_title'");
    if (isset($result['key_value'])) {
        return $result['key_value'];
    }
    
    return "";
}

/**
 * Display site header
 * @param string $page_title Title of the page
 * @param string $header_name Name of the header file
 */
function get_site_header($page_title = '', $header_name = 'header') {

    $file_path = EZ_BASE_PATH . 'view/' . $header_name . '.php';
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        die('header file error!');
    }
}

/**
 * Display the footer of site
 * @param type $footer_name Name of the footer file
 */
function get_site_footer($footer_name = 'footer') {

    $file_path = EZ_BASE_PATH . 'view/' . $footer_name . '.php';
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        die('footer file error!');
    }
}

/**
 * Get page titles with template file name
 * @return array Titles of pages with filenames
 */
function get_pages_name_title() {
    return array (
        'home'      =>  '',
        'analyze'   =>  'Model a Part',
        'compare'   =>  'Compare Parts',
        'recommend' =>  'Get Recommendations',
        'login'     =>  'Login',
        'signup'    =>  'Signup',
        '404'       =>  '404',
    );
}

/**
 * Get the title of request page
 * @return string Title of the request page
 */
function get_page_title() {
    global $Router, $EZ_DB;
    $title = '';
        
    //Array of the file name and page titles
    $page_titles = get_pages_name_title();
    
    if (isset($Router->page_name) && !empty($Router->page_name)) {
        if (key_exists($Router->page_name, $page_titles)) {
            $title = $page_titles[$Router->page_name];
        } else if ($Router->is_cms_page($Router->page_name)) {
            $page_id = $Router->is_cms_page($Router->page_name);
            $result = $EZ_DB->run_query("SELECT title from pages WHERE ID='$page_id'");
            if (isset($result['title']))
                $title = $result['title'];
        } else {
            $title = 'Not Found';
        }
    }
    return $title;
}

/**
 * Add Browser and page class to body
 * @param array $class Custom array of class names
 * @return string string of classes
 */
function get_body_class( $class = array() ) {
    global $Router;
    $classes = array();

    if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']) {

        $browsers = array(
            'firefox',
            'msie',
            'opera',
            'chrome',
            'safari',
            'netscape',
            'aol',
            'iphone',
            'android'
        );

        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        foreach ($browsers as $value) {
            $match = strpos($user_agent, $value);
            if (false !== $match) {
                
                // Add ie version
                if ('msie' == $value) {
                    preg_match( '/msie ([0-9]+)([a-zA-Z0-9.]+)/', $user_agent, $browser_version );
                    $classes[] = 'ie'.$browser_version[1];
                }
                
                //Check if it is chrome as chrome content both safari and chrome
                if (('safari' == $value) && (strpos($user_agent, 'chrome') !== FALSE)) {
                    //Do not add safari class to chrome
                } else {
                    $classes[] = $value;
                }

            }
        }
    }

    //Add file name as class to body
    if ( isset($Router->page_name) ) {
        global $Router;
        if ( array_key_exists($Router->page_name, get_pages_name_title()) )
            $classes[] = $Router->page_name;
        else if ($Router->is_cms_page($Router->page_name)){

            $classes[] = 'cms-page '.$Router->page_name;
            
        }
        else
            $classes[] = 'error404';
    }

    //Add custom class
    if (is_array($class) && !empty($class)) {
        $classes = array_merge($classes, $class);
    }

    $class_str = implode(' ', $classes);
    return $class_str;
}

/**
 * echo the active class if it is current page
 * @param string $page_name Name of the page
 * @return string class name "active"
 */
function is_current_page($page_name) {
    global $Router;
    if (isset($Router->page_name) && $Router->page_name == $page_name)
        return 'active';
}

/**
 * Check if graph api is being used on perticular page
 * @return boolean true or false
 */
function is_graph_page(){
    global $Router;
    $page_names = array(
        'analyze',
        'compare'
    );

    if (isset($Router->page_name) && in_array($Router->page_name, $page_names))
        return TRUE;

    return false;
}

/**
 * Function to add subscriber to the database
 * @global object $EZ_DB database coonection object 
 * @return string response message
 */
function vit_add_subscribers(){

    global $EZ_DB;

    if( empty( $_POST['user_email'] ) ){// Check if email is empty

        return '<div class="error">Enter Email</div>';

    }elseif( !filter_var( $_POST['user_email'], FILTER_VALIDATE_EMAIL ) ){ // Check if email formatting is ok

        return '<div class="error">Enter Valid Email</div>';

    }else{// Check if that email is already present or not

        $email = mysqli_real_escape_string( $EZ_DB->connect, $_POST['user_email'] );

        $query = "SELECT * FROM users WHERE user_email='$email'";
        $result = $EZ_DB->run_query( $query );

        if( !empty( $result ) ){

            return '<div class="error">Email already exists</div>';

        }else{

            $uname = time();
            $psw   = md5( $uname );

            $query = "INSERT INTO users VALUES('', '$uname', '', '', '$psw', '$email', '0', '1' )";
            $result = $EZ_DB->run_query( $query, 1 );

            if( empty( $result ) ){

                return '<div class="error">Something went wrong, please try after some time</div>';

            }else{

                return '<div class="success">Successfully added to subscriber\'s list</div>';

            }

        }
        
    }
}

/**
 * For logging the user in
 */
function vit_login(){

    global $EZ_DB;

    if( !session_id() )
        session_start();

    if( empty( $_POST['loginmail'] ) ){// Check if email is empty

        return '<div class="error">Enter Email</div>';

    }elseif( !filter_var($_POST['loginmail'], FILTER_VALIDATE_EMAIL) ){ // Check if email formatting is ok

        return '<div class="error">Enter Valid Email</div>';

    }else{//Check if that email is present or not

        $email = mysqli_real_escape_string( $EZ_DB->connect, $_POST['loginmail']);

        $query = "SELECT * FROM users WHERE user_email='$email' AND is_admin='0'";
        $result = $EZ_DB->run_query( $query );

        if( !empty( $result ) ){// User is present. 

            /* Login process here */
            $_SESSION['user_id'] = $result['ID'];
            $_SESSION['capability'] = 'user';
            $homepage = return_site_url();
            vit_redirect( $homepage );

        }else{

            return '<div class="error">Email does not exists</div>';
        }
    }
}

/**
 * for signing up
 */
function vit_signup(){

    global $EZ_DB;

    $fname  =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['fname'] );
    $lname  =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['lname'] );
    $email  =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['loginmail'] );
    $psw    =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['psw'] );
    $uname  =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['uname'] );

    $error  =   '';

    if( empty( $fname ) )
        $error .= '<p class="error">Enter First Name</p>';
    elseif( !preg_match("/^[a-zA-Z'-]+$/",$fname) )
        $error .= '<p class="error">Enter Valid First Name</p>';

    if( empty( $lname ) )
        $error .= '<p class="error">Enter Last Name</p>';
    elseif( !preg_match("/^[a-zA-Z'-]+$/",$lname) )
        $error .= '<p class="error">Enter Valid Last Name</p>';

    if( empty( $uname ) )
        $error .= '<p class="error">Enter Username</p>';

    if( empty( $psw ) )
        $error .= '<p class="error">Password should not be blank</p>';

    if( empty( $email ) )
            $error .= '<p class="error">Enter Email Id</p>';
        elseif( !filter_var($email, FILTER_VALIDATE_EMAIL) )
            $error .= '<p class="error">Enter Valid Email</p>';

    /* Check if email already present */
    $query  = "SELECT ID FROM users WHERE user_email='$email'";
    $result = $EZ_DB->run_query( $query );
    if( !empty( $result ) )
        $error .= '<p class="error">Email already exists</p>';

    /* Check if username already exists */
    $query  = "SELECT ID FROM users WHERE username='$email'";
    $result = $EZ_DB->run_query( $query );
    if( !empty( $result ) )
        $error .= '<p class="error">Username already exists</p>';

    if( empty( $error ) ){

        $psw = md5($psw);

        $query = "INSERT INTO users VALUES ( '', '$uname', '$fname', '$lname', '$psw',  '$email', '0', '0' )";
        $res = $EZ_DB->run_query( $query );
        if( !empty( $res ) )
            $error .= '<p class="success">Signed up successfully, please login !</p>';
        else
            $error .= '<p class="error">Something went wrong, try again !</p>';
    }

    return $error;
}

/**
 * Function to redirect User
 */
function vit_redirect( $Location ){

    if( !headers_sent() ):
        header("Location:".$Location);
        die(1);
    endif;

}

/**
 * Function to get all models names which are visible in front end
 * @global DB Connection object $EZ_DB
 * @return array array of all models
 */
function vit_get_all_models(){

    global $EZ_DB;

    $query  = "SELECT * FROM models WHERE include_model='1'";
    $result = $EZ_DB->run_query( $query , 1 );
    $models = array();

    if( !empty( $result ) ){

        while( $row = mysqli_fetch_assoc($result) ){
            
            array_push( $models, $row['model_name'] );
        }

    }

    return $models;

}

/**
 * Function to render inputs
 * @param string $span_class Class of span
 * @param string $ip_name Input name
 * @param string $label Label
 * @param string $sup 
 * @param string $sub
 * @param string $content
 * @param string $inputClass Class of input
 * @param string $default_value Default value for input
 */
function vit_render_input( $span_class = 'control', $ip_name='default', $label = '', $sup = '', $sub = '', $content = '', $inputClass= 'igbt-input', $default_value = '' ){ ?>

    <span class="<?php echo $span_class ?>"><?php
        if ($label != '')
            echo '<label>' . $label . '</label>'; ?>
        
        <input data-default="<?php echo $default_value; ?>" class="<?php echo $inputClass ?>" type="text" name="<?php echo $ip_name ?>" value="">
        <span class="parametric"><?php if( isset( $sup ) && $sup != '' ){ ?><sup><?php echo $sup ?></sup><?php } ?><?php echo $content ?><?php if( !empty( $sub ) ){ ?><sub><?php echo $sub ?></sub><?php } ?></span>
    </span><?php

}

/**
 * Function to render models
 */
function vit_render_models( $span_class = 'control', $select_name = '' ){ ?>

    <span class="select_model <?php echo $span_class ?>">

        <select name="<?php echo $select_name ?>">
            <option value="">Choose an IGBT</option><?php

            $models = vit_get_all_models();

            if( !empty( $models ) ){

                foreach( $models as $model ){

                    echo '<option value="'.$model.'">'.$model.'</option>';

                }

            } ?>

        </select>

    </span><?php

}

/**
 * Function to render action buttons
 */
function vit_render_action_buttons(){ ?>

    <div class="action-buttons">

        <a class="report-bug" href="#"><span>Report a bug</span></a>
        <a class="download-pdf" href="#"><span>Download PDF</span></a>
        <a class="download-pdf" href="#"><span>Download CSV</span></a>
        <a class="get-samples" href="#"><span>Get Samples</span></a>

    </div><?php

}

/**
 * Get error message string
 * @param int $msg_id Id of message
 * @return array|boolean If message id passed return the message string else whole array or false on wrong message id
 */
function graph_error_msgs( $msg_id = FALSE ) {

    $messages = array(
        1 => 'Please select an IGBT and enter My T<sub>j</sub>',
        2 => 'Database Error!',
        3 => 'Please enter My T<sub>j</sub> value from 25 to ',
        4 => 'Please enter current range beetween 0 to ' ,
        5 => 'Please select all models',
        6 => 'Enter frequency range between 0 to 100',
        7 => 'I<sub>min</sub> should be less than I<sub>max</sub>',
    );

    if (!$msg_id)
        return $messages;

    if (array_key_exists($msg_id, $messages))
        return $messages[$msg_id];
    
    return FALSE;
}

/**
 * Calculate Veon for certain model
 */
function calculate_Vceon( $args = array() ){

    global $EZ_DB;

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => array()
    );


    $error_msg = graph_error_msgs();
    extract( $args, EXTR_SKIP );  

    if( empty( $model_id ) || empty( $userTemp ) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[1];
        return ($result_data);

    }

    $query = "SELECT i_rated, tjref, vttjmax, atjmax, btjmax, vt, a, b FROM models where model_name='$model_id'";
    $result =   $EZ_DB->run_query( $query );

    if( $result ) {

        /* range */
        $iRated =   $result['i_rated'];
        $tjMax  =   $result['tjref'];

        /* For VceON */
        $vtRoom = $result['vt'];
        $aRoom  = $result['a'];
        $bRoom  = $result['b'];

        $vtMax  = $result['vttjmax'];
        $aMax   = $result['atjmax'];
        $bMax   = $result['btjmax'];
        /* For VceON */

        $user_plot_rang = FALSE;

        //Validate Imin or Imax value
        if ( !empty( $currentMax ) || !empty( $currentMin ) || $currentMin === '0') {

            $currentMin = (int) $currentMin;
            $currentMax = (int) $currentMax;

            if ( ($currentMax <= 0) || ( ( $iRated * 4 ) < $currentMax ) || ($currentMin >= $currentMax) ){

                $result_data['error'] = true;
                $result_data['error_msg'] = $error_msg[4] . ($iRated*4);
                return ($result_data);

            } else {

                $user_plot_rang = true;

            } 
        }

        $iCordinates = array();

        //Add user plotting points if exist
        if ($user_plot_rang) {

            $range = $currentMax;
            $plotting = ($currentMax - $currentMin) / 19;

            while( $range >= $currentMin ){

                $iCordinates[] = $range;
                $range = $range - $plotting;

            }

        } else {//else calculate the cordinate with 2 times iRated 
            $range = $iRated * 2;
            $plotting = $range / 20;

            while( $range > 0 ){
                $iCordinates[] = $range;
                $range = $range - $plotting;

            }
        }

        $iCordinates = array_reverse($iCordinates);

        if ( !($userTemp <= $tjMax) || !($userTemp >= 25) ) {

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[3] . $tjMax;
            return ($result_data);

        }

        /* Room temp calculations */
        $vceonRoom = $vceonMax = $vceonUser = $main_array = $main_array_vcon =  array(); // multiple initialization

        foreach( $iCordinates as $temp ){

            /************* Calculations for VceON *****************/

            $roomExp        =   pow( $temp, $bRoom );// room exponantial
            $roomCalc       =   $vtRoom + ( $aRoom * $roomExp );
            $vceonRoom[]    =   $roomCalc;

            //Tjmax Calculation
            $maxExp     =   pow( $temp, $bMax );// TjMax exponantial
            $maxCalc    =   $vtMax + ( $aMax * $maxExp );
            $vceonMax[] =   $maxCalc;

            /************* Calculations for VceON Ends *****************/
            
        }

        /* Formula: Vn= V1+ ([V2-V1]/[T2-T1])*(Tn-T1)) */
        foreach( $vceonMax as $key => $MaxVoltage ){

            $calculate      =   $vceonRoom[$key] + ( ( ( $MaxVoltage - $vceonRoom[$key] ) / ( $tjMax - 25 ) ) * ( $userTemp - 25 ) );
            $vceonUser[]    =   $calculate;

        }

        /* Create data format */
        foreach ( $iCordinates as $key => $value ){

            $points[0] = $value;

            //VCon for user
            $points[1] = $vceonUser[$key];
            $main_array_vcon[] = $points;

        }

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[3] . $tjMax;
            $result_data['data']    =   $main_array_vcon;

            return $result_data; // Here we got Vceon points

    }
}

/**
 * This function calculates ETs
 */
function calculate_ets( $args = array() ){
    
    global $EZ_DB;

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => array()
    );

    $error_msg = graph_error_msgs();
    extract( $args, EXTR_SKIP );  

    if( empty( $model_id ) || empty( $userTemp ) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[1];
        return ($result_data);

    }

    $query = "SELECT i_rated, tjref, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$model_id'";
    $result =   $EZ_DB->run_query( $query );

    if( $result ) {

        /* range */
        $iRated =   $result['i_rated'];
        $tjMax  =   $result['tjref'];


        $hTjMax =   $result['htjmax'];
        $kTjMax =   $result['ktjmax'];
        $mTjMax =   $result['mtjmax'];
        $nTjMax =   $result['ntjmax'];

        $hTjRoom    =   $result['h'];
        $kTjRoom    =   $result['k'];
        $mTjRoom    =   $result['m'];
        $nTjRoom    =   $result['n'];

        $user_plot_rang = FALSE;

        //Validate Imin or Imax value
        if (!empty($currentMax) || !empty($currentMin) || $currentMin === '0') {

            $currentMin = (int) $currentMin;
            $currentMax = (int) $currentMax;
            
            if ( ($currentMax <= 0) || ( ( $iRated*4 ) < $currentMax) || ($currentMin >= $currentMax) ) {
                $result_data['error'] = true;
                $result_data['error_msg'] = $error_msg[4] . ($iRated*4);
                echo json_encode($result_data);
                die();
            } else {

                $user_plot_rang = true;
            }
        }

        $iCordinates = array();

        //Add user plotting points if exist
        if ($user_plot_rang) {

            $range = $currentMax;
            $plotting = ($currentMax - $currentMin) / 19;

            while( $range >= $currentMin ){

                $iCordinates[] = $range;
                $range = $range - $plotting;

            }

        } else {

            //else calculate the cordinate with 2 times iRated 
            $range = $iRated * 2;
            $plotting = $range / 20;

            while( $range > 0 ){
                $iCordinates[] = $range;
                $range = $range - $plotting;

            }
        }

        $iCordinates = array_reverse($iCordinates);

        if ( !($userTemp <= $tjMax) || !($userTemp >= 25) ) {

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[3] . $tjMax;
            return ($result_data);

        }

        $EtsRoom = $EtsMax = $EtsUser = $main_array_ets = array(); // multiple initialization

        foreach( $iCordinates as $temp ){

            $iPowerkMax     =   pow( $temp, $kTjMax );// 'm' TjMax exponantial
            $iPowernMax     =   pow( $temp, $nTjMax );// 'n' TjMax exponantial
            $iPowerkRoom    =   pow( $temp, $kTjRoom );// 'm' TjRoom exponantial
            $iPowernRoom    =   pow( $temp, $nTjRoom );// 'n' TjRoom exponantial

            $EtsMax[]       =   ( $hTjMax * $iPowerkMax ) + ( $mTjMax * $iPowernMax );
            $EtsRoom[]      =   ( $hTjRoom * $iPowerkRoom ) + ( $mTjRoom * $iPowernRoom );

            /************* Calculations for Ets Ends *****************/
            
        }

        /* Formula:  ETS = Etsroom +  ( ( [Etsmax - Etsroom ] / [ Tjmax - Temproom ] ) * ( TempUser - 25 ) )  */
        foreach( $EtsMax as $key=>$MaxEts ){

            $calculate      =   $EtsRoom[$key] + ( ( ( $MaxEts - $EtsRoom[$key] ) / ( $tjMax -25  ) ) * ( $userTemp - 25 ) );
            $EtsUser[]      =   $calculate;

        }

        /* Create data format */
        foreach ( $iCordinates as $key => $value ){

            $points[0] = $value;

            //VCon for user
            $points[1] = $EtsUser[$key];
            $main_array_vcon[] = $points;

        }

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[3] . $tjMax;
        $result_data['data']    =   $main_array_vcon;

        return $result_data; // Here we got Vceon points

    }

}

/**
 * Function calculates ploss
 */
function calculate_ploss( $args = array() ){

    global $EZ_DB;

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => array()
    );

    $error_msg = graph_error_msgs();
    extract( $args, EXTR_SKIP );

    if( empty( $modelNo ) || empty( $mytj ) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[1];
        return ($result_data);

    }

    if( ( $fMin <= 0 ) || ( $fMin >100 ) || ( $fMax > 100 ) || ( $fMax <= 0 ) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[6];
        echo json_encode( $result_data );
        die;

    }

    $frequencyDiff      =   ($fMax - $fMin);
    $frequencyRange     =   $frequencyDiff / 10; // plot the ranges of frequencies

    $query = "SELECT i_rated, tjref, vref, vttjmax, atjmax, btjmax, vt, a, b, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$modelNo'";
    $result =   $EZ_DB->run_query( $query );

    if( $result ){

        $tjMax  =   $result['tjref'];

        if( ( $mytj < 25 ) || ( $mytj >= $tjMax ) ){

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[3].$tjMax;
            echo json_encode( $result_data );
            die;

        }

        $vref   =   $result['vref'];
        /* range */
        $iRated =   $result['i_rated'];

        if( ( $myI < 0 ) || ( $myI >= ( 4 * $iRated ) ) ){

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[4]. ( 4 * $iRated );
            return ( $result_data );

        }

        /* For VceON */
        $vtRoom = $result['vt'];
        $aRoom  = $result['a'];
        $bRoom  = $result['b'];

        $vtMax  = $result['vttjmax'];
        $aMax   = $result['atjmax'];
        $bMax   = $result['btjmax'];
        /* For VceON */

        /* For Ets */
        $hTjMax =   $result['htjmax'];
        $kTjMax =   $result['ktjmax'];
        $mTjMax =   $result['mtjmax'];
        $nTjMax =   $result['ntjmax'];

        $hTjRoom    =   $result['h'];
        $kTjRoom    =   $result['k'];
        $mTjRoom    =   $result['m'];
        $nTjRoom    =   $result['n'];
        /* For Ets */

        // Calculate Vceon at Tjmax
        $power =    pow( $myI,$bMax );
        $vconMax    =   $vtMax + ( $aMax * $power ); // done

        // calculate Vceon at room temp
        $power =    pow( $myI , $bRoom );
        $vconRoom    =   $vtRoom + ( $aRoom * $power ); // done

        /* Calculate Vceon at Mytj : Formula: Vn = V1+ ([V2-V1]/[T2-T1])*(Tn-T1)) */
        $VcoenTj = $vconRoom + ( ( ( $vconMax - $vconRoom ) / ( $tjMax - 25 ) ) * ( $mytj - 25 ) ); // This is VceOn at Tj

        // calculate Ets at Tj
        $ipowerk = pow( $myI , $kTjMax );
        $ipowern = pow( $myI, $nTjMax );
        $EtsMax = ( $hTjMax * $ipowerk ) + ( $mTjMax + $ipowern ); // done

        // calculate Ets at room
        $ipowerk = pow( $myI , $kTjRoom );
        $ipowern = pow( $myI, $nTjRoom );
        $EtsRoom = ( $hTjRoom * $ipowerk ) + ( $mTjRoom + $ipowern ); // done

        /* Calculate Ets at Tj Formula:  ETS = Etsroom +  ( ( [Etsmax - Etsroom ] / [ Tjmax - Temproom ] ) * ( TempUser - 25 ) )  */
        $EtsTj = $EtsRoom + ( ( ( $EtsMax - $EtsRoom ) / ( $tjMax - 25 ) ) * ( $mytj - 25 ) ); // This is Ets at Tj

        $plosses  =  $points    =   array();

        while( $fMax >= $fMin ){

            $calculate = ( ( $myD / 100 ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $fMax ) );

            $points[0] = $fMax;
            $points[1] = $calculate;

            $plosses[] = $points;

            $fMax = $fMax - $frequencyRange;

        }

        $plosses = array_reverse($plosses);

        $result_data['error'] = false;
        $result_data['error_msg'] = 'Error!';
        $result_data['data'] = $plosses;

        return ( $result_data );

    }

}

/**
 * Debugging Function
 */
if( !function_exists('db') ){

    function db( $data ){

        echo '<pre>';
        print_r( $data );
        echo '</pre>';
        die;
    }
}
