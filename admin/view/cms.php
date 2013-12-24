<?php
/**
 * Listing of the pages created by CMS
 */
global $EZ_DB, $cms; ?>

<div class="cms-area">

    <div class="page-add">
        <a href="<?php echo return_site_url().'admin/index.php?page=cms&action=add' ?>" class="add-new-page">Add New Page</a>
    </div><?php

    /* Add a new page to the site */
    if( empty( $_GET['action'] ) ){

        $cms->pageListing();

    }elseif( trim ( $_GET['action'] ) == 'add' ){

        $cms->newPage();

    }elseif( ( trim ( $_GET['action'] ) == 'edit' ) && !empty( $_GET['pid'] ) ){

        $cms->editPage( $_GET['pid'] );

    }elseif( ( trim ( $_GET['action'] ) == 'delete' ) && !empty( $_GET['pid'] ) ){

        $cms->deletePage( $_GET['pid'] );

    } ?>

</div>