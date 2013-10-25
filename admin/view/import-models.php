<?php
/*
 * Import CSV template
 * and open the template in the editor.
 */
global $EZ_DB;
$rows = 0;
$error = false;
$msg = '';
$class = '';

if( isset( $_POST['submit'] ) ) {

    set_time_limit(0);

    if( !empty( $_FILES['csv_file']['name'] ) ){

        $fileName = $_FILES['csv_file']['name'];
        $uploadedFile = $fileName;
        $fileName = explode(".", strtolower( $fileName ) );

        if ( !in_array( end( $fileName ), array( 'csv' ) ) ){
            $error = true;
            $msg .= 'This file type is not allowed, please upload csv file!<br />'; 
        }

        if( !$error && move_uploaded_file( $_FILES["csv_file"]["tmp_name"], EZ_BASE_PATH.'uploads'.EZ_SLASHES.$_FILES['csv_file']['name'] ) ){

            $fhandle = fopen( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$_FILES['csv_file']['name'], 'r+' );

                $counter = 1;

                while( $data = fgetcsv($fhandle,1000,",","'") ) {

                    if ($counter != 1) {
                        
                        
                    /* 
                     * First check if the model is already present in the database if so update it
                     * Else Enter the new model record into the database
                     */
                    $modelQuery =   "SELECT * FROM models WHERE model_name='$data[0]]'";
                    $result     =   $EZ_DB->run_query( $modelQuery, 0 );

                    if( !$result ){

                    $result = $EZ_DB->run_query( "INSERT INTO models VALUES ( '". trim($data[0]) ."', '".$data[1]."', '".$data[2]."', '".$data[3]."', '".$data[4]."', 
                            '".$data[5]."', '".$data[6]."', '".$data[7]."', '".$data[8]."', '".$data[9]."', '".$data[10]."', '".$data[11]."', 
                            '".$data[12]."', '$data[13]', '$data[14]', '$data[15]', '$data[16]','$data[17]', '$data[18]', '$data[19]', '$data[20]',
                                '$data[21]', '$data[22]', '$data[23]', '$data[24]','$data[25]', '$data[26]', '$data[27]', '$data[28]',
                                    '$data[29]', '$data[30]', '$data[31]', '$data[32]','$data[33]', '$data[34]', '$data[35]', '$data[36]',
                                        '$data[37]', '$data[38]', '$data[39]', '$data[40]','$data[41]', '$data[42]', '$data[43]', '$data[44]')" );
                    }else{

                    $result = $EZ_DB->run_query( "UPDATE models SET  v_rated='".$data[1]."', i_rated='".$data[2]."', package='".$data[3]."', rthjc_igbt='".$data[4]."', 
                            rthjc_diode='".$data[5]."', rthid='".$data[6]."', rthdi='".$data[7]."', tjref='".$data[8]."', vref='".$data[9]."', eontjmax='".$data[10]."', htjmax='".$data[11]."', 
                            ktjmax='".$data[12]."', xtjmax='$data[13]', eofftjmax='$data[14]', mtjmax='$data[15]', ntjmax='$data[16]', ytjmax='$data[17]', vttjmax='$data[18]', atjmax='$data[19]', btjmax='$data[20]',
                                er0tjmax='$data[21]', d1tjmax='$data[22]', d2tjmax='$data[23]', vtdtjmax='$data[24]',adtjmax='$data[25]', bdtjmax='$data[26]', eon='$data[27]', h='$data[28]',
                                    k='$data[29]', x='$data[30]', eoff='$data[31]', m='$data[32]',n='$data[33]', y='$data[34]', vt='$data[35]', a='$data[36]',
                                        b='$data[37]', er0='$data[38]', d1='$data[39]', d2='$data[40]',vdt='$data[41]', ad='$data[42]', bd='$data[43]', include_model='$data[44]' WHERE model_name='". trim($data[0]) ."]'" );

                    }

                    if ($result)
                            $rows++;

                    }
                $counter++;

                }

        } else {
            $error = TRUE;
            $msg .= 'Error!<br />';
        }

    } else {
        $error = TRUE;
        $msg = 'Please Upload CSV File! <br />';
    }

}

if ($rows || $msg) {
    if ($error)
        $class = 'error';
if ($rows)
        $msg .= "updated $rows rows!<br />"; ?>

    <div class='msg <?php echo $class; ?>'><?php echo $msg; ?></div><?php
    
} ?>

<form enctype="multipart/form-data" action="" method="POST">

    <p>
        <input id="csv-file" type="file" value="Upload CSV" name="csv_file" />
        <div id="fakebutton">
            <img src="<?php echo VIT_IMG.'/openicon.png' ?>" alt="Open Icon" />
            <input type="button" name="fakebutton" value="Browse CSV File" />
        </div>
    </p>

    <p>
        <input id="upload-csv" type="submit" value="Upload CSV" name="submit" />
    </p>

</form>