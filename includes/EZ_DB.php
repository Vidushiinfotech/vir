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
        $db_host = (defined('EZ_DB_HOST') && EZ_DB_HOST) ? EZ_DB_HOST : FALSE;
        $db_name = (defined('EZ_DB_NAME') && EZ_DB_NAME) ? EZ_DB_NAME : FALSE;
        $db_user = (defined('EZ_DB_USER') && EZ_DB_USER) ? EZ_DB_USER : FALSE;
        $db_pass = (defined('EZ_DB_PASS')) ? EZ_DB_PASS : FALSE;
        
        if ($db_host && $db_name && $db_user && $db_pass !== FALSE) {
            $db_details = true;
            $this->error = array(
                'error' => FALSE,
                'msg' => ''
            );
        } else {
            $db_details = false;
            $this->error = array(
                'error' => TRUE,
                'msg' => 'Database details are missing or incorrect!'
            );
        }
        
        if ($db_details) {
            $this->connect = mysqli_connect( $db_host, $db_user, $db_pass, $db_name  );
        }
        
        if ($db_details && !$this->connect) {
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
    public function run_query($query) {
        if ($this->error['error'])
            return false;
        
        $query_return = mysqli_query( $this->connect, $query );
        return mysqli_fetch_assoc($query_return);
    }

    /**
     * Check if table exist in databse
     * @return Array
     */
    public function table_exist() {
        
        $return = array(
            'all_table' =>  FALSE,
            'users'  =>  FALSE
        );
        
        if (!$this->error['error']) {
            // Check for user table
            $user_array = $this->run_query("SHOW TABLES FROM ".EZ_DB_NAME." LIKE 'users'");
            if ($user_array)
                $return['users'] = true;
            
            //Check if all table exist
            if ($return['users'])
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
            
            if ($table_exist['all_table'])
                return; // Return if all tables are there.
            
            // Create user table
            if (!$table_exist['users']) {
                $query_return = $this->run_query("CREATE TABLE users ( ID MEDIUMINT NOT NULL AUTO_INCREMENT,
                                                    username varchar( 50 ) NOT NULL, password varchar(100),
                                                    user_email varchar(100) UNIQUE NOT NULL, primary key(ID) )");
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
        
        $query_return = $this->run_query( "SELECT * FROM users" );
        if ($query_return)
            return TRUE;
    }
}