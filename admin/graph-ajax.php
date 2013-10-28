<?php
/*
 * Ajax file for handling graph requests
 * 
 */

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

// Handle Request for tab1 graph1
if ( $_POST['action'] == 'tab1_graph1' ) {

    global $EZ_DB;

    $result_data = array(
        'error' => false,
        'error_msg' => 'Error!',
        'data' => array()
    );

    $error_msg = graph_error_msgs();

    $modelNo    =   $_POST['modal_id'];
    $userTemp   =   $_POST['mytj'];
    $currentMin =   $_POST['imin'];
    $currentMax =   $_POST['imax'];

    if (empty($modelNo) || empty($userTemp)) {

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[1];
        echo json_encode($result_data);
        die();

    }

    $query = "SELECT i_rated, tjref, vttjmax, atjmax, btjmax, vt, a, b, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$modelNo'";
    $result =   $EZ_DB->run_query( $query );

    /* If model is present as such */
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

        $user_plot_rang = FALSE;

        //Validate Imin or Imax value
        if (!empty($currentMax) || !empty($currentMin) || $currentMin === '0') {

            $currentMin = (int) $currentMin;
            $currentMax = (int) $currentMax;
            
            if ( ($currentMax <= 0) || (($iRated*4) < $currentMax) || ($currentMin >= $currentMax) || ($currentMin < 0) ) {
                $result_data['error'] = true;
                if (($currentMin >= $currentMax)) {

                    $result_data['error_msg'] = $error_msg[7];
                }else
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

        }else {//else calculate the cordinate with 2 times iRated

            $range = $iRated * 2;
            $plotting = $range / 20;

            while( $range >= 0 ){

                $iCordinates[] = $range;
                $range = $range - $plotting;

            }
        }

        $iCordinates = array_reverse($iCordinates);

        if ( !($userTemp <= $tjMax) || !($userTemp >= 25) ) {

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[3] . $tjMax;
            echo json_encode($result_data);
            die();

        }

        /* At this point we will get Irated ranges */

        /********************** Calculation For VceOn *******************************/

        /* Room temp calculations */
        $vceonRoom = $vceonMax = $vceonUser = $main_array = $main_array_vcon =  array(); // multiple initialization
        $EtsRoom = $EtsMax = $EtsUser = $main_array_ets = array(); // multiple initialization

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

            $iPowerkMax     =   pow( $temp, $kTjMax );// 'm' TjMax exponantial
            $iPowernMax     =   pow( $temp, $nTjMax );// 'n' TjMax exponantial
            $iPowerkRoom    =   pow( $temp, $kTjRoom );// 'm' TjRoom exponantial
            $iPowernRoom    =   pow( $temp, $nTjRoom );// 'n' TjRoom exponantial

            $EtsMax[]       =   ( $hTjMax * $iPowerkMax ) + ( $mTjMax * $iPowernMax );
            $EtsRoom[]      =   ( $hTjRoom * $iPowerkRoom ) + ( $mTjRoom * $iPowernRoom );

            /************* Calculations for Ets Ends *****************/

        }

        /* Now calculate for user temperature */

        /* Formula: Vn = V1+ ([V2-V1]/[T2-T1])*(Tn-T1)) */
        foreach( $vceonMax as $key => $MaxVoltage ){

            $calculate      =   $vceonRoom[$key] + ( ( ( $MaxVoltage - $vceonRoom[$key] ) / ( $tjMax - 25 ) ) * ( $userTemp - 25 ) );
            $vceonUser[]    =   $calculate;
        }

        /* Formula:  ETS = Etsroom +  ( ( [Etsmax - Etsroom ] / [ Tjmax - Temproom ] ) * ( TempUser - 25 ) )  */
        foreach( $EtsMax as $key=>$MaxEts ){

            $calculate      =   $EtsRoom[$key] + ( ( ( $MaxEts - $EtsRoom[$key] ) / ( $tjMax -25  ) ) * ( $userTemp - 25 ) );
            $EtsUser[]      =   $calculate;

        }

        /* Create data format */
        foreach ( $iCordinates as $key => $value ){

            //Example format of ouput: [[0,0],[1.26,6],[1.60,10],[1.80,9],[2,14]]
            $points[0] = $value;

            //VCon for user
            $points[1] = $vceonUser[$key];
            $main_array_vcon[0][] = $points;

            //VCon for room
            $points[1] = $vceonRoom[$key];
            $main_array_vcon[1][] = $points;

            //VCon for max
            $points[1] = $vceonMax[$key];
            $main_array_vcon[2][] = $points;

            //VCon for user
            $points[1] = $EtsUser[$key];
            $main_array_ets[0][] = $points;

            //VCon for room
            $points[1] = $EtsRoom[$key];
            $main_array_ets[1][] = $points;

            //VCon for max
            $points[1] = $EtsMax[$key];
            $main_array_ets[2][] = $points;

        }

        $main_array[0] = $main_array_vcon;
        $main_array[1] = $main_array_ets;
        $result_data['data'] = $main_array;
        echo json_encode($result_data);
        die();

    } else {

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[2];
        echo json_encode($result_data);
        die();

    }

}

/**
 * For calculating tab2 of analyze.
 */
if( $_POST['action'] == 'tab2_graph1' ){

    $error_msg   = graph_error_msgs();
    $result_data['error'] = false;
    $result_data['error_msg'] = 'Error!';

    $modelNo    =   $_POST['model_name'];
    $mytj       =   $_POST['mytj'];
    $myD        =   $_POST['myd'];
    $myI        =   empty($_POST['myi']) ? 0 : $_POST['myi'];
    $myvdc      =   empty($_POST['myvdc']) ? 0 : $_POST['myvdc'];

    $fMin       =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin']; // min value can be 0.1
    $fMax       =   empty( $_POST['fmax'] ) ? 100 : $_POST['fmax']; // max value can be 100

    if( ( $fMin <= 0 ) || ( $fMin >100 ) || ( $fMax > 100 ) || ( $fMax <= 0 ) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[6];
        echo json_encode( $result_data );
        die;

    }

    $frequencyDiff      =   ($fMax - $fMin);
    $frequencyRange     =   $frequencyDiff / 10; // plot the ranges of frequencies. for plotting 10 points

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
            echo json_encode( $result_data );
            die;

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

        echo json_encode( $result_data );

    }

}

/**
 * Compare menu graph tab-1
 */
if( $_POST['action'] == 'compare_tab1' ){

    global $EZ_DB;

    $error_msg = graph_error_msgs();

    $model1 = empty($_POST['modal_id1']) ? false : $_POST['modal_id1'];
    $model2 = empty($_POST['modal_id2']) ? false : $_POST['modal_id2'];
    $model3 = empty($_POST['modal_id3']) ? false : $_POST['modal_id3'];

    /* Check if all models are selected */
    if( !($model1 && $model2 && $model3) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[5];
        echo json_encode($result_data);
        die;

    }

    $userTemp   = $_POST['mytj'];
    $currentMin = $_POST['imin'];
    $currentMax = $_POST['imax'];

    /* Check if minimum and max currents are ok */
    $query  = "SELECT min(i_rated) as mincurrent FROM models WHERE model_name IN ( '".$model1."', '".$model2."', '".$model3."' )";
    $result = $EZ_DB->run_query( $query );

    if( $result ){

        if( ( $currentMax > ( 4 * $result['mincurrent'] ) ) ){

            $result_data['error'] = true;
            $maxValue = ( 4 * $result['mincurrent'] );
            $result_data['error_msg'] = $error_msg[4].$maxValue ;
            echo json_encode($result_data);
            die;

        }
    }

    $argsModel1 = array( 'model_id'=>$model1, 'currentMax'=>$currentMax, 'currentMin'=>$currentMin, 'userTemp'=>$userTemp );
    $argsModel2 = array( 'model_id'=>$model2, 'currentMax'=>$currentMax, 'currentMin'=>$currentMin, 'userTemp'=>$userTemp );
    $argsModel3 = array( 'model_id'=>$model3, 'currentMax'=>$currentMax, 'currentMin'=>$currentMin, 'userTemp'=>$userTemp );

    $vceonModel1 = calculate_Vceon( $argsModel1 );
    $vceonModel2 = calculate_Vceon( $argsModel2 );
    $vceonModel3 = calculate_Vceon( $argsModel3 );

    $etsModel1 = calculate_ets ( $argsModel1 );
    $etsModel2 = calculate_ets ( $argsModel2 );
    $etsModel3 = calculate_ets ( $argsModel3 );

    $main_array_vcon[0] = $vceonModel1['data'];
    $main_array_vcon[1] = $vceonModel2['data'];
    $main_array_vcon[2] = $vceonModel3['data'];

    $main_array_ets[0] = $etsModel1['data'];
    $main_array_ets[1] = $etsModel2['data'];
    $main_array_ets[2] = $etsModel3['data'];

    $result_data['error'] = false;
    $result_data['error_msg'] = 'Error!';
    $result_data['data'][0] = $main_array_vcon;
    $result_data['data'][1] = $main_array_ets;

    echo json_encode($result_data);

}

/**
 * Compare tab 2 ajax request processing
 */
if( $_POST['action'] == 'compare_tab2' ){

    global $EZ_DB;

    $error_msg = graph_error_msgs();

    $model1 = empty($_POST['modal_id1']) ? false : $_POST['modal_id1'];
    $model2 = empty($_POST['modal_id2']) ? false : $_POST['modal_id2'];
    $model3 = empty($_POST['modal_id3']) ? false : $_POST['modal_id3'];

    /* Check if all models are selected */
    if( !($model1 && $model2 && $model3) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[5];
        echo json_encode($result_data);
        die;

    }

    $userTemp   =   $_POST['mytj'];
    $userI      =   empty($_POST['myi']) ? 0 : $_POST['myi'];
    $userd      =   $_POST['myd'];
    $uservdc    =   $_POST['myvdc'];
    $freqMin    =   empty($_POST['fmin']) ? 0.1 : $_POST['fmin'];
    $freqMax    =   empty($_POST['fmax']) ? 100 : $_POST['fmax'];
    
    $ploss1 = calculate_ploss( array( 'modelNo'=>$model1, 'mytj' => $userTemp, 'myD'=> $userd, 'myI' => $userI, 'myvdc'=>$uservdc, 'fMin'=>$freqMin, 'fMax'=>$freqMax ) );
    $ploss2 = calculate_ploss( array( 'modelNo'=>$model2, 'mytj' => $userTemp, 'myD'=> $userd, 'myI' => $userI, 'myvdc'=>$uservdc, 'fMin'=>$freqMin, 'fMax'=>$freqMax ) );
    $ploss3 = calculate_ploss( array( 'modelNo'=>$model3, 'mytj' => $userTemp, 'myD'=> $userd, 'myI' => $userI, 'myvdc'=>$uservdc, 'fMin'=>$freqMin, 'fMax'=>$freqMax ) );
    
    $main_array_ploss[0] = $ploss1['data'];
    $main_array_ploss[1] = $ploss2['data'];
    $main_array_ploss[2] = $ploss3['data'];
    
    $result_data['error'] = false;
    $result_data['error_msg'] = 'Error!';
    $result_data['data'] = $main_array_ploss;

    echo json_encode($result_data);


}
die(1);
