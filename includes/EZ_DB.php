<?php
/**
 * Databse Class
 *
 */
class EZ_DB {

    public $connect;
    public $error = array(
        'error' => true,
        'msg'   => 'Unknown Error!'
    );

    /**
     * Constructor of class EZ_DB
     */
    public function __construct() {

        $db_host = ( defined('EZ_DB_HOST') && EZ_DB_HOST ) ? EZ_DB_HOST : FALSE;
        $db_name = ( defined('EZ_DB_NAME') && EZ_DB_NAME ) ? EZ_DB_NAME : FALSE;
        $db_user = ( defined('EZ_DB_USER') && EZ_DB_USER ) ? EZ_DB_USER : FALSE;
        $db_pass = ( defined('EZ_DB_PASS') ) ? EZ_DB_PASS : FALSE;

        if ( $db_host && $db_name && $db_user && $db_pass !== FALSE ) {

            $db_details = true;

            $this->error = array(
                'error' => FALSE,
                'msg' => ''
            );

        }else {

            $db_details = false;
            $this->error = array(
                'error' => TRUE,
                'msg' => 'Database details are missing or incorrect!'
            );

        }

        if ( $db_details ) {

            $this->connect = mysqli_connect( $db_host, $db_user, $db_pass, $db_name  );

        }

        if ( $db_details && !$this->connect ) {

            $this->error = array(
                'error' => TRUE,
                'msg' => 'Database connection error!'
            );

        }

        // Create Table if do not exist
        $this->create_table();
    }

    /**
     * Run the SQL query
     * @param type $query Pass SQL query
     * @return Array query result as array or false
     */
    public function run_query( $query, $multi_result = 0 ) {

        if ($this->error['error'])
            return false;

        $query_return = mysqli_query( $this->connect, $query );

        if( !$multi_result ){
            if ( is_object( $query_return ) )
                return mysqli_fetch_assoc($query_return);
        }
        return $query_return;

    }

    /**
     * Check if table exist in databse
     * @return Array
     */
    public function table_exist() {

        $return = array(
            'all_table'     =>  FALSE,
            'users'         =>  FALSE,
            'config'        =>  FALSE,
            'models'        =>  FALSE,
            'pages'         =>  FALSE
        );

        if (!$this->error['error']) {
            // Check for user table
            $user_array = $this->run_query("SHOW TABLES FROM ".EZ_DB_NAME." LIKE 'users'");
            if ($user_array)
                $return['users'] = true;

            // Check for pages table
            $pages_array = $this->run_query("SHOW TABLES FROM ".EZ_DB_NAME." LIKE 'pages'");
            if ( $pages_array )
                $return['pages'] = true;

            // Check for config table
            $config_array = $this->run_query("SHOW TABLES FROM ".EZ_DB_NAME." LIKE 'config'");
            if ($config_array)
                $return['config'] = true;

            //Check for models table
            $models_array = $this->run_query("SHOW TABLES FROM ".EZ_DB_NAME." LIKE 'models'");
            if ($models_array)
                $return['models'] = true;

            //Check if all table exist
            if ( $return['users'] && $return['config'] && $return['models'] && $return['pages'] )
                $return['all_table'] = true;
        }

        return $return;

    }

    /**
     * Create table if does not exist
     */
    public function create_table() {

        if (!$this->error['error']) {
            $table_exist = $this->table_exist();
 
            if ( !empty($table_exist['all_table']) )
                return; // Return if all tables are there.

            // Create user table
            if (  empty($table_exist['users']) ) {

                $query_return = $this->run_query("CREATE TABLE users ( ID MEDIUMINT NOT NULL AUTO_INCREMENT, username varchar(50) NOT NULL, password varchar(100), fname VARCHAR(50) NULL, lname VARCHAR(50) NULL, user_email varchar(100) UNIQUE NOT NULL, application varchar(100) NOT NULL DEFAULT 'Other Apllication', is_admin TINYINT NOT NULL DEFAULT '1', is_subscriber TINYINT NOT NULL DEFAULT '0', primary key (ID) );");
            }

            // Create configuration table
            if ( empty($table_exist['config']) ) {

                $query_return = $this->run_query("CREATE TABLE config ( key_name varchar ( 100 ), key_value varchar(500) )");
            }

            // Create pages table
            if ( empty($table_exist['pages']) ) {
                
                $query_return = $this->run_query("CREATE TABLE pages ( ID INT NOT NULL AUTO_INCREMENT , 
                                                    slug varchar(255) NOT NULL, title varchar(255) NOT NULL, 
                                                    content LONGTEXT NOT NULL, visible TINYINT NOT NULL DEFAULT 1, 
                                                    PRIMARY KEY ( ID ) )
                                                ");

            }

            //Create models table
            if ( empty($table_exist['models']) ) {

                $query_return = $this->run_query("CREATE TABLE models (
                                                    model_name VARCHAR( 100 ) NOT NULL ,
                                                    v_rated INT NULL ,
                                                    i_rated INT NULL ,
                                                    package VARCHAR( 100 ) NULL ,
                                                    rthjc_igbt FLOAT NULL ,
                                                    rthjc_diode FLOAT NULL ,
                                                    rthcs FLOAT NULL ,
                                                    tjref INT NULL ,
                                                    vref INT NULL ,
                                                    eontjmax FLOAT NULL ,
                                                    htjmax FLOAT NULL ,
                                                    ktjmax FLOAT NULL ,
                                                    xtjmax FLOAT NULL ,
                                                    eofftjmax FLOAT NULL ,
                                                    mtjmax FLOAT NULL ,
                                                    ntjmax FLOAT NULL ,
                                                    ytjmax FLOAT NULL ,
                                                    vttjmax FLOAT NULL ,
                                                    atjmax FLOAT NULL ,
                                                    btjmax FLOAT NULL ,
                                                    er0tjmax FLOAT NULL ,
                                                    d1tjmax FLOAT NULL ,
                                                    d2tjmax FLOAT NULL ,
                                                    vtdtjmax FLOAT NULL ,
                                                    adtjmax FLOAT NULL ,
                                                    bdtjmax FLOAT NULL ,
                                                    eon FLOAT NULL ,
                                                    h FLOAT NULL ,
                                                    k FLOAT NULL ,
                                                    x FLOAT NULL ,
                                                    eoff FLOAT NULL ,
                                                    m FLOAT NULL ,
                                                    n FLOAT NULL ,
                                                    y FLOAT NULL ,
                                                    vt FLOAT NULL ,
                                                    a FLOAT NULL ,
                                                    b FLOAT NULL ,
                                                    er0 FLOAT NULL ,
                                                    d1 FLOAT NULL ,
                                                    d2 FLOAT NULL ,
                                                    vdt FLOAT NULL ,
                                                    ad FLOAT NULL ,
                                                    bd FLOAT NULL ,
                                                    include_model TINYINT NULL ,
                                                    PRIMARY KEY ( model_name ))");
            }
        }
    }

    /**
     * Check if any single admin exist
     * @return boolean
     */
    public function admin_exist() {

        if ($this->error['error'])
            return FALSE;

        $query_return = $this->run_query( "SELECT * FROM users WHERE is_admin='1'" );

        if ( $query_return )
            return TRUE;
        else
            return FALSE;
    }
}

/* Create instance */
$EZ_DB = new EZ_DB();