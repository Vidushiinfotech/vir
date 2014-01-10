<?php
/**
 * If url is hitted directly, stop user.
 */
if( empty( $_POST['action'] ) )
    die('Don\'t try to cheat me. ;)');

/* include files */
require_once '../config.php';
require_once EZ_BASE_PATH . 'includes/EZ_DB.php';
require_once EZ_ADMIN_PATH . 'includes/admin-functions.php';

global $EZ_DB;


/******  Trial ******/

/******  Trial ******/

/**
 * For loading more model names.
 */
if( $_POST['action'] == 'load_more' ){

    $paged = ( !empty($_POST['pagenumber']) ) ? $_POST['pagenumber'] : 0;
    $offst = $paged * 50;

    /* Display Model Names */        
    $query      =   "SELECT model_name, include_model FROM models ORDER BY model_name ASC LIMIT 50 OFFSET ".$offst;
    $results    =   $EZ_DB->run_query( $query, 1 );
    $count = 0;

    while( $row = mysqli_fetch_assoc( $results ) ){

        $class      =   ( !$count || ( $count % 2 == 0 ) ) ? ' even' : ' odd';
        $message    =   ( $row['include_model'] ) ? 'Currently Shown' : 'Currently Hidden';
        $source     =   ( $row['include_model'] ) ? VIT_IMG.'/show.png' : VIT_IMG.'/hide.png';

        echo '<div class="row'. $class.' clearfix">';

            echo '<div class="col model-cols">'. $row['model_name'] .'</div>';

            echo '<div class="col on-off model-onoff">
                    <span class="onoffmsg">'. $message .'</span>
                    <img data-part="'. $row['model_name'] .'" class="alter-parts" src="'.  $source .'" title="Show" alt="on-off" />
                  </div>';

            echo '<div class="col model-delete"><a data-modelname="'. $row['model_name'] .'" href="#">Delete this part</a></div>';

        echo '</div>';

        $count++; // increment counter

    }

}

/* To show / hide models on front end */
if( $_POST['action'] == 'alter_part' ){

    $model = empty( $_POST['modelname'] ) ? 0 : $_POST['modelname'];

    if( !$model )
        echo '0';

    $query      =   "SELECT include_model FROM models WHERE model_name = '".$model."'";
    $results    =   $EZ_DB->run_query( $query, 1 );

    while( $row = mysqli_fetch_assoc($results) ){

        $current_status = $row['include_model']; // store current status in this variable

    }

    /* Only if we got some result */
    if( isset( $current_status ) ){

        $current_status = ( $current_status == 1 ) ? 0 : 1; // Alter the status here

        $query  =   "UPDATE models SET include_model = ".$current_status." WHERE model_name = '".$model."'";
        $results    =   $EZ_DB->run_query( $query, 1 );

        if( $results )
            echo 1;

    }else{

        echo '0'; // If nothing was found previously
    }

}

/* To delete a model from database */
if( $_POST['action'] == 'delete_part' ){

    $model  =   empty( $_POST['modelname'] ) ? 0 : $_POST['modelname'];

    if( !$model )
        echo '0';

    $query      =   "DELETE FROM models WHERE model_name = '". $model ."'";
    $results    =   $EZ_DB->run_query( $query, 1 );

    if( !$results )
        echo '0';
    else
        echo '1';

}

/* Feature On / Off */
if( $_POST['action'] == 'feature_toggle' ){

    if( empty( $_POST['featureno'] ) ){
        echo '0';
    }

    $featureno      =   $_POST['featureno'];
    $statusCurrent  =   $_POST['status'];

    $statusCurrent  = intval($statusCurrent);
    $statusCurrent  = !($statusCurrent); // Toggle the on / off status

    $query      =   "SELECT key_value FROM config WHERE key_name ='features_status'";
    $results    =   $EZ_DB->run_query( $query, 1 );
    $row        =   mysqli_fetch_assoc($results);

    $Data               =   unserialize( $row['key_value'] );
    $Data[$featureno]   =   $statusCurrent; // Alter the status of that perticular feature

    $Data       =   ( serialize( $Data) );
    $query      =   "UPDATE config SET key_value = '$Data' WHERE key_name='features_status'";
    $results    =   $EZ_DB->run_query( $query, 1 );

    if( $results != false )
        echo '1';
    else
        echo '0';

}

