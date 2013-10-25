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

if( $_POST['action'] == 'cform_submit' ){

    require_once './external-libs/PHPMailer/class.phpmailer.php';

    $fname      =   empty( $_POST['fname'] ) ? '' : $_POST['fname'];
    $lname      =   empty( $_POST['lname'] ) ? '' : $_POST['lname'];
    $email      =   empty( $_POST['mail'] ) ? '' : $_POST['mail'];
    $sbjct      =   empty( $_POST['subject'] ) ? '' : $_POST['subject'];
    $message    =   empty( $_POST['msg'] ) ? '' : $_POST['msg'];

    $error      =   '';

    if( empty( $fname ) )
        $error .= '<p class="error">Enter First Name</p>';
    elseif( !preg_match("/^[a-zA-Z'-]+$/",$fname) )
        $error .= '<p class="error">Enter Valid First Name</p>';

    if( empty( $lname ) )
        $error .= '<p class="error">Enter Last Name</p>';
    elseif( !preg_match("/^[a-zA-Z'-]+$/",$lname) )
        $error .= '<p class="error">Enter Valid Last Name</p>';

    if( empty( $email ) )
        $error .= '<p class="error">Enter Email Id</p>';
    elseif( !filter_var( $email, FILTER_VALIDATE_EMAIL) )
        $error .= '<p class="error">Enter Valid Email</p>';

    if( empty( $sbjct ) )
        $error  .=  '<p class="error">Enter Subject</p>';

    if( empty( $message ) )
        $error  .=  '<p class="error">Enter Message</p>';

    if( empty( $error ) ){

        $url        =   './Mails/Notify.html';
        $mail_HTML  =   file_get_contents( $url );

        $name = $fname.' '.$lname;

        $mail_HTML  =   str_replace('[igbt-logo]' , return_site_url(). 'assets/img/logo.png', $mail_HTML );
        $mail_HTML  =   str_replace('[igbt-name]' , $name, $mail_HTML );
        $mail_HTML  =   str_replace('[igbt-mail]' , $email, $mail_HTML );
        $mail_HTML  =   str_replace('[igbt-msg]'  , $message, $mail_HTML );

        $subject    =   $sbjct;
        $toSend     =   $email;

        $mail = new PHPMailer();

        $mail->IsSMTP();
        $mail->Host     = "192.168.0.11";

        $mail->Port       = 25; // set the SMTP port for the GMAIL server
        $mail->SMTPAuth  = TRUE;
        $mail->Username   = "ankit.gade@vidushigoc.com"; // SMTP account username
        $mail->Password   = "p@ssword";// SMTP account password

        $mail->AddReplyTo("ankit.gade@vidushigoc.com","Ankit Gade");
        $mail->SetFrom('ankit.gade@vidushigoc.com', 'Ankit Gade');

        $address = "ankit.gade@vidushigoc.com";
        $mail->AddAddress($address, "Sumit");

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

die(1);