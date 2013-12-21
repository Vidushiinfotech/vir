/*
 * Script Required For Admin Panel 
 */
jQuery(document).ready(function(){

    /* To load Models in admin panel */
    jQuery('body').on( 'click', '#load-parts' ,function(){

        var paged   =   jQuery(this).data('page');
        paged       =   parseInt(paged);
        var element =   jQuery(this);

        jQuery('.loading-div').show('fast');

        var ajaxdata = {

            action: 'load_more',
            pagenumber: paged

        };

        jQuery.post( ajaxurl, ajaxdata, function(res){
            
            jQuery('.loading-div').hide('slow');
            
            if (res) {
            jQuery(element).before(res);
            jQuery(element).data( 'page' ,( paged + 1 ) );
            } else {
                jQuery(element).html('No more data!').fadeOut('slow');
            }
        });

    });

    /* To Show / Hide Model */
    jQuery('body').on( 'click', '.alter-parts' ,function(){

        var model   =   jQuery(this).data('part');
        var flag    =   jQuery(this).data('flag');
        var element =   jQuery(this);

        var ajaxdata = {

            action: 'alter_part',
            modelname: model
        };
        
        jQuery('.loading-div').show();

        jQuery.post( ajaxurl, ajaxdata, function(res){
            
            jQuery('.loading-div').hide();

            if( res == '1' ){

                if( flag == 'show' ){

                    jQuery(element).attr( 'src',  showimg );
                    jQuery(element).prev().text('Currently Hidden');
                    jQuery(element).data('flag', 'hide');
                    jQuery( element ).attr( 'title' , 'Show' );

                }else{

                    jQuery(element).attr( 'src', hideimg );
                    jQuery(element).prev().text('Currently Shown');
                    jQuery(element).data('flag', 'show');
                    jQuery( element ).attr( 'title' , 'Hide' );
                }
            }
        });

    });

    /* To Delete Parts ( Models ) from admin panel */
    jQuery('body').on( 'click', '.model-delete a', function(e){

        e.preventDefault();
        var getconfirm =   confirm("Are you sure you want to delete this part?");
        if( getconfirm === true ){
        var model   =   jQuery(this).data('modelname');
        var element =   jQuery(this);

        var ajaxdata = {

            action: 'delete_part',
            modelname: model
        };
        
        jQuery('.loading-div').show();

        jQuery.post( ajaxurl, ajaxdata, function(res){
            
            jQuery('.loading-div').hide();

            var target = jQuery(element).parents('.row');
            jQuery(element).parents('.row').fadeOut('',function(){

                jQuery(target).remove();

                var count = 0;
                var applyClass = "";

                jQuery('.model-activation .row').each(function(){

                            if( !jQuery(this).hasClass('heading') ){

                               applyClass = ( !count || ! ( count % 2  ) ) ? 'even' : 'odd';
                               jQuery(this).removeClass('even');
                               jQuery(this).removeClass('odd');
                               jQuery(this).addClass(applyClass);
                               count++;

                            }

                        });

                    });

                });

            }
    });

    /* Calculation Feature On / Off */
    jQuery('body').on( 'click', '.feature-toggle', function(e){

        var featureno   =   jQuery(this).data('featureno');
        var status      =   jQuery(this).data('status');
        if( jQuery.trim(status) == "" )
            status      =   0;
        status          =   parseInt(status);
        var element     =   jQuery(this);

        var ajaxdata = {

            action: 'feature_toggle',
            featureno:featureno,
            status:status

        };
        
        jQuery('.loading-div').show();

        jQuery.post( ajaxurl , ajaxdata, function( res ){// Send the ajax request.

            jQuery('.loading-div').hide();
            /* Play with the ajax response here */
            if( res === '1' ){
                
                if( status === 0 ){

                    jQuery( element ).prev().text('Currently Active');
                    jQuery( element ).data( 'status', '1' );
                    jQuery( element ).attr( 'src' , inactive )
                    jQuery( element ).attr( 'title' , 'Deactivate' )

                }else{

                    jQuery( element ).prev().text('Currently Inactive ');
                    jQuery( element ).data( 'status', '0' );
                    jQuery( element ).attr( 'src' , active )
                    jQuery( element ).attr( 'title' , 'Activate' );
                }
            }

        });

    });

    /* tinymce for adding pages */
    tinymce.init({
        selector: "textarea#add-page",
        height: 250,
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste jbimages"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"

    });

     /* Create Pages Using Ajax */
     jQuery("#page-submit").on( 'click', function(){

        var title   = jQuery("#page-name").val();
        var editor = tinymce.get( 'add-page' );
        editor.save();// save tinymce value in textarea

        var content =   jQuery("#add-page").val();
        var pid     =   jQuery(this).data('pid');

        if( pid == 0 ){

            var ajaxdata = {
                action: 'create_page',
                title: title,
                content: content
            };

        }else{

            var ajaxdata = {
                action: 'create_page',
                title: title,
                content: content,
                pid: pid
            };

        }
        
        jQuery('.loading-div').show('fast');

        jQuery.post( ajaxurl, ajaxdata, function(res){
            
            jQuery('.loading-div').hide('slow');

            var msg = res.split('~');
            jQuery(".msg-div").html(msg[1]);

            if( ( msg[0] == 'success' ) && ( pid == 0 ) ){

                tinymce.get('add-page').setContent(''); 
                jQuery("#page-name").val('');
            }

        });

     });

     /* Delete a post using ajax */
     jQuery(".get-confirm span").on('click', function(){

         var pid = jQuery(this).data('pid');

         var ajaxdata = {

            action: 'delete_page',
            pid: pid
         };

         jQuery.post( ajaxurl, ajaxdata, function(res){

             jQuery('.msg-div').html(res);
             
         });
         
     });

});

/* After Window loaded completely */
jQuery(window).on('load', function(){
   
    var windowHeight    =   jQuery(window).height();
    jQuery(".admin-panel").css('min-height', (windowHeight-220))
    
});