if( $_POST['action'] == 'create_page' ){

    $title   = empty( $_POST['title'] ) ? '' : mysqli_real_escape_string( $EZ_DB->connect, $_POST['title'] );
    $content = empty( $_POST['content'] ) ? '' : $_POST['content'];
    $pid     = empty( $_POST['pid'] ) ? 0 : $_POST['pid'];

    $error  =   '';
    
    if( empty( $title ) || is_numeric($title) )
        $error .= '<p class="error">Enter Title</p>';

    if( empty( $content ) )
        $error .= '<p class="error">Enter Content</p>';

    if( !empty( $error ) ){

        echo 'error~'.$error;

    }else{

        $slug = sanitize_title_with_dashes($title);

        if( !$pid )
            $query  = "INSERT INTO pages VALUES ( '', '$slug', '$title', '$content', '1' )";
        else
            $query  = "UPDATE pages SET title='$title', slug='$slug', content='$content', visible='1' WHERE ID='$pid'";
       
        $result = $EZ_DB->run_query( $query, 1 );

        if( $result ){

            if( !$pid )
                $msg = 'success~<p class="success">Page Created Successfully</p>';
            else
                $msg = 'success~<p class="success">Page Updated Successfully</p>';

        }else
            $msg = 'error~<p class="error">Something went wrong, please try again</p>';

        echo $msg;

    }

}

if( $_POST['action'] == 'delete_page' ){

    $pid = empty( $_POST['pid'] ) ? 0 : $_POST['pid'];

    if( empty( $pid ) )
        $msg = "<p class='error'>Invalid request</p>";

    $query = "DELETE FROM pages WHERE ID='$pid'";

    $res = $EZ_DB->run_query( $query, 1 );

    if( $res )
        $msg = "<p class='success'>Post deleted successfully</p>";
    else
        $msg = "<p class='error'>Unable to delete post, please try again</p>";

    echo $msg;

}

if( $_POST['action'] == 'cform_submit' || $_POST['action'] == 'report_bug' ){

    require_once './external-libs/PHPMailer/class.phpmailer.php';

    $cform  =   ( $_POST['action'] == 'cform_submit' ) ? true : false;
    //$cform  =   ( $_POST['action'] == 'cform_submit' ) ? 'cform' : 'bug';

    if( $cform ):
        $fname      =   empty( $_POST['fname'] ) ? '' : $_POST['fname'];
        $lname      =   empty( $_POST['lname'] ) ? '' : $_POST['lname'];
    endif;
        $email      =   empty( $_POST['mail'] ) ? 'anonymous@igbt.com' : $_POST['mail'];
        $sbjct      =   'Contact Us Request';
        $message    =   empty( $_POST['msg'] ) ? '' : $_POST['msg'];
        
    if( !$cform )
        $issue      =   empty( $_POST['issue'] ) ? '' : $_POST['issue'];

    $error      =   '';

    if( $cform ):

        if( empty( $fname ) )
            $error .= '<p class="error">Enter First Name</p>';
        elseif( !preg_match("/^[a-zA-Z'-]+$/",$fname) )
            $error .= '<p class="error">Enter Valid First Name</p>';

        if( empty( $lname ) )
            $error .= '<p class="error">Enter Last Name</p>';
        elseif( !preg_match("/^[a-zA-Z'-]+$/",$lname) )
            $error .= '<p class="error">Enter Valid Last Name</p>';

    endif;

    if( $cform ):

        if( empty( $email ) )
            $error .= '<p class="error">Enter Email Id</p>';
        elseif( !filter_var( $email, FILTER_VALIDATE_EMAIL) )
            $error .= '<p class="error">Enter Valid Email</p>';

        if( empty( $sbjct ) )
            $error  .=  '<p class="error">Enter Subject</p>';

        if( empty( $message ) )
            $error  .=  '<p class="error">Enter Message</p>';

    endif;

    if( empty( $error ) ){

        if( $cform )
            $url        =   './Mails/Notify.html';
        else
            $url        =   './Mails/bugmail.html';

        $mail_HTML  =   file_get_contents( $url );

        if( $cform )
            $name = $fname.' '.$lname;

        $mail_HTML  =   str_replace('[igbt-logo]' , return_site_url(). 'assets/img/logo.png', $mail_HTML );

        if( $cform )
            $mail_HTML  =   str_replace('[igbt-name]' , $name, $mail_HTML );
        if( !empty($email) )
            $mail_HTML  =   str_replace('[igbt-mail]' , $email, $mail_HTML );
            $mail_HTML  =   str_replace('[igbt-msg]'  , $message, $mail_HTML );

        if( !$cform )//[igbt-issue]
            $mail_HTML  =   str_replace( '[igbt-issue]' , $issue, $mail_HTML );

        $subject    =   $sbjct;
        $toSend     =   $email;

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->Host     = EZ_SMTP_HOST;

        $mail->Port       =  EZ_SMTP_PORT; // set the SMTP port for the GMAIL server
        $mail->SMTPAuth  = TRUE;
        $mail->Username   = EZ_SMTP_USER; // SMTP account username
        $mail->Password   = EZ_SMTP_PASS;// SMTP account password

        if( $cform )
            $mail->SetFrom($email,  $fname.' '.$lname );
        else
            $mail->SetFrom( EZ_SMTP_FROM, EZ_SMTP_REPLY_FROM_NAME );

        $address = EZ_SMTP_FROM;
        $mail->AddAddress( $address, EZ_SMTP_REPLY_FROM_NAME );

        $mail->Subject    = $subject;

        $mail->MsgHTML( $mail_HTML );

        if( !$mail->Send() ) {
          echo '<p class="error">Something went wrong, please try again !</p>';
        } else {
          echo '<p class="success">Thank You !</p>';
        }

    }else{

        echo $error;
    }

}

/* Add a subscriber to the databse */
if( $_POST['action'] == 'subscribeme' ){

    $error  =   '';    
    $email  = empty( $_POST['email'] ) ? '' : $_POST['email'];

    if( empty( $email ) ){
        
        $error  =   '<p class="error">Please enter email</p>';
    }

    if( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
        
        $error  =   '<p class="error">Please enter valid email</p>';
    }
    
    /* Check if email already registered */
    if( empty( $error ) ){
        
        $query  =   "SELECT * FROM users WHERE user_email='".$email."'";
        $result =   $EZ_DB->run_query( $query );
        
        if( $result )
            $error  =   '<p class="error">Email already exists</p>';
    }
    
    /* Insert the user here */
    if( empty( $error ) ){
        
        $query  =   "INSERT INTO users VALUES ( '', '$email', '', '', '', '$email', '0', '1' )";
        $result =   $EZ_DB->run_query( $query );
        
        if( $result )
            $error  =   '<p class="success">Subscribed successfully !</p>';
    }

    echo $error;

}

/* Download graph CSV */
if( $_POST['action'] == 'graph_csv' ){

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => return_site_url() . 'admin/download.php?site=' . return_site_url() . '&file=graph_output.csv'
    );
    
    $csv_heading = array();
    $csv_data = array();
    $csv_data_input_values = array();
    $is_compare = isset($_POST['is_compare']) && ($_POST['is_compare'] === 'true') ? true : false;
    $data_validation_failed = true;
    
    //Validate the data (graph points)
    if ($is_compare && !empty($_POST['data'])) {
        foreach ($_POST['data'] as $value) {
            if (!empty($value) && is_array($value)) {
                $data_validation_failed = false;
            }
        }
    } else if(!empty($_POST['data'])) {
        if (!empty($_POST['data'][0]) && is_array($_POST['data'][0])) {
            $data_validation_failed = false;
        }
    }

    if ( $data_validation_failed ) {
        $result_data['error'] = true;
        $result_data['error_msg'] = graph_error_msgs(8);
    } else if (!(isset($_POST['input_values']) && is_array($_POST['input_values']) && !empty($_POST['input_values']))) {
        $result_data['error'] = true;
        $result_data['error_msg'] = graph_error_msgs(9);
    } else if (!(isset($_POST['axis_names']) && is_array($_POST['axis_names']) && !empty($_POST['axis_names']))) {
        $result_data['error'] = true;
        $result_data['error_msg'] = graph_error_msgs(10);
    } else {

        $graph_data = $_POST['data'];
        foreach ($_POST['input_values'] as $key => $value) {
            $key = preg_replace("/[^a-zA-Z0-9 ]+/", "", $key);
            $csv_heading[] = trim($key);
            $csv_data_input_values[] = $value;
        }
        
        if( $is_compare ):
            $allModels  =   $csv_data_input_values[0];
            $csv_data_input_values[0] = $csv_data_input_values[0][0];
        endif;

        foreach ($_POST['axis_names'] as $key2 => $value2) {

            if (isset($value2[0]) && !empty($value2[0])) {
                $csv_heading[] = trim($value2[0]);
            } else {
                $csv_heading[] = 'novalue';
            }

            if (isset($value2[1]) && !empty($value2[1])) {
                $csv_heading[] = trim($value2[1]);
            } else {
                $csv_heading[] = 'Y'.($key2+1);
            }
        }

        $MainName       =   ( $is_compare ) ? 'Compare' : 'Analyze';

        $result_data['data']    =   return_site_url() . 'admin/download.php?site=' . return_site_url() . '&file='.$MainName.'.csv';

        $csv_file = fopen( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$MainName.'.csv', 'w+');
        chmod( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$MainName.'.csv', 0777 );
        $temp = $csv_heading;
        $csv_heading = array();

        $csv_heading_prev = $csv_heading;

        foreach ($temp as $indexing => $value) {

            if( $value === 'novalue' ){
                
                $noIndexing = $indexing;
                continue;

            }
            $csv_heading[] = str_replace('µ', 'u', $value);

        }

        fputcsv( $csv_file , $csv_heading );

        /* Store the point of first graph */
        if (!empty($graph_data[0])) {

            $graph_data1 = $graph_data[0];
            $counter = 1;

            foreach ($graph_data1 as $single_series1) {

                foreach ($single_series1 as $value) {

                $temp_array = array();
                $temp_array = array_merge($temp_array, $csv_data_input_values);
                $temp_array = array_merge($temp_array, $value);
                if( isset( $noIndexing ) ){
                    unset($temp_array[$noIndexing]);
                }
                $csv_data[] = $temp_array;
            }

                if ( !$is_compare && $counter == 1 ) {
                    break;
        }
        
                if( $counter < 3 ) // a Patch code ;)
                    $csv_data_input_values[0] = $allModels[$counter];
                $counter++;

            }
        }

        /* Store the point of second graph if exist */
        if (!empty($graph_data[1])) {

            $graph_data2 = $graph_data[1];
            $counter = 1;
            $index = 0;

            foreach ($graph_data2 as $single_series2) {

                foreach ($single_series2 as $key => $value) {

                    if ( isset($csv_data[$index]) ) {

                        $csv_data[$index] = array_merge($csv_data[$index], $value);
                }

                    $index++;
            }

                if ( !isset($is_compare) && $counter == 1) {
                    break;
        }
        
                $counter++;
            }

        }

        foreach ($csv_data as $value) {
            fputcsv( $csv_file , $value);
        }
        
        fclose($csv_file);
    }    

    echo json_encode($result_data);

}

/* Refresh captcha logic */
if( $_POST['action'] == 'refresh_captcha' ){

    session_start();

    $_SESSION['security_number']=rand(10000,99999);
    $rand = time();
    echo '<img class="captcha_img" src="'.return_site_url().'admin/external-libs/captchalib/image.php?'.$rand.'" />';

}

/**************** Code For PDF Generation is below **********************/
    /* Create PDF File */
    if( strpos( $_POST['action'], 'pdf' ) != FALSE ){

        require_once './external-libs/fpdf/fpdf.php';

        $dataURL = $_POST["image"];

        // Extract base64 data (Get rid from the MIME & Data Type)
        $parts = explode(',', $dataURL);  
        $data = $parts[1];

        // Decode Base64 data
        $data = base64_decode($data);  
        // Save data as an image
        $fp = fopen('image.png', 'w');
        fwrite($fp, $data);  
        fclose($fp); 

        if( file_exists(EZ_BASE_PATH.'uploads/analyse.pdf') )
            unlink( EZ_BASE_PATH.'uploads/analyse.pdf' );
        if( file_exists(EZ_BASE_PATH.'uploads/compare.pdf') )
            unlink( EZ_BASE_PATH.'uploads/compare.pdf' );

        ini_set("session.auto_start", 0);

        class PDF extends FPDF {

            function Header() {

                $this->Image('image.png',10,45,200);

            }
        }

        $pdf = new PDF();
        $timestamp = time();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->Image('logo.png',10,5,20);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell( 140, -3 , 'ezIGBT makes it easy to find the right IGBT for your application' , 0 , 0, 'R' );
        $pdf->Ln(3);
        #row of captions
        $pdf->SetFont('Arial','',9);
        $pdf->Ln(6);

        if ( $_POST['action'] == 'analyze_tab1_pdf' ) {

                $inp1 = $_POST["v1"];
                $inp2 = $_POST["v2"];
                $inp3 = $_POST["v3"];
                $inp4 = $_POST["v4"];

                if($inp3==''){
                    $inp3='-';
                }
                else{
                        $inp3 = $inp3 . 'A';
                }

                if($inp4==''){
                    $inp4='-';
                }

                else {

                    $inp4 = $inp4 . 'A';

                }

                $inp2 = $inp2 . 'C';

                $pdf->Cell(45,4,'Discrete IGBT:',0,0,'C',0);
                $pdf->Cell(55,4,'Temperature:',0,0,'C',0);
                $pdf->Cell(35,4,'Min Current',0,0,'C',0);
                $pdf->Cell(35,4,'Max Current',0,1,'C',0);
                $pdf->Ln(0);

                #row of values
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(45,6,$inp1,1,0,'C',0);
                $pdf->Cell(55,6,$inp2,1,0,'C',0);
                $pdf->Cell(35,6,$inp3,1,0,'C',0);
                $pdf->Cell(35,6,$inp4,1,1,'C',0);
                $pdf->Ln(2);
                $pdf->Cell( 195 , -45, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
                $pdf->Ln(3);
                $pdf->SetFont('Arial','',12);
                $pdf->Cell(255,2,str_repeat('_',130),0,1,'C',0);
                $pdf->Ln(2);

                $pdf->Output('analyse.pdf', 'F');

                $status = copy( EZ_ADMIN_PATH.'analyse.pdf', EZ_BASE_PATH.'uploads/analyse.pdf' );
                unlink( EZ_ADMIN_PATH.'analyse.pdf' );

                $file = 'analyse.pdf';

                echo $file;

        }

        /* Generate PDF for analyze tab2 */
        if ( $_POST['action'] == 'analyze_tab2_pdf' ) {

            $model  =   $_POST['modelname'];
            $temp   =   $_POST['temp']; // temperature
            $myD    =   $_POST['myd']; // myd
            $myvdc  =   $_POST['myvdc']; // myvdc
            $myI    =   $_POST['myI'];
            $fMin   =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin'];
            $fMax   =   empty( $_POST['fmax'] ) ? 100 : $_POST['fmax'];

            $pdf->Cell(35,4,'Discrete IGBT:',0,0,'C',0);
            $pdf->Cell(35,4,'Temperature:',0,0,'C',0);
            $pdf->Cell(20,4,'MyD',0,0,'C',0);
            $pdf->Cell(20,4,'My Vdc',0,0,'C',0);
            $pdf->Cell(20,4,'Current',0,0,'C',0);
            $pdf->Cell(30,4,'Min Frequency',0,0,'C',0);
            $pdf->Cell(30,4,'Max Frequency',0,1,'C',0);
            $pdf->Ln(0);

            #row of values
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,6,$model,1,0,'C',0);
            $pdf->Cell(35,6,$temp.'C',1,0,'C',0);
            $pdf->Cell(20,6, $myD.'%' ,1,0,'C',0);
            $pdf->Cell(20,6,$myvdc.'V',1,0,'C',0);
            $pdf->Cell(20,6, $myI.'A' ,1,0,'C',0);
            $pdf->Cell(30,6, $fMin.'KHz' ,1,0,'C',0);
            $pdf->Cell(30,6, $fMax.'KHz' ,1,1,'C',0);
            $pdf->Ln(2);
            $pdf->Cell( 195 , -45, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
            $pdf->Ln(3);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
            $pdf->Ln(2);

            $pdf->Output('analyse.pdf', 'F');

            $status = copy( EZ_ADMIN_PATH.'analyse.pdf', EZ_BASE_PATH.'uploads/analyse.pdf' );
            unlink( EZ_ADMIN_PATH.'analyse.pdf' );


            $file = 'analyse.pdf';
            echo $file;
        }

        /* Generate PDF for analyze tab2 */
        if ( $_POST['action'] == 'analyze_tab3_pdf' ) {

            $model  =   $_POST['modelname'];
            $myD    =   $_POST['myd']; // myd
            $myvdc  =   $_POST['myvdc']; // myvdc
            $myI    =   $_POST['myI'];
            $fMin   =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin'];
            $fMax   =   empty( $_POST['fmax'] ) ? 100 : $_POST['fmax'];
            $mytj   =   empty( $_POST['mytj'] ) ? 100 : $_POST['mytj'];
            $myrthcs=   empty( $_POST['myrthcs'] ) ? 100 : $_POST['myrthcs'];
            $mytamb =   empty( $_POST['mytamb'] ) ? 100 : $_POST['mytamb'];
            $mytsink=   empty( $_POST['mytsink'] ) ? 100 : $_POST['mytsink'];

            $pdf->Cell(35,4,'Discrete IGBT:',0,0,'C',0);
            $pdf->Cell(35,4,'Temperature:',0,0,'C',0);
            $pdf->Cell(20,4,'MyD',0,0,'C',0);
            $pdf->Cell(20,4,'My Rthcs',0,0,'C',0);
            $pdf->Cell(20,4,'Current',0,0,'C',0);
            $pdf->Cell(20,4,'Tamb',0,0,'C',0);
            $pdf->Cell(20,4,'Tsink',0,0,'C',0);
            $pdf->Cell(20,4,'My Vdc',0,1,'C',0);
            $pdf->Ln(0);

            #row of values
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,6,$model,1,0,'C',0);
            $pdf->Cell(35,6,$mytj.'C',1,0,'C',0);
            $pdf->Cell(20,6, $myD.'%' ,1,0,'C',0);
            $pdf->Cell(20,6, $myrthcs.'C/W' ,1,0,'C',0);
            $pdf->Cell(20,6, $myI.'A' ,1,0,'C',0);
            $pdf->Cell(20,6, $mytamb.'C' ,1,0,'C',0);
            $pdf->Cell(20,6, $mytsink.'C' ,1,0,'C',0);
            $pdf->Cell(20,6,$myvdc.'V',1,1,'C',0);

            $pdf->Ln(1);

            $pdf->SetFont('Arial','',9);
            $pdf->Cell( 35,4,'Min Frequency',0,0,'C',0 );
            $pdf->Cell( 35,4,'Min Frequency',0,1,'C',0 );
            $pdf->Ln(0);
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,6, $fMin.'KHz' ,1,0,'C',0);
            $pdf->Cell(35,6, $fMax.'KHz' ,1,1,'C',0);

            $pdf->Cell( 195 , -63, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
            $pdf->Ln(3);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
            $pdf->Ln(2);

            $pdf->Output('analyse.pdf', 'F');

            $status = copy( EZ_ADMIN_PATH.'analyse.pdf', EZ_BASE_PATH.'uploads/analyse.pdf' );
            unlink( EZ_ADMIN_PATH.'analyse.pdf' );

            $file = 'analyse.pdf';
            echo $file;

        }

        /* Generate PDF for analyze tab2 */
        if ( $_POST['action'] == 'analyze_tab4_pdf' ) {

            $model  =   $_POST['modelname'];
            $myD    =   $_POST['myd']; // myd
            $myvdc  =   $_POST['myvdc']; // myvdc
            $myI    =   $_POST['myI'];
            $myf    =   empty( $_POST['myf'] ) ? 0.1 : $_POST['myf'];
            $mytj   =   empty( $_POST['mytj'] ) ? 0.1 : $_POST['mytj'];

            $pdf->Cell(35,4,'Discrete IGBT:',0,0,'C',0);
            $pdf->Cell(35,4,'Temperature:',0,0,'C',0);
            $pdf->Cell(20,4,'MyD',0,0,'C',0);
            $pdf->Cell(20,4,'Current',0,0,'C',0);
            $pdf->Cell(20,4,'Frequency',0,0,'C',0);
            $pdf->Cell(20,4,'My Vdc',0,1,'C',0);
            $pdf->Ln(0);

            #row of values
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,6,$model,1,0,'C',0);
            $pdf->Cell(35,6,$mytj.'C',1,0,'C',0);
            $pdf->Cell(20,6, $myD.'%' ,1,0,'C',0);
            $pdf->Cell(20,6, $myI.'A' ,1,0,'C',0);
            $pdf->Cell(20,6, $myf.'KHz' ,1,0,'C',0);
            $pdf->Cell(20,6,$myvdc.'V',1,1,'C',0);

            $pdf->Ln(1);

            $pdf->SetFont('Arial','',9);

            $pdf->Cell( 195 , -42, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
            $pdf->Ln(3);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
            $pdf->Ln(2);

            $pdf->Output('analyse.pdf', 'F');

            $status = copy( EZ_ADMIN_PATH.'analyse.pdf', EZ_BASE_PATH.'uploads/analyse.pdf' );
            unlink( EZ_ADMIN_PATH.'analyse.pdf' );

            $file = 'analyse.pdf';
            echo $file;

        }

        /* Generate PDF for compare tab1 */
        if ( $_POST['action'] == 'compare_tab1_pdf' ){

                $model1 =   $_POST["model1"];
                $model2 =   $_POST["model2"];
                $model3 =   $_POST["model3"];
                $temp   =   $_POST['usertemp'];
                $imin   =   $_POST['imin'];
                $imax   =   $_POST['imax'];

                $pdf->Cell(35,4,'IGBT 1:',0,0,'C',0);
                $pdf->Cell(35,4,'IGBT 2:',0,0,'C',0);
                $pdf->Cell(35,4,'IGBT 3:',0,0,'C',0);
                $pdf->Cell(35,4,'Temperature:',0,0,'C',0);
                $pdf->Cell(30,4,'Min current',0,0,'C',0);
                $pdf->Cell(30,4,'Max current',0,1,'C',0);
                $pdf->Ln(0);

                #row of values
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(35,6,$model1,1,0,'C',0);
                $pdf->Cell(35,6,$model2,1,0,'C',0);
                $pdf->Cell(35,6,$model3,1,0,'C',0);
                $pdf->Cell(35,6,$temp.'C',1,0,'C',0);
                $pdf->Cell(30,6, $imin.'A' ,1,0,'C',0);
                $pdf->Cell(30,6, $imax.'A' ,1,1,'C',0);
                $pdf->Ln(2);
                $pdf->Cell( 195 , -45, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
                $pdf->Ln(3);
                $pdf->SetFont('Arial','',12);
                $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
                $pdf->Ln(2);

                $pdf->Output('compare.pdf', 'F');

                $status = copy( EZ_ADMIN_PATH.'compare.pdf', EZ_BASE_PATH.'uploads/compare.pdf' );
                unlink( EZ_ADMIN_PATH.'compare.pdf' );

                $file = 'compare.pdf';

                echo $file;

        }

        /* Generate PDF for compare tab2 */
        if ( $_POST['action'] == 'compare_tab2_pdf' ) {

            $model1 =   $_POST['modelname1'];
            $model2 =   $_POST['modelname2'];
            $model3 =   $_POST['modelname3'];
            $temp   =   $_POST['temp']; // temperature
            $myD    =   $_POST['myd']; // myd
            $myvdc  =   $_POST['myvdc']; // myvdc
            $myI    =   $_POST['myI'];
            $fMin   =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin'];
            $fMax   =   empty( $_POST['fmax'] ) ? 100 : $_POST['fmax'];

            $pdf->Cell(35,4,'Discrete IGBT 1:',0,0,'C',0);
            $pdf->Cell(35,4,'Discrete IGBT 2:',0,0,'C',0);
            $pdf->Cell(35,4,'Discrete IGBT 3:',0,0,'C',0);
            $pdf->Cell(35,4,'Temperature:',0,0,'C',0);
            $pdf->Cell(20,4,'MyD',0,0,'C',0);
            $pdf->Cell(20,4,'My Vdc',0,1,'C',0);
            $pdf->Ln(0);

            #row of values
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,6,$model1,1,0,'C',0);
            $pdf->Cell(35,6,$model2,1,0,'C',0);
            $pdf->Cell(35,6,$model3,1,0,'C',0);
            $pdf->Cell(35,6,$temp.'C',1,0,'C',0);
            $pdf->Cell(20,6, $myD.'%' ,1,0,'C',0);
            $pdf->Cell(20,6,$myvdc.'V',1,1,'C',0);
            $pdf->Ln(1);

            $pdf->SetFont('Arial','',9);
            $pdf->Cell(20,4,'Current',0,0,'C',0);
            $pdf->Cell(30,4,'Min Frequency',0,0,'C',0);
            $pdf->Cell(30,4,'Max Frequency',0,1,'C',0);
            $pdf->Ln(0);

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(20,6, $myI.'A' ,1,0,'C',0);
            $pdf->Cell(30,6, $fMin.'KHz' ,1,0,'C',0);
            $pdf->Cell(30,6, $fMax.'KHz' ,1,1,'C',0);
            $pdf->Ln(2);
            $pdf->Cell( 195 , -67, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
            $pdf->Ln(3);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
            $pdf->Ln(2);

            $pdf->Output('compare.pdf', 'F');

            $status = copy( EZ_ADMIN_PATH.'compare.pdf', EZ_BASE_PATH.'uploads/compare.pdf' );
            unlink( EZ_ADMIN_PATH.'compare.pdf' );


            $file = 'compare.pdf';
            echo $file;
        }

        /* Generate PDF for analyze tab2 */
        if ( $_POST['action'] == 'compare_tab3_pdf' ) {

            $model1 =   $_POST['modelname1'];
            $model2 =   $_POST['modelname2'];
            $model3 =   $_POST['modelname3'];
            $myD    =   $_POST['myd']; // myd
            $myvdc  =   $_POST['myvdc']; // myvdc
            $myI    =   $_POST['myI'];
            $fMin   =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin'];
            $fMax   =   empty( $_POST['fmax'] ) ? 100 : $_POST['fmax'];
            $mytj   =   empty( $_POST['mytj'] ) ? 100 : $_POST['mytj'];
            $myrthcs=   empty( $_POST['myrthcs'] ) ? 100 : $_POST['myrthcs'];
            $mytamb =   empty( $_POST['mytamb'] ) ? 100 : $_POST['mytamb'];
            $mytsink=   empty( $_POST['mytsink'] ) ? 100 : $_POST['mytsink'];

            $pdf->Cell(35,4,'Discrete IGBT 1:',0,0,'C',0);
            $pdf->Cell(35,4,'Discrete IGBT 2:',0,0,'C',0);
            $pdf->Cell(35,4,'Discrete IGBT 3:',0,0,'C',0);
            $pdf->Cell(35,4,'Temperature:',0,0,'C',0);
            $pdf->Cell(20,4,'MyD',0,0,'C',0);
            $pdf->Cell(20,4,'My Rthcs',0,1,'C',0);
            $pdf->Ln(0);

            #row of values
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(35,6,$model1,1,0,'C',0);
            $pdf->Cell(35,6,$model2,1,0,'C',0);
            $pdf->Cell(35,6,$model3,1,0,'C',0);
            $pdf->Cell(35,6,$mytj.'C',1,0,'C',0);
            $pdf->Cell(20,6, $myD.'%' ,1,0,'C',0);
            $pdf->Cell(20,6, $myrthcs.'C/W' ,1,1,'C',0);
            $pdf->Ln(1);

            $pdf->SetFont('Arial','',9);
            $pdf->Cell(25,4,'Current',0,0,'C',0);
            $pdf->Cell(25,4,'Tamb',0,0,'C',0);
            $pdf->Cell(35,4,'Tsink',0,0,'C',0);
            $pdf->Cell(35,4,'My Vdc',0,0,'C',0);
            $pdf->Cell( 35,4,'Min Frequency',0,0,'C',0 );
            $pdf->Cell( 35,4,'Min Frequency',0,1,'C',0 );
            $pdf->Ln(0);
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(25,6, $myI.'A' ,1,0,'C',0);
            $pdf->Cell(25,6, $mytamb.'C' ,1,0,'C',0);
            $pdf->Cell(35,6, $mytsink.'C' ,1,0,'C',0);
            $pdf->Cell(35,6,$myvdc.'V',1,0,'C',0);
            $pdf->Cell(35,6, $fMin.'KHz' ,1,0,'C',0);
            $pdf->Cell(35,6, $fMax.'KHz' ,1,1,'C',0);

            $pdf->Cell( 195 , -63, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
            $pdf->Ln(3);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
            $pdf->Ln(2);

            $pdf->Output('compare.pdf', 'F');

            $status = copy( EZ_ADMIN_PATH.'compare.pdf', EZ_BASE_PATH.'uploads/compare.pdf' );
            unlink( EZ_ADMIN_PATH.'compare.pdf' );

            $file = 'compare.pdf';
            echo $file;

        }

        /* Generate PDF for analyze tab2 */
        if ( $_POST['action'] == 'compare_tab4_pdf' ) {

            $model1 =   $_POST['modelname1'];
            $model2 =   $_POST['modelname2'];
            $model3 =   $_POST['modelname3'];
            $myD    =   $_POST['myd']; // myd
            $myvdc  =   $_POST['myvdc']; // myvdc
            $myI    =   $_POST['myI'];
            $myf    =   empty( $_POST['myf'] ) ? 0.1 : $_POST['myf'];
            $mytj   =   empty( $_POST['mytj'] ) ? 0.1 : $_POST['mytj'];

            $pdf->Cell(30,4,'Discrete IGBT 1:',0,0,'C',0);
            $pdf->Cell(30,4,'Discrete IGBT 2:',0,0,'C',0);
            $pdf->Cell(30,4,'Discrete IGBT 3:',0,0,'C',0);
            $pdf->Cell(30,4,'Temperature:',0,0,'C',0);
            $pdf->Cell(20,4,'MyD',0,0,'C',0);
            $pdf->Cell(20,4,'Current',0,0,'C',0);
            $pdf->Cell(20,4,'Frequency',0,0,'C',0);
            $pdf->Cell(20,4,'My Vdc',0,1,'C',0);
            $pdf->Ln(0);

            #row of values
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(30,6,$model1,1,0,'C',0);
            $pdf->Cell(30,6,$model2,1,0,'C',0);
            $pdf->Cell(30,6,$model3,1,0,'C',0);
            $pdf->Cell(30,6,$mytj.'C',1,0,'C',0);
            $pdf->Cell(20,6, $myD.'%' ,1,0,'C',0);
            $pdf->Cell(20,6, $myI.'A' ,1,0,'C',0);
            $pdf->Cell(20,6, $myf.'KHz' ,1,0,'C',0);
            $pdf->Cell(20,6,$myvdc.'V',1,1,'C',0);

            $pdf->Ln(1);

            $pdf->SetFont('Arial','',9);

            $pdf->Cell( 195 , -42, date( "Y/m/d h:i:s A",$timestamp ), 0, 0, 'R' );
            $pdf->Ln(3);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell( 240 ,2,str_repeat('_',130) , 0 , 1, 'C',0  );
            $pdf->Ln(2);

            $pdf->Output('compare.pdf', 'F');

            $status = copy( EZ_ADMIN_PATH.'compare.pdf', EZ_BASE_PATH.'uploads/compare.pdf' );
            unlink( EZ_ADMIN_PATH.'compare.pdf' );

            $file = 'compare.pdf';
            echo $file;

        }



    }

die();