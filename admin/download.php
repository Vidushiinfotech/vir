<?php
/**
 * Purpose: To download the created file csv / xls.
 */

/* Include config file to get all the constants */
require_once '../config.php';

/* If site url or filename is missing , stop the script here */
if( empty( $_GET['site'] ) || empty( $_GET['file'] ) ){ die('Invalid Request'); }

/* Valid Extensions */
$validExt = array( 'csv', 'xls', 'pdf' );

$fileName = $_GET['file'];
$uploadedFile = $fileName;
$fileName = explode(".", strtolower( $fileName ) );

/* If extension is valid then bail the execution */
if( $fileName ){

    foreach( $fileName as $val ){

        if( in_array( $val, $validExt ) ):
            $bail = true;
            break;
        endif;// End the loop here

    }

}

if( !isset($bail) )
    die( 'Invalid Request' );

/* Finally check if file requested exists or not */
if( substr( EZ_BASE_PATH, -1 ) != EZ_SLASHES )
    $root = EZ_BASE_PATH.EZ_SLASHES;
else
    $root = EZ_BASE_PATH;

$filename = $root.'uploads'.EZ_SLASHES.$_GET['file'];
if( !file_exists($filename) )
    die( 'Invalid Request' );

/* If you are here, request is fair good ;) */


header("Content-Length: " . filesize($filename));

if ( in_array( end( $fileName ), array( 'csv' ) ) ){

    header('Content-Encoding: UTF-8');
    header('Content-Type: text/plain; charset=utf-8');

}elseif( in_array( end( $fileName ), array( 'xls' ) ) ){

    header('Content-Type: application/vnd.ms-excel');

}elseif( in_array( end( $fileName ), array( 'pdf' ) ) ){

    header('Content-Type: application/pdf');
    header("Content-Type: application/force-download");
}
header('Content-Disposition:attachment;filename='.  reset($fileName).'_'.date("Y_m_d.").end($fileName) );
header('Pragma: no-cache');
readfile( $filename );
exit();
