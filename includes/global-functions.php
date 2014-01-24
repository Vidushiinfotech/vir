<?php
/**
 * Commmon global functions
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
        if ( array_key_exists($Router->page_name, get_pages_name_title()) ) {
            if ($Router->page_name === '404') {
                $classes[] = 'error404'; //Instead of assigning '404' assign 'error404' to maintain one class name
            } else {
                $classes[] = $Router->page_name;
            }
        } else if ($Router->is_cms_page($Router->page_name)){
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
        'compare',
        'recommend'
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
            $_SESSION['user_email'] = $result['user_email'];
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

    $email          =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['loginmail'] );
    $application    =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['primary_application'] );
    $captcha        =   mysqli_real_escape_string( $EZ_DB->connect , $_POST['logincaptcha']);

    $error  =   '';

    if( empty( $captcha ) ){
        $error .= '<p class="error">Please enter the valid captcha value.</p>';
        $_SESSION['security_number']=rand(10000,99999);
    }

    elseif ( $captcha != $_SESSION['security_number'] ) {
        //echo $captcha.'  '.$captcha_code;
        $error .= '<p class="error">You have entered incorrect captcha value.</p>';
        $_SESSION['security_number']=rand(10000,99999);
    }

    if( empty( $email ) )
            $error .= '<p class="error">Enter Email Id</p>';
        elseif( !filter_var($email, FILTER_VALIDATE_EMAIL) )
            $error .= '<p class="error">Enter Valid Email</p>';

    if( empty( $application ) )
        $error .= '<p class="error">Select your primary application</p>';

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

        $query = "INSERT INTO users VALUES ( '', '', '', '', '',  '$email', $application, '0', '0' )";
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
            
            array_push( $models, $row );
        }

    }

    return $models;

}

/**
 * Function to render inputs
 * @param string $span_class Class of span
 * @param string $ip_name Input name
 * @param string $label Label
 * @param string $sup Superscript
 * @param string $sub Subscript
 * @param string $content Content
 * @param string $inputClass Class of input
 * @param string $default_value Default value for input
 * @param string $title Title for input Default is label
 */
function vit_render_input( $span_class = 'control', $ip_name='default', $label = '', $sup = '', $sub = '', $content = '', $inputClass= 'igbt-input', $default_value = '', $title='' ){ ?>

    <span class="<?php echo $span_class ?>"><?php
        $title = strip_tags($title);
        if ($title == '')
            $title = strip_tags($label);
    
        if ($label != '')
            echo '<label title="'. $title .'">' . $label . '</label>'; ?>
        
        <input title="<?php echo $title; ?>" data-default="<?php echo $default_value; ?>" class="<?php echo $inputClass ?>" type="text" name="<?php echo $ip_name ?>" value="">
        <span class="parametric"><?php if( isset( $sup ) && $sup != '' ){ ?><sup><?php echo $sup ?></sup><?php } ?><?php echo $content ?><?php if( !empty( $sub ) ){ ?><sub><?php echo $sub ?></sub><?php } ?></span>
    </span><?php

}

/**
 * Function to render models
 * @param string $span_class Class of span
 * @param string $select_name Name of select
 * @param string $title Title for select
 */
function vit_render_models( $span_class = 'control', $select_name = '', $title='' ){
    
    if ($title)
        $title = 'title="' . $title . '"'; ?>

    <span class="select_model <?php echo $span_class ?>">

        <select <?php echo $title; ?> name="<?php echo $select_name ?>">
            <option value="">Choose an IGBT</option><?php

            $models = vit_get_all_models();

            if( !empty( $models ) ){

                foreach( $models as $model ){

                    echo '<option data-default="'. (int)( $model['i_rated'] / 2 ) .'" value="'.$model['model_name'].'">'.$model['model_name'].'</option>';

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
        <a class="<?php  echo ( is_current_page('recommend') == 'active' ) ? 'download-recommend-csv' : 'download-csv' ?>" href="#"><span>Download CSV</span></a>
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
        8 => 'Can not create csv. Graph data not available!',
        9 => 'Can not create csv. Input values not available!',
       10 => 'Can not create csv. Axis name not available!',
       11 => 'Please enter Tj > Tsink > Tamb',
       12 => 'F<sub>min</sub> should be lesser than F<sub>max</sub>',
       13 => 'Enter Ducty cycle (MyD) beteen 0 to 100'

    );

    if (!$msg_id)
        return $messages;

    if (array_key_exists($msg_id, $messages))
        return $messages[$msg_id];
    
    return FALSE;
}

/**
 * Get nearest value within array
 * @param int $search value to search for
 * @param array $arr array within which search will be performed
 * @return int closest value
 */
function vit_getClosest($search, $arr) {

   $closest = null;

   foreach($arr as $item) {
      if($closest == null || abs($search - $closest) > abs($item - $search)) {
         $closest = $item;
      }
   }

   return $closest;
}

/**
 * Get Calculator status of active or inactive
 * @param int $calc_id Pass the id of calculator else it will return the array of all calculators
 * @return mixed true or false or array 
 */
function get_calc_status($calc_id = NULL) {
    global $EZ_DB;
    $data = serialize(array(NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1));
    $query  = "SELECT key_value FROM config WHERE key_name ='features_status'";
    $result = $EZ_DB->run_query( $query );
    
    if ($result) {
        $data = unserialize($result['key_value']);
    }
    
    if ($calc_id && array_key_exists($calc_id, $data)) {
        return $data[$calc_id];
    } else {
        return $data;
    }
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
        if ( !empty( $currentMax ) || !empty( $currentMin ) || $currentMin === '0' || ( $currentMin <= $currentMax ) ) {

            $currentMin = (int) $currentMin;
            $currentMax = ( !isset( $isCompare ) ) ? (int) $currentMax : $currmaxallow;

            if ( ($currentMax <= 0) || ( ( ( $iRated * 4 ) < $currentMax ) && !isset( $isCompare ) ) || ( isset( $isCompare ) && ( ($currmaxallow) < $currentMax ) ) || ( $currentMin >= $currentMax ) ){

                $currentMin = (int) $currentMin;
                $currentMax = ( !isset( $isCompare ) ) ? (int) $currentMax : $currmaxallow;

                if( $currentMin >= $currentMax ){

                    $result_data['error'] = true;
                    $result_data['error_msg'] = $error_msg[7];

                }else{

                    $result_data['error'] = true;
                    $result_data['error_msg'] = $error_msg[4] . ($iRated*4);

                }

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

        } else { //else calculate the cordinate with 2 times iRated
            $range = $iRated * 2;
            $plotting = $range / 20;

            while( $range > 0 ){
                $iCordinates[] = $range;
                $range = $range - $plotting;

            }
        }

        $iCordinates = array_reverse($iCordinates);

        if ( ($userTemp > $tjMax) || ($userTemp < 25) ) {

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

        $result_data['error'] = false;
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
            $currentMax = isset( $isCompare ) ? $currmaxallow : $currentMax;

            if ( ($currentMax <= 0) || ( ( ( $iRated*4 ) < $currentMax ) && !isset( $isCompare ) ) || ( isset( $isCompare ) && ( ( $currmaxallow ) < $currentMax ) ) || ($currentMin >= $currentMax) ) {
                $result_data['error'] = true;
                if( !isset( $isCompare ) )
                    $result_data['error_msg'] = $error_msg[4] . ($iRated*4);
                else
                    $result_data['error_msg'] = $error_msg[4] . $currmaxallow;
                return ($result_data);
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

        $result_data['error'] = false;
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

    if( ( $fMin <= 0 ) || ( $fMin >100 ) || ( $fMax > 100 ) || ( $fMax <= 0 ) || ($fMin >= $fMax) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[6];
        echo json_encode( $result_data );
        die;

    }

    $frequencyDiff      =   ($fMax - $fMin);

    if( $fMax >10 )
        $frequencyRange     =   $frequencyDiff / 20; // plot the ranges of frequencies
    else
        $frequencyRange     =   $frequencyDiff / 30; // plot the ranges of frequencies

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

        /* This is Veon at Tj  */
        $VcoenTj = calculate_vceon_single_temp( array( 'myI'=>$myI, 'bMax'=>$bMax, 'aMax'=>$aMax, 'vtMax'=>$vtMax, 'bRoom'=>$bRoom, 'aRoom'=>$aRoom, 'vtRoom'=>$vtRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

        /* This is Veon at Tj  */
        $EtsTj = calculate_ets_single_temp( array( 'myI'=>$myI, 'kTjMax'=>$kTjMax, 'nTjMax'=>$nTjMax, 'hTjMax'=>$hTjMax, 'mTjMax'=>$mTjMax, 'kTjRoom'=>$kTjRoom, 'nTjRoom'=>$nTjRoom, 'hTjRoom'=> $hTjRoom, 'mTjRoom'=>$mTjRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );
        $plosses  =  $points    =   array();

        while( $fMax >= $fMin ){

            $prevVal    =   $fMax;
            $fMax       =   (int)$fMax;

            if( !$fMax )
                $fMax   =   $prevVal;

            $calculate = ( ( $myD / 100 ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $fMax * 1000 / 1000000 ) );
            if( (int)$fMax == 16 ){

                //echo $myD.'--'.$VcoenTj.'--'.$myI.'--'.$myvdc.'--'.$vref.'--'.$EtsTj.'--'.$fMax; die;
            }

            $points[0] = $fMax;
            $points[1] = $calculate;

            $plosses[] = $points;
            
            $fMax = $fMax - $frequencyRange;
        }

        if( ($fMax != $fMin) ){

            $calculate = ( ( $myD/100  ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $fMin * (1000 / 1000000) ) );

            $points[0] = $fMin;
            $points[1] = $calculate;

            $plosses[] = $points;

        }

        $plosses = array_reverse($plosses);

        $result_data['error'] = false;
        $result_data['error_msg'] = 'Error!';
        $result_data['data'] = $plosses;
        return ( $result_data );

    }
}

/**
 * Analyze 3rd tab
 * @global object $EZ_DB
 * @param array $args
 * @return boolean
 */
function calculate_heat_sink( $args = array() ){

    global $EZ_DB;

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => array()
    );

    $error_msg = graph_error_msgs();

    extract( $args, EXTR_SKIP );

    if( empty( $args ) )
        return false;

    /* Do all validations here */
    if( empty( $model ) ){

        $result_data = array(

            'error' => true,
            'error_msg' => $error_msg[1],
            'data' => array()
        );
    }

    $query = "SELECT er0tjmax, d1tjmax,	d2tjmax, er0, d1, d2, vdt, ad, bd, vtdtjmax, bdtjmax, adtjmax , i_rated, tjref, vref, vttjmax, atjmax, btjmax, vt, a, b, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$model'";
    $result =   $EZ_DB->run_query( $query );

    if( !empty( $result ) ){

        $tjMax = $result['tjref'];

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

        $vref   =   $result['vref'];

        $frequencyDiff      =   ($fmax - $fmin);
        $frequencyRange     =   $frequencyDiff / 20; // plot the ranges of frequencies. for plotting 10 points

        /* First calculate Vceon at Tj */
        $VcoenTj = calculate_vceon_single_temp( array( 'myI'=>$myI, 'bMax'=>$bMax, 'aMax'=>$aMax, 'vtMax'=>$vtMax, 'bRoom'=>$bRoom, 'aRoom'=>$aRoom, 'vtRoom'=>$vtRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

        /* Calculate Ets at Tj */
        $EtsTj = calculate_ets_single_temp( array( 'myI'=>$myI, 'kTjMax'=>$kTjMax, 'nTjMax'=>$nTjMax, 'hTjMax'=>$hTjMax, 'mTjMax'=>$mTjMax, 'kTjRoom'=>$kTjRoom, 'nTjRoom'=>$nTjRoom, 'hTjRoom'=> $hTjRoom, 'mTjRoom'=>$mTjRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

        $points = $plosses = $plot = array();

        while( $fmax >= $fmin ){

            $prevVal    =   $fmax;
            $fmax       =   (int)$fmax;

            if( !$fmax )
                $fmax = $prevVal;

            $calculate  =   ( ( $myD / 100  ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $fmax * (1000 / 1000000) ) );

            $points[0] = $fmax;
            $points[1] = $calculate;

            $plosses[] = $points;

            $fmax = $fmax - $frequencyRange;

        }

        $fMax = $fmax;
        $fMin = $fmin;

        if( ($fMax != $fMin) ){

            $calculate = ( ( $myD / 100  ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $fMin * (1000 / 1000000) ) );

            $points[0] = $fMin;
            $points[1] = $calculate;

            $plosses[] = $points;

        }


        $plosses = array_reverse($plosses);

        /* Now calculate RTHSA for each Ploss point */
        foreach( $plosses as $ploss ){

            $plottinfPoints[0]  =   $ploss[0];  
            $plottinfPoints[1]  =   ( $tSink - $tAmb )/$ploss[1];

            $plot[] = $plottinfPoints;

        }

        $result_data['error'] = false;
        $result_data['error_msg'] = 'Error!';
        $result_data['data'] = $plot;

        if( !empty( $returnRaw ) )
            return ( $result_data );
        else
            return json_encode( $result_data );

    }

}

/**
 * function to calculate switch losses
 */
function calculate_split_loss( $args = array() ){

    global $EZ_DB;

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => array()
    );

    $error_msg = graph_error_msgs();
    extract( $args, EXTR_SKIP );

    if( empty( $args ) )
        return false;

    /* Do all validations here */
    if( empty( $model ) ){

        $result_data = array(

            'error' => true,
            'error_msg' => $error_msg[1],
            'data' => array()
        );
    }

    $query = "SELECT er0tjmax, d1tjmax,	d2tjmax, er0, d1, d2, vdt, ad, bd, vtdtjmax, bdtjmax, adtjmax , i_rated, tjref, vref, vttjmax, atjmax, btjmax, vt, a, b, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$model'";
    $result =   $EZ_DB->run_query( $query );

    if( !empty( $result ) ){

        $tjMax = $result['tjref'];
        $vref   =   $result['vref'];
        $iRated =   $result['i_rated'];

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

        /* For wheeling diode */
        $vtdtjmax   = $result['vtdtjmax'];
        $bdtjmax    = $result['bdtjmax'];
        $adtjmax    = $result['adtjmax'];

        $vdt    =   $result['vdt'];
        $ad     =   $result['ad'];
        $bd     =   $result['bd'];

        /* For Err */
        $er0tjmax   =   $result['er0tjmax'];
        $d1tjmax    =   $result['d1tjmax'];
        $d2tjmax    =   $result['d2tjmax'];
        $er0        =   $result['er0'];
        $d1         =   $result['d1'];
        $d2         =   $result['d2'];

        /* First calculate Vceon at Tj */
        $VcoenTj = calculate_vceon_single_temp( array( 'myI'=>$myI, 'bMax'=>$bMax, 'aMax'=>$aMax, 'vtMax'=>$vtMax, 'bRoom'=>$bRoom, 'aRoom'=>$aRoom, 'vtRoom'=>$vtRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

        /* Calculate Ets at Tj */
        $EtsTj = calculate_ets_single_temp( array( 'myI'=>$myI, 'kTjMax'=>$kTjMax, 'nTjMax'=>$nTjMax, 'hTjMax'=>$hTjMax, 'mTjMax'=>$mTjMax, 'kTjRoom'=>$kTjRoom, 'nTjRoom'=>$nTjRoom, 'hTjRoom'=> $hTjRoom, 'mTjRoom'=>$mTjRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

        /* Calculate free wheeling diode */
        $iPowerbTjMax    =   pow( $myI, $bdtjmax );
        $V_FtjMax   =   $vtdtjmax + $adtjmax * $iPowerbTjMax; // at TjMax

        $iPowerbRoom    =   pow( $myI, $bd );
        $V_FRoom        =   $vdt + $ad * $iPowerbRoom; // at Room

        $v_FTj          =   $V_FRoom + ( ($V_FtjMax-$V_FRoom) / ( $tjMax - 25 ) * ( $mytj - 25 ) ); // Vf at tj
        
        /* Calculate Err at Tj */
        $ErrTjMax       =   $er0tjmax + $d1tjmax * ( pow( $myI, $d2tjmax ) ); // At Tjmax
        $ErrRoom        =   $er0 + $d1 * ( pow( $myI, $d2 ) ); // At room
        $ErrTj          =   $ErrRoom + ( ( $ErrTjMax - $ErrRoom ) / ( $tjMax - 25 ) * ( $mytj - 25 ) ); // At tj

        $PlossConduction    =   ( $myD / 100 ) * ( ( $VcoenTj ) * $myI );
        $pSwitching         =   ( $EtsTj * $myf * (1000 / 1000000) );
        $pCondDiode         =   ( ( 100 - $myD ) / 100 ) * ( $v_FTj * $myI );
        $pSwitchingDiode    =   $ErrTj * $myf * (1000 / 1000000);

        if( !$pSwitchingDiode )
            $pSwitchingDiode = $pSwitching * 0.2;

        $result_data['data'] = array(  array( 2, $PlossConduction ), array( 4,$pSwitching ), array( 6,$pCondDiode ), array( 8,$pSwitchingDiode ) );

        return $result_data;

    }

}

/**
 * Calculate Vceon at single temperature
 */
function calculate_vceon_single_temp( $args ){

    extract( $args, EXTR_SKIP );
    // Calculate Vceon at Tjmax
    $power      =   pow( $myI , $bMax );
    $vconMax    =   $vtMax + ( $aMax * $power ); // done

    // calculate Vceon at room temp
    $power =    pow( $myI , $bRoom );
    $vconRoom    =   $vtRoom + ( $aRoom * $power ); // done

    /* Calculate Vceon at Mytj : Formula: Vn = V1+ ([V2-V1]/[T2-T1])*(Tn-T1)) */
    $VcoenTj = $vconRoom + ( ( ( $vconMax - $vconRoom ) / ( $tjMax - 25 ) ) * ( $mytj - 25 ) ); // This is VceOn at Tj
    return $VcoenTj;

}

/**
 * Calculate Ets at single temperature
 */
function calculate_ets_single_temp( $args ){

    extract( $args, EXTR_SKIP );

    // calculate Ets at Tj
    $ipowerk = pow( $myI , $kTjMax );
    $ipowern = pow( $myI, $nTjMax );
    $EtsMax = ( $hTjMax * $ipowerk ) + ( $mTjMax * $ipowern ); // done

    // calculate Ets at room
    $ipowerk = pow( $myI , $kTjRoom );
    $ipowern = pow( $myI, $nTjRoom );
    $EtsRoom = ( $hTjRoom * $ipowerk ) + ( $mTjRoom * $ipowern ); // done

    /* Calculate Ets at Tj Formula:  ETS = Etsroom +  ( ( [Etsmax - Etsroom ] / [ Tjmax - Temproom ] ) * ( TempUser - 25 ) )  */
    //print_r($EtsRoom); die;
    $EtsTj = $EtsRoom + ( ( ( $EtsMax - $EtsRoom ) / ( $tjMax - 25 ) ) * ( $mytj - 25 ) ); // This is Ets at Tj

    return $EtsTj; // This is the Ets value at Tj

}

/**
 * Function to calculate I vs F curve
 */
function calculate_i_vs_f( $args = array() ) {

    global $EZ_DB;

    $result_data = array( 'error'=>false, 'error_msg'=>'', 'data'=>'' );
    $error_msg = graph_error_msgs();

    extract( $args, EXTR_SKIP );

    /* Temperature validation */
    if( $mytj < $tsink ){
        $result_data = array( 'error'=>true, 'error_msg'=>'Please enter Tj > Tsink', 'data'=>'' );
        return ($result_data);
        die;
    }

    /* Check for valid frequency range */
    if( $fmax < $fmin ){
        $result_data = array( 'error'=>true, 'error_msg'=>$error_msg[12], 'data'=>'' );
        return ($result_data);
        die;
    }

    $query = "SELECT er0tjmax, d1tjmax, rthjc_igbt, d2tjmax, er0, d1, d2, vdt, ad, bd, vtdtjmax, bdtjmax, adtjmax , i_rated, tjref, vref, vttjmax, atjmax, btjmax, vt, a, b, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$model'";
    $result =   $EZ_DB->run_query( $query );
    
    if( !empty( $result ) ){

        $tjMax  =   $result['tjref'];

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

        $vref   =   $result['vref'];
        $rthjc  =   $result['rthjc_igbt'];

        /* First calculate frequency range */
        $frequencyDiff      =   ($fmax - $fmin);
        $frequencyRange     =   $frequencyDiff / 15;

        $plossesall = $allFrequencies = $allTjs = $allCurrents = $plotting =  array(); // Initialize empty arrays for later storage

        while( $fmax >= $fmin ){

            array_push( $allFrequencies, $fmax ); // push all values to an array
            $fmax = $fmax - $frequencyRange;

        }

        if( ($fmax != $fmin) ){

            array_push( $allFrequencies, $fmin ); // push all values to an array
        }

        foreach( $allFrequencies as $index => $frequency ){

            for( $myI = 0; $myI <= 200; $myI += 0.01 ){

                $VcoenTj = calculate_vceon_single_temp( array( 'myI'=>$myI, 'bMax'=>$bMax, 'aMax'=>$aMax, 'vtMax'=>$vtMax, 'bRoom'=>$bRoom, 'aRoom'=>$aRoom, 'vtRoom'=>$vtRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

                $EtsTj = calculate_ets_single_temp( array( 'myI'=>$myI, 'kTjMax'=>$kTjMax, 'nTjMax'=>$nTjMax, 'hTjMax'=>$hTjMax, 'mTjMax'=>$mTjMax, 'kTjRoom'=>$kTjRoom, 'nTjRoom'=>$nTjRoom, 'hTjRoom'=> $hTjRoom, 'mTjRoom'=>$mTjRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );

                $plossTj = ( ( $myD / 100 ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $frequency * 1000 / 1000000 ) );

                $calculatedTj = ( $rthjc * $myrthcs ) * $plossTj * $tsink;

                array_push( $allTjs, $calculatedTj );
                array_push( $allCurrents, $myI );
                array_push( $plossesall, $plossTj );

            }// end of for loop

            $closestval = vit_getClosest( $mytj, $allTjs );
            $getKey = array_search( $closestval, $allTjs );
            $currentval = $allCurrents[$getKey];

            $points[0]  = $frequency;
            $points[1]  = $currentval;

            $plotting[] = $points;

            $allTjs = $allCurrents = $plossesall = array();

        }

        $plotting  = array_reverse( $plotting );

        $result_data['data'] = $plotting;

        return ( $result_data );
    }
    
}

function get_web_page($url) {
    $options = array (CURLOPT_RETURNTRANSFER => true, // return web page
    CURLOPT_HEADER => false, // don't return headers
    CURLOPT_FOLLOWLOCATION => true, // follow redirects
    CURLOPT_ENCODING => "", // handle compressed
    CURLOPT_USERAGENT => "test", // who am i
    CURLOPT_AUTOREFERER => true, // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
    CURLOPT_TIMEOUT => 120, // timeout on response
    CURLOPT_MAXREDIRS => 10 ); // stop after 10 redirects

    $ch = curl_init ( $url );
    curl_setopt_array ( $ch, $options );
    $content = curl_exec ( $ch );
    $err = curl_errno ( $ch );
    $errmsg = curl_error ( $ch );
    $header = curl_getinfo ( $ch );
    $httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );

    curl_close ( $ch );

    $header ['errno'] = $err;
    $header ['errmsg'] = $errmsg;
    $header ['content'] = $content;
    return $header ['content'];
}

/**
 * Sort array based on key order of other array.
 * @param array $array array to sort ( pass it by reference )
 * @param array $refArray reference array.
 */
function vit_sort_array( $array, $refArray ){

    $toCount = count( $array ) / 2;
    $toCount = floor( $toCount );

    $count = 0;
    foreach ( $array as $key=>$val ){

        if( $count >= $toCount )
            break;

        $temp = $refArray[$count];
        $refArray[$count] = $refArray[$key];
        $refArray[$key] = $temp;

        $count++;
    }

    return $refArray;
}

/**
 * Make indices of array in ascending order
 */
function vit_maintain_indices( $array = array() ){

    if( empty( $array ) )
        return $array();

    $order_array = array();

    foreach( $array as $k=>$v ){
        
        array_push( $order_array, $v );
    }

    return $order_array;

}

/**
 * Debugging Function
 */
if( !function_exists('db') ){

    function db( $data ){

        echo '<pre>';
        print_r( $data );
        echo '</pre>';
    }
}
