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

                if( $range == 0 )
                    $perfectZero = true;

            }

        }else {//else calculate the cordinate with 2 times iRated

            $range = $iRated * 2;
            $plotting = $range / 20;

            while( $range >= 0 ){

                $iCordinates[] = $range;
                $range = $range - $plotting;
                
                if( $range == 0 )
                    $perfectZero = true;

            }
        }

        if( !isset( $perfectZero ) ){

            $iCordinates[] = 0;
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

    if( $myD < 0 || $myD > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;

    }

    if( ( $fMin <= 0 ) || ( $fMin >100 ) || ( $fMax > 100 ) || ( $fMax <= 0 ) || ($fMax <= $fMin) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[6];
        echo json_encode( $result_data );
        die;

    }

    $frequencyDiff      =   ( $fMax - $fMin );
    if( $fMax > 10 )
        $frequencyRange     =   $frequencyDiff / 20; // plot the ranges of frequencies. for plotting 10 points
    else
        $frequencyRange     =   $frequencyDiff / 32;

    $query = "SELECT i_rated, tjref, vref, vttjmax, atjmax, btjmax, vt, a, b, htjmax, ktjmax, mtjmax, ntjmax, h, k, m, n FROM models where model_name='$modelNo'";
    $result =   $EZ_DB->run_query( $query );

    if( $result ){

        $tjMax  =   $result['tjref'];

        if( ( $mytj < 25 ) || ( $mytj > $tjMax ) ){

            $result_data['error'] = true;
            $result_data['error_msg'] = $error_msg[3].$tjMax;
            echo json_encode( $result_data );
            die;

        }

        $vref   =   $result['vref'];
        /* range */
        $iRated =   $result['i_rated'];

        if( ( $myI < 0 ) || ( $myI > ( 4 * $iRated ) ) ){

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

        $VcoenTj = calculate_vceon_single_temp( array( 'myI'=>$myI, 'bMax'=>$bMax, 'aMax'=>$aMax, 'vtMax'=>$vtMax, 'bRoom'=>$bRoom, 'aRoom'=>$aRoom, 'vtRoom'=>$vtRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) );
        /* Calculate Ets at Tj Formula:  ETS = Etsroom +  ( ( [Etsmax - Etsroom ] / [ Tjmax - Temproom ] ) * ( TempUser - 25 ) )  */
        $EtsTj = calculate_ets_single_temp( array( 'myI'=>$myI, 'kTjMax'=>$kTjMax, 'nTjMax'=>$nTjMax, 'hTjMax'=>$hTjMax, 'mTjMax'=>$mTjMax, 'kTjRoom'=>$kTjRoom, 'nTjRoom'=>$nTjRoom, 'hTjRoom'=> $hTjRoom, 'mTjRoom'=>$mTjRoom, 'tjMax'=>$tjMax, 'mytj'=>$mytj ) ); // This is Ets at Tj

        $plosses  =  $points    =   array();

        while( $fMax >= $fMin ){

            $prevVal    =   $fMax;
            $fMax       =   (int)$fMax;

            if( !$fMax )
                $fMax = $prevVal;

            $calculate = ( ( $myD/100 ) * ( $VcoenTj * $myI ) ) + ( ( $myvdc / $vref ) * ( $EtsTj * $fMax * ( 1000 / 1000000 ) ) );
            //echo $myD.'--'.$VcoenTj.'--'.$myI.'--'.$myvdc.'--'.$vref.'--'.$EtsTj.'--'.$fMax; die;
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

    /* Check if dis */

    /* Check if all models are selected */
    if( !($model1 && $model2 && $model3) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[5];
        echo json_encode($result_data);
        die;

    }

    $userTemp   = $_POST['mytj'];
    $currentMin = empty($_POST['imin']) ? 0 : $_POST['imin'];
    $currentMax = empty($_POST['imax']) ? 4 : $_POST['imax'];
    $validateCurrent = $_POST['currentvalidate'];

    /* Check if minimum and max currents are ok */
    $query  = "SELECT max(i_rated) as mincurrent FROM models WHERE model_name IN ( '".$model1."', '".$model2."', '".$model3."' )";
    $result = $EZ_DB->run_query( $query );

    if( $result ){

        if( ( $currentMax > ( 4 * $result['mincurrent'] ) ) ){

            $result_data['error'] = true;
            $maxValue = ( 4 * $result['mincurrent'] );
            $result_data['error_msg'] = $error_msg[4]. $maxValue;
            echo json_encode($result_data);
            die;

        }
    }

    $argsModel1 = array( 'model_id'=>$model1, 'currentMax'=>$currentMax, 'currentMin'=>$currentMin, 'userTemp'=>$userTemp, 'isCompare'=>true, 'currmaxallow'=> (empty( $validateCurrent )) ? ( 4 * $result['mincurrent'] ) : $currentMax );
    $argsModel2 = array( 'model_id'=>$model2, 'currentMax'=>$currentMax, 'currentMin'=>$currentMin, 'userTemp'=>$userTemp, 'isCompare'=>true, 'currmaxallow'=> (empty( $validateCurrent )) ? ( 4 * $result['mincurrent'] ) : $currentMax );
    $argsModel3 = array( 'model_id'=>$model3, 'currentMax'=>$currentMax, 'currentMin'=>$currentMin, 'userTemp'=>$userTemp, 'isCompare'=>true, 'currmaxallow'=> (empty( $validateCurrent )) ? ( 4 * $result['mincurrent'] ) : $currentMax );

    $vceonModel1 = calculate_Vceon( $argsModel1 );
    $vceonModel2 = calculate_Vceon( $argsModel2 ); //print_r($vceonModel2);
    $vceonModel3 = calculate_Vceon( $argsModel3 ); //print_r($vceonModel3); die;

    $etsModel1 = calculate_ets ( $argsModel1 );//print_r($argsModel1);
    $etsModel2 = calculate_ets ( $argsModel2 );//print_r($argsModel2);
    $etsModel3 = calculate_ets ( $argsModel3 );//print_r($argsModel3); die;

    $allArray = array( $vceonModel1, $vceonModel2, $vceonModel3, $etsModel1, $etsModel2, $etsModel3 );

    //db( $allArray );

    foreach( $allArray as $key=>$value ){

        if( $value['error'] == true ):
            echo json_encode( $value );
            die;
        endif;
    }

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

    if( $userd < 0 || $userd > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;

    }

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

/* Analyze graph 4 */
if( $_POST['action'] == 'tab4-graph1' ){

    $model  =   empty( $_POST['modal_id']) ? false : $_POST['modal_id'];
    $mytj   =   empty( $_POST['mytj'] ) ? 0 : $_POST['mytj'];
    $myD    =   empty( $_POST['myd'] ) ? 0 : $_POST['myd'];
    $myF    =   empty( $_POST['myf'] ) ? 0.1 : $_POST['myf'];
    $myvdc  =   empty( $_POST['myvdc'] ) ? 0 : $_POST['myvdc'];
    $myI    =   empty( $_POST['myi'] ) ? 0 : $_POST['myi'];

    $args = array( 'model'=>$model, 'myI'=>$myI, 'mytj'=>$mytj, 'myD'=>$myD, 'myf'=>$myF, 'myvdc'=>$myvdc );
    $splitLoss = calculate_split_loss( $args );

    echo json_encode($splitLoss);

}

/* Analyze tab 3 graph */
if( $_POST['action'] == 'analyze_tab3' ){

    $model  =   empty( $_POST['modal_id']) ? false : $_POST['modal_id'];
    $mytj   =   empty( $_POST['mytj'] ) ? 0 : $_POST['mytj'];
    $myD    =   empty( $_POST['myd'] ) ? 0 : $_POST['myd'];
    $myRthcs=   empty( $_POST['myrthcs'] ) ? 0.1 : $_POST['myrthcs'];
    $myvdc  =   empty( $_POST['myvdc'] ) ? 0.1 : $_POST['myvdc'];
    $tAmb   =   empty( $_POST['mytamb'] ) ? 0 : $_POST['mytamb'];
    $tSink  =   empty( $_POST['mytsink'] ) ? 0 : $_POST['mytsink'];
    $fmin   =   empty( $_POST['fmin'] ) ? 0 : $_POST['fmin'];
    $fMax   =   empty( $_POST['fmax'] ) ? 0 : $_POST['fmax'];
    $myI    =   empty( $_POST['myI'] ) ? 0 : $_POST['myI'];

    $error_msg = graph_error_msgs();
    $result_data = array( 'error' => true, 'error_msg' => $error_msg[11], 'data' => array() );

    if( $myD < 0 || $myD > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;

    }

    /* A validation line */
    if( ($mytj < $tSink) || ( $tSink < $tAmb ) || ( $mytj < $tAmb ) ){

        $result_data = array('error_msg' => $error_msg[11]);
        echo json_encode( $result_data );
        die;
    }

    $heatSink   =   calculate_heat_sink( array( 'model'=>$model, 'myI'=>$myI, 'mytj'=>$mytj, 'myD'=>$myD,
                                        'myvdc'=>$myvdc, 'tAmb'=>$tAmb, 'tSink'=>$tSink, 'fmin'=>$fmin, 
                                        'fmax'=>$fMax, 'myRthcs'=>$myRthcs ) );

    echo $heatSink;

}

/**
 * Compare tab3
 */
if( $_POST['action'] == 'tab3-graph1' ){

    $result_data = array( 'error'=>false, 'error_msg'=>'', 'data'=>'' );

    $error_msg = graph_error_msgs();

    $model1     =   empty( $_POST['modal_id1']) ? false : $_POST['modal_id1'];
    $model2     =   empty( $_POST['modal_id2']) ? false : $_POST['modal_id2'];
    $model3     =   empty( $_POST['modal_id3']) ? false : $_POST['modal_id3'];
    $mytj       =   empty( $_POST['mytj'] ) ? 0 : $_POST['mytj'];
    $myD        =   empty( $_POST['myd'] ) ? 0 : $_POST['myd'];
    $myRthcs    =   empty( $_POST['myrthcs'] ) ? 0.1 : $_POST['myrthcs'];
    $myvdc      =   empty( $_POST['myvdc'] ) ? 0.1 : $_POST['myvdc'];
    $tAmb       =   empty( $_POST['mytamb'] ) ? 0 : $_POST['mytamb'];
    $tSink      =   empty( $_POST['mytsink'] ) ? 0 : $_POST['mytsink'];
    $fmin       =   empty( $_POST['fmin'] ) ? 0 : $_POST['fmin'];
    $fMax       =   empty( $_POST['fmax'] ) ? 0 : $_POST['fmax'];
    $myI        =   empty( $_POST['myI'] ) ? 0 : $_POST['myI'];

    /* Check if all models are selected */
    if( !($model1 && $model2 && $model3) ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[5];
        echo json_encode($result_data);
        die;
    }

    if( $myD < 0 || $myD > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;
    }

    $heatSink1   =   calculate_heat_sink( array( 'model'=>$model1, 'myI'=>$myI, 'mytj'=>$mytj, 'myD'=>$myD,
                                    'myvdc'=>$myvdc, 'tAmb'=>$tAmb, 'tSink'=>$tSink, 'fmin'=>$fmin, 
                                    'fmax'=>$fMax, 'myRthcs'=>$myRthcs, 'returnRaw'=>true ) );

    $heatSink2   =   calculate_heat_sink( array( 'model'=>$model2, 'myI'=>$myI, 'mytj'=>$mytj, 'myD'=>$myD,
                                    'myvdc'=>$myvdc, 'tAmb'=>$tAmb, 'tSink'=>$tSink, 'fmin'=>$fmin, 
                                    'fmax'=>$fMax, 'myRthcs'=>$myRthcs, 'returnRaw'=>true ) );

    $heatSink3   =   calculate_heat_sink( array( 'model'=>$model3, 'myI'=>$myI, 'mytj'=>$mytj, 'myD'=>$myD,
                                    'myvdc'=>$myvdc, 'tAmb'=>$tAmb, 'tSink'=>$tSink, 'fmin'=>$fmin, 
                                    'fmax'=>$fMax, 'myRthcs'=>$myRthcs, 'returnRaw'=>true ) );

    $result_data['data'] = array( 0=>$heatSink1['data'] , 1=>$heatSink2['data'] , 2=>$heatSink3['data'] );

    echo json_encode( $result_data );
    die;
}

/* Compare graph 4 */
if( $_POST['action'] == 'compare_tab4' ){

    $result_data = array( 'error'=>false, 'error_msg'=>'', 'data'=>'' );
    $error_msg = graph_error_msgs();

    $model1 =   empty( $_POST['modal_id1']) ? false : $_POST['modal_id1'];
    $model2 =   empty( $_POST['modal_id2']) ? false : $_POST['modal_id2'];
    $model3 =   empty( $_POST['modal_id3']) ? false : $_POST['modal_id3'];
    $mytj   =   empty( $_POST['mytj'] ) ? 0 : $_POST['mytj'];
    $myD    =   empty( $_POST['myd'] ) ? 0 : $_POST['myd'];
    $myF    =   empty( $_POST['myf'] ) ? 0.1 : $_POST['myf'];
    $myvdc  =   empty( $_POST['myvdc'] ) ? 0 : $_POST['myvdc'];
    $myI    =   empty( $_POST['myi'] ) ? 0 : $_POST['myi'];

    if( $myD < 0 || $myD > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;
    }

    $args = array( 'model'=>$model1, 'myI'=>$myI, 'mytj'=>$mytj, 'myD'=>$myD, 'myf'=>$myF, 'myvdc'=>$myvdc );

    $splitLoss1 = calculate_split_loss( $args );

    $args['model'] = $model2;
    $splitLoss2 = calculate_split_loss( $args );

    $args['model'] = $model3;
    $splitLoss3 = calculate_split_loss( $args );

    $FinalPoints = array();
    $FinalPoints[0] = array( array( 1, $splitLoss1['data'][0][1] ), array( 5, $splitLoss1['data'][1][1] ), array(9, $splitLoss1['data'][2][1] ), array( 13, $splitLoss1['data'][3][1] ) );
    $FinalPoints[1] = array( array( 2, $splitLoss2['data'][0][1] ), array( 6, $splitLoss2['data'][1][1] ), array( 10, $splitLoss2['data'][2][1] ), array( 14, $splitLoss2['data'][3][1] ) );
    $FinalPoints[2] = array( array( 3, $splitLoss3['data'][0][1] ), array( 7, $splitLoss3['data'][1][1] ), array( 11, $splitLoss3['data'][2][1] ), array( 15, $splitLoss3['data'][3][1] ) );

    $result_data['data'] = $FinalPoints;

    echo json_encode($result_data);

}

/* Analyze tab 5 */
if( $_POST['action'] == 'analyze_tab5' ){

    $result_data = array( 'error'=>false, 'error_msg'=>'', 'data'=>'' );
    $error_msg = graph_error_msgs();

    $model  =   empty( $_POST['modal_id']) ? false : $_POST['modal_id'];
    $mytj   =   empty( $_POST['mytj'] ) ? 0 : $_POST['mytj'];
    $myD    =   empty( $_POST['myd'] ) ? 0 : $_POST['myd'];
    $fmin   =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin'];
    $fmax   =   empty( $_POST['fmax'] ) ? 0.1 : $_POST['fmax'];
    $myvdc  =   empty( $_POST['myvdc'] ) ? 0 : $_POST['myvdc'];
    $tsink  =   empty( $_POST['tsink'] ) ? 0 : $_POST['tsink'];
    $myrthcs=   empty( $_POST['myrthcs'] ) ? 0 : $_POST['myrthcs'];

    if( $myD < 0 || $myD > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;
    }

    /* Temperature validation */
    if( $mytj < $tsink ){
        $result_data = array( 'error'=>true, 'error_msg'=>'Please enter Tj > Tsink', 'data'=>'' );
        echo json_encode($result_data);
        die;
    }

    /* Check for valid frequency range */
    if( $fmax < $fmin ){
        $result_data = array( 'error'=>true, 'error_msg'=>$error_msg[12], 'data'=>'' );
        echo json_encode($result_data);
        die;
    }

    $resultData =   calculate_i_vs_f( array( 'model'=>$model, 'mytj'=>$mytj, 'myD'=>$myD, 'fmin'=>$fmin, 'fmax'=>$fmax, 'myvdc'=>$myvdc, 'tsink'=>$tsink, 'myrthcs'=>$myrthcs ) ); 

    echo json_encode($resultData);

}

/* Compare tab 5 */
if( $_POST['action'] == 'compare_tab5' ){

    set_time_limit(0);

    $result_data = array( 'error'=>false, 'error_msg'=>'', 'data'=>'' );
    $error_msg = graph_error_msgs();

    $model1 =   empty( $_POST['modal_id1']) ? false : $_POST['modal_id1'];
    $model2 =   empty( $_POST['modal_id2']) ? false : $_POST['modal_id2'];
    $model3 =   empty( $_POST['modal_id3']) ? false : $_POST['modal_id3'];
    $mytj   =   empty( $_POST['mytj'] ) ? 0 : $_POST['mytj'];
    $myD    =   empty( $_POST['myd'] ) ? 0 : $_POST['myd'];
    $fmin   =   empty( $_POST['fmin'] ) ? 0.1 : $_POST['fmin'];
    $fmax   =   empty( $_POST['fmax'] ) ? 0.1 : $_POST['fmax'];
    $myvdc  =   empty( $_POST['myvdc'] ) ? 0 : $_POST['myvdc'];
    $tsink  =   empty( $_POST['tsink'] ) ? 0 : $_POST['tsink'];
    $myrthcs=   empty( $_POST['myrthcs'] ) ? 0 : $_POST['myrthcs'];

    if( $myD < 0 || $myD > 100 ){

        $result_data['error'] = true;
        $result_data['error_msg'] = $error_msg[13];
        echo json_encode( $result_data );
        die;
    }

    /* Temperature validation */
    if( $mytj < $tsink ){
        $result_data = array( 'error'=>true, 'error_msg'=>'Please enter Tj > Tsink', 'data'=>'' );
        echo json_encode($result_data);
        die;
    }

    /* Check for valid frequency range */
    if( $fmax < $fmin ){
        $result_data = array( 'error'=>true, 'error_msg'=>$error_msg[12], 'data'=>'' );
        echo json_encode($result_data);
        die;
    }

    $resultData1 =   calculate_i_vs_f( array( 'model'=>$model1, 'mytj'=>$mytj, 'myD'=>$myD, 'fmin'=>$fmin, 'fmax'=>$fmax, 'myvdc'=>$myvdc, 'tsink'=>$tsink, 'myrthcs'=>$myrthcs ) ); 

    $resultData2 =   calculate_i_vs_f( array( 'model'=>$model2, 'mytj'=>$mytj, 'myD'=>$myD, 'fmin'=>$fmin, 'fmax'=>$fmax, 'myvdc'=>$myvdc, 'tsink'=>$tsink, 'myrthcs'=>$myrthcs ) ); 
    $resultData3 =   calculate_i_vs_f( array( 'model'=>$model3, 'mytj'=>$mytj, 'myD'=>$myD, 'fmin'=>$fmin, 'fmax'=>$fmax, 'myvdc'=>$myvdc, 'tsink'=>$tsink, 'myrthcs'=>$myrthcs ) ); 
   
    $result_data['data'][0] = $resultData1['data'];
    $result_data['data'][1] = $resultData2['data'];
    $result_data['data'][2] = $resultData3['data'];
    
    echo json_encode($result_data);
    
}

/**
 * Draw recommend table
 */
if( $_POST['action'] == 'recommend' ){

    global $EZ_DB;

    $result_data = array( 'error'=>false, 'error_msg'=>'', 'data'=>'' );
    $error_msg = graph_error_msgs();

    $response   =   $_POST['data'];
    $data       =   parse_str( $_POST['data'], $response );

    $maxTref = $EZ_DB->run_query("SELECT MAX(tjref) as tref FROM models");
    $maxTref = (int)$maxTref['tref'];

    /* Validate all the things first */

    /* Validate frequency */
    if( empty( $response['myf'] ) || $response['myf'] > 100 ){

        $result_data = array( 'error'=>true, 'error_msg'=>'Please enter frequency between 0 to 100', 'data'=>'' );
        echo json_encode($result_data);
        die;
    }

    /* Validate myTj */
    if( empty( $response['myvdc'] ) || $response['myvdc'] < 100 || $response['myvdc'] > 900 ){

        $result_data = array( 'error'=>true, 'error_msg'=>'Select VDC between 100V to 900V', 'data'=>'' );
        echo json_encode( $result_data );
        die;
    }

    /* Validate myTj */
    if( empty( $response['mytj'] ) || $response['mytj'] > $maxTref ){

        $result_data = array( 'error'=>true, 'error_msg'=>'Please enter junction temperature between 0 to '.$maxTref, 'data'=>'' );
        echo json_encode( $result_data );
        die;
    }

    /* Validate Tcase */
    if( empty( $response['mytcase'] ) || ( $response['mytcase'] > $response['mytj'] ) ) {

        $result_data = array( 'error'=>true, 'error_msg'=>'Tcase should be 25 < Tcase < Tj', 'data'=>'' );
        echo json_encode($result_data);
        die;
    }

    if( $response['myvdc'] > 100 && $response['myvdc'] <= 120 )        
        $condition = ' WHERE v_rated <= 250';
    elseif( $response['myvdc'] > 120 && $response['myvdc'] <= 200 )
        $condition = ' WHERE v_rated <= 350 AND v_rated > 250';
    elseif( $response['myvdc'] > 200 && $response['myvdc'] <= 400 )
        $condition = ' WHERE v_rated <= 650 AND v_rated > 350';
    elseif( $response['myvdc'] > 400 && $response['myvdc'] <= 600 )
        $condition = ' WHERE v_rated <= 1000 AND v_rated >= 651';
    elseif( $response['myvdc'] > 600 && $response['myvdc'] <= 900 )
        $condition = ' WHERE v_rated >= 1000';

    $query  =   "SELECT * FROM models ".$condition.' AND include_model = 1';

    $result = $EZ_DB->run_query( $query, 1 );
    $count = 0;

    $allTjs = $allmodels = $allPloss = $allPconds = $allPsw = $allDeltaTj = array();

    while( $row = mysqli_fetch_assoc($result) ){

        /* range */
        $iRated =   $row['i_rated'];
        $tjMax  =   $row['tjref'];
        $vref = $row['vref'];

        /* For VceON */
        $vtRoom = $row['vt'];
        $aRoom  = $row['a'];
        $bRoom  = $row['b'];

        $vtMax  = $row['vttjmax'];
        $aMax   = $row['atjmax'];
        $bMax   = $row['btjmax'];
        /* For VceON */

        /* For Ets */
        $hTjMax =   $row['htjmax'];
        $kTjMax =   $row['ktjmax'];
        $mTjMax =   $row['mtjmax'];
        $nTjMax =   $row['ntjmax'];

        $hTjRoom    =   $row['h'];
        $kTjRoom    =   $row['k'];
        $mTjRoom    =   $row['m'];
        $nTjRoom    =   $row['n'];
        /* For Ets */

        if( empty( $vref ) || ( $tjMax < $response['mytj'] ) )
            continue;

        $VcoenTj    = calculate_vceon_single_temp( array( 'myI'=>$response['myI'], 'bMax'=>$bMax, 'aMax'=>$aMax, 
                        'vtMax'=>$vtMax, 'bRoom'=>$bRoom, 'aRoom'=>$aRoom, 'vtRoom'=>$vtRoom, 'tjMax'=>$tjMax, 
                        'mytj'=>$response['mytj'] ) );

        $EtsTj      = calculate_ets_single_temp( array( 'myI'=>$response['myI'], 'kTjMax'=>$kTjMax, 'nTjMax'=>$nTjMax, 
                        'hTjMax'=>$hTjMax, 'mTjMax'=>$mTjMax, 'kTjRoom'=>$kTjRoom, 'nTjRoom'=>$nTjRoom, 
                        'hTjRoom'=> $hTjRoom, 'mTjRoom'=>$mTjRoom, 'tjMax'=>$tjMax, 'mytj'=>$response['mytj'] ) ); // This is Ets at Tj

        $Pconds  =   ( $response['myd'] / 100 ) * ( ( $VcoenTj ) * $response['myI'] );
        $Psw     =   ( $response['myvdc'] / $vref ) * ( $EtsTj * $response['myf'] * (1000 / 1000000) );
        $ploss   =   $Pconds + $Psw;
        // Calculate Tj
        $calcTj = $row['rthjc_igbt'] * $ploss + $response['mytcase'];

        if( $calcTj > $tjMax )
            continue;

        $DeltaTj =   ( $calcTj - $response['mytcase'] );

        array_push( $allTjs, $calcTj );
        array_push( $allmodels, $row['model_name'] );
        array_push( $allPloss, $ploss );
        array_push( $allPconds, $Pconds );
        array_push( $allPsw, $Psw );
        array_push( $allDeltaTj, $DeltaTj );
    }

    $models = $plosses = $pconds = $psws = $deltaTjs = array();

    if( !empty( $allTjs ) ){

        $iterate = 0;

        foreach( $allTjs as $k=>$v ){

            if( $iterate > 6 )
                break;

            $closest = vit_getClosest( $response['mytj'] , $allTjs );
            $getKey = array_search( $closest , $allTjs );
            $model = $allmodels[$getKey];
            $plossVal = $allPloss[$getKey];

            array_push( $models, $model );
            array_push( $plosses, $plossVal );
            array_push( $pconds, $allPconds[$getKey] );
            array_push( $psws, $allPsw[$getKey] );
            array_push( $deltaTjs, $allDeltaTj[$getKey] );

            unset( $allTjs[$getKey] );
            unset( $allmodels[$getKey] );
            unset( $allPloss[$getKey] );
            unset( $allPconds[$getKey] );
            unset( $allPsw[$getKey] );
            unset( $allDeltaTj[$getKey] );

            $iterate++;

        }

        $result_data['data']    =   array('models'=>$models, 'plosses'=>$plosses, 'pconds'=>$pconds, 'psws'=>$psws, 'deltaTjs'=>$deltaTjs );
        asort($result_data['data']['deltaTjs']);
        $result_data['data']['models'] = vit_sort_array(  $result_data['data']['deltaTjs'] , $result_data['data']['models'] );
        $result_data['data']['plosses'] = vit_sort_array(  $result_data['data']['deltaTjs'] , $result_data['data']['plosses'] );
        $result_data['data']['pconds'] = vit_sort_array(  $result_data['data']['deltaTjs'] , $result_data['data']['pconds'] );
        $result_data['data']['psws'] = vit_sort_array(  $result_data['data']['deltaTjs'] , $result_data['data']['psws'] );
        $result_data['data']['deltaTjs'] = vit_maintain_indices( $result_data['data']['deltaTjs'] );

    }

    echo json_encode($result_data);
    die;

}

die(1);