<?php
class manageCMS {

    public function __construct() {

        /* Empty constructor */

    }

    /* For creating new pages from back-end */
    public function newPage(){ ?>

        <div id="new-page-container">

            <div class="msg-div"></div><!-- For Displaying response messages -->

            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" mehod="post">

                <div class="field">
                    <div class="label"><label for="page-name">Title</label></div>
                    <div class="inpt">
                        <input type="text" name="page_name" id="page-name" value="" />
                    </div>
                </div>

                <div class="field">
                    <div class="label"><label for="add-page">Content</label></div>
                    <div class="inpt">
                        <textarea id="add-page" name="page_content"></textarea>
                    </div>
                </div>

                <div class="field">
                    <input data-pid="0" class="button" type="button" name="page_submit" id="page-submit" value="Create Page" />
                </div>

            </form>

        </div><?php
 
    }

    /* For editing current existing page */
    public function editPage( $pageID ){

        global $EZ_DB;
        $query  = "SELECT * FROM pages WHERE ID=".$pageID;
        $result = $EZ_DB->run_query( $query, 0 ); 

        if( !empty( $result ) ): ?>

            <div id="new-page-container">

                <div class="msg-div"></div><!-- For Displaying response messages -->

                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" mehod="post">

                    <div class="field">

                        <div class="label"><label for="page-name">Title</label></div>
                        <div class="inpt">
                            <input type="text" name="page_name" id="page-name" value="<?php echo $result['title'] ?>" />
                        </div>

                    </div>

                    <div class="field">
                        <div class="label"><label for="add-page">Content</label></div>
                        <div class="inpt">
                            <textarea id="add-page" name="page_content"><?php echo $result['content'] ?></textarea>
                        </div>
                    </div>

                    <div class="field">
                        <input data-pid="<?php echo $pageID ?>" class="button" type="button" name="page_submit" id="page-submit" value="Update Page" />
                    </div>

                </form>

            </div><?php

        endif;

    }

    /* To delete existing pages in the database created by client */
    public function deletePage( $pageID ){ ?>

        <div class="delete-page">

            <div class="msg-div"></div><?php

            if( !is_int( $pageID ) ){ ?>

                <p>Invalid Request</p><?php

            } ?>

            <p><strong>Are you sure you want to delete post ?</strong></p>
            <div class="get-confirm">
                <span data-pid="<?php echo $pageID ?>">Yes</span>
            </div>

        </div><?php

    }

    /* List all the pages created by admin */
    public function pageListing(){

        global $EZ_DB;

        $pages = mysqli_query( $EZ_DB->connect, "SELECT * FROM pages" );

            echo '<ul class="page-listing">';

                while( $row = mysqli_fetch_assoc( $pages ) ){ ?>

                    <li class="pages-list-item clearfix">

                        <div class="page-title"><?php echo $row['title'] ?></div>
                        <div class="action">
                            <span><a href="<?php  echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=edit&pid='.$row['ID'] ?>">Edit</a></span>
                            <span><a href="<?php  echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=delete&pid='.$row['ID'] ?>">Delete</a></span>
                            <span><a target="_blank" href="#">View</a></span>
                        </div>

                    </li><?php

                }

            echo '</ul>';
    }

};

global $cms;
$cms = new manageCMS();