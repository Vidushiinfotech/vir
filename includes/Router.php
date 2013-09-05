<?php
/**
 * Map the request and load the html
 *
 */
class Router {
    
    public $page_name = '';
    public $view_path = '';

    public function __construct() {
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $this->page_name = $_GET['page'];
        } else {
            $this->page_name = 'index';
        }
        
        $this->view_path = $this->view_path();
        
        $this->loader();
    }
    
    public function view_path() {
        return EZ_BASE_PATH . '/view';
    }

    public function loader() {
        // if the file is not there
        $load_file = '';
        $file = $this->view_path . '/' . $this->page_name . '.php';
        
        if ( !file_exists($file) ) {
            $load_file = $this->view_path . '/' . '404.php';
	} else {
            $load_file = $file;
        }
        
        //Include the template
        include $load_file;
    }
    
}