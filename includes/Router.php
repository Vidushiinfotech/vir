<?php
/**
 * Map the request and load the html
 */
class Router {

    public $page_name = '';
    public $view_path = '';

    /**
     * Constructor
     */
    public function __construct() {

        global $EZ_DB;

        $this->view_path = $this->view_path();

        if (isset($_GET['page']) && !empty($_GET['page'])) {

            $this->page_name = $_GET['page'];
            $file = $this->view_path . '/' . $this->page_name . '.php';

            if ( !file_exists(  $file ) ){

                $cms_page = $this->is_cms_page( $this->page_name, false );
                $result = $EZ_DB->run_query("SELECT key_value from config WHERE key_name='site_url'");

                if (isset($cms_page['ID']) && $cms_page['ID'] && isset( $result['key_value'] )) {
                    $siteurl = $result['key_value'].'?page='.$cms_page['slug'];

                        if( $_GET['page'] != $cms_page['slug'] ):
                            header("Location: $siteurl");
                            die(1);
                        endif;
                        
                } else {
                    header("HTTP/1.0 404 Not Found");
                    header("Status: 404 Not Found");
                }
            }

        } else {
            $this->page_name = 'home';
        }
    }

    /**
     * Get path of view folder
     * @return string path of view folder
     */
    public function view_path() {
        return EZ_BASE_PATH . 'view/';
    }

    /**
     * Load the php file or cms content
     */
    public function loader() {
        // if the file is not there
        $load_file = '';
        $cms_page_id = FALSE;
        $cms_content = '';
        $file = $this->view_path . '/' . $this->page_name . '.php';

        if ( !file_exists($file) ) {

            $cms_page_id = $this->is_cms_page( $this->page_name );

            if ( !$cms_page_id )
                $load_file = $this->view_path . '/' . '404.php';

	} else {

            $load_file = $file;

        }

        //Include the template or echo cms content
        if ( $cms_page_id ){

            $pageData       =   $this->get_cms_page_content( $cms_page_id );
            $pageTitle      =   $pageData['title'];
            $pageContent    =   $pageData['content'];

            echo '<div class="cmspage-wrapper">';
                echo '<h1 class="cmspage-title">'.$pageTitle.'</h1>';
                echo '<div class="page-content">'.$pageContent.'</div>';
            echo '</div>';

        }
        else
            include $load_file;
    }

    function is_cms_page( $slug, $returnID = true ) {

        global $EZ_DB;

        $page_id = $EZ_DB->run_query( "SELECT ID, slug from pages WHERE slug LIKE '%$slug%'" );
        if ( !empty( $page_id ) && $returnID )
            return $page_id['ID'];
        elseif( !empty( $page_id ) && !$returnID )
            return $page_id;

        return FALSE;

    }

    function get_cms_page_content($page_id) {

            global $EZ_DB;
            $pageData = $EZ_DB->run_query("SELECT title, content from pages WHERE ID='$page_id'");

            if( !empty( $pageData ) )
                return $pageData;

            return '';
        }
}