<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
global $EZ_DB;
$query  = "SELECT user_email, is_subscriber FROM users WHERE is_admin='0'";
$result = $EZ_DB->run_query( $query, 1 ); // Get all emails listed here

/* Code For Generating CSV File On Server */
if( $_GET['format'] == 'csv' ){

    $fname = time (); // Get timestamp , it will be our filename, so that it will be unique

    $fp = fopen( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$fname.'.csv', 'w+'); // Create a new file using w+ mode and open file handle

    chmod( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$fname.'.csv', 0777 ); // Assign permission to file

    while( $row = mysqli_fetch_assoc( $result ) ){

        if( $row['is_subscriber'] )
            $userType = 'subscriber';
        else
            $userType = 'user';

        $array['email'] = $row['user_email'];
        $array['subscriber'] = $userType;

        fputcsv( $fp , $array );

    }

    fclose($fp); ?>

    <p>File has been created, <a target="_blank" href="./download.php?site=<?php echo return_site_url() ?>&file=<?php echo $fname.'.csv' ?>">click here</a>&nbsp;to download.</p><?php

}

/* For creating file in xls format */
if( $_GET['format'] == 'xls' ){

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    date_default_timezone_set('Europe/London');

    if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

    $path = dirname( dirname(__FILE__) ).'/external-libs/phpexcel/Classes/PHPExcel.php';
        require_once( $path ); // Include External Library To Create XLS File.

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("EzIGBT")
                                    ->setLastModifiedBy("EzIGBT")
                                    ->setTitle("EzIGBT Document")
                                    ->setSubject("EzIGBT Document");

    $count = 1;

    while( $row = mysqli_fetch_assoc( $result ) ){

        $col = 'A'.$count;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $col, $row['user_email'] );

        $count++;
    }

    $filename = time();
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5' );
    $objWriter->save( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$filename.'.xls' );

    chmod( EZ_BASE_PATH.'uploads'.EZ_SLASHES.$filename.'.xls', 0777 ); // Assign permission to file ?>

    <p>File has been created, <a target="_blank" href="./download.php?site=<?php echo return_site_url() ?>&file=<?php echo $filename.'.xls' ?>">click here</a>&nbsp;to download.</p><?php

    exit;
}