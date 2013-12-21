/* 
 * Custom jQuery and js for ezIGBT
 */
//Load jQuery when dom is fully ready
jQuery(document).ready(function (){

    /* jQuery custom tab */
    jQuery('.page-tabs li:first-child').addClass('selected');
    var first_tab = jQuery('.tabcontainer').get(0);
    jQuery(first_tab).show();
    jQuery('.ez-tab-list-wrapper a').click(function (e) {
        e.preventDefault();
        jQuery('.tab-content-wrapper .tabcontent').hide();
        var divID = jQuery(this).attr('href');
        jQuery(divID).fadeIn();
        jQuery('.ez-tab-list-wrapper li').removeClass('active');
        jQuery(this).parent('li').addClass('active');
    });
    
    /* Add class if js is enabled */
    jQuery('body').addClass('has-js');
    
    /* Accordion on FAQ Page */
    jQuery('.accordion .visible').on('click', function(){

        var visible = false;

        if( jQuery(this).hasClass('expand') ){

            jQuery(this).next().slideUp();
            jQuery(this).removeClass('expand');
            return false;
        }

        jQuery('.accordion .visible').each(function(){

            jQuery(this).removeClass('expand');

        });

        if( true /*visible*/ ){

            jQuery('.visible').next().slideUp();
            jQuery(this).next().slideDown();
            jQuery(this).addClass('expand');
        }

    });

    /* Append loader to button */
    var loaderImg = '<img class="ajax-loader-img" src="'+loader+'" alt="ajax-loader" />';
    jQuery("#cform-submit").parent().append(loaderImg);

    /* Contact Page JS */
    jQuery('#cform-submit').on('click', function(){

        var fname   =   jQuery('#fname').val();
        var lname   =   jQuery('#lname').val();
        var mail    =   jQuery('#cform-email').val();
        var subjct  =   jQuery('#subject option:selected').text();
        var msg     =   jQuery('#msg').val();

        var ajaxdata    =   {

            action: 'cform_submit',
            fname: fname,
            lname: lname,
            mail: mail,
            subject: subjct,
            msg: msg

        };

        jQuery('.ajax-loader-img').css('visibility', 'visible');

        jQuery.post( ajaxurl, ajaxdata, function(res){

            jQuery('.ajax-loader-img').css('visibility', 'hidden');
            jQuery('.msg-div').html(res);
            jQuery('.captcha-fresh').trigger('click');

        });

    });

    /* For analyze and compare tabs */
    jQuery('.page-tabs li').on('click', function(){

        var tab = jQuery(this).data('tab');

        jQuery('.page-tabs li').each(function(){

            var tabGet = jQuery(this).data('tab');
            jQuery('.'+tabGet).hide();
            jQuery(this).removeClass('selected');

        });

        jQuery('.'+tab).show();
        jQuery(this).addClass('selected');
        
        if( localStorage.lastpart !== 'undefined' )
            localStorage.removeItem('lastpart');

    });

    /* Graph form inputs lable hide and show */
    jQuery('span.control > label').click(function(e){
        jQuery(this).hide();
        jQuery(this).next().focus();
    });

    jQuery('span.control > input[type="text"]').on('focus',function(e){
        jQuery(this).prev().hide();
    });

    jQuery('span.control > input[type="text"]').on('blur',function(e){
        if (!jQuery(this).val())
            jQuery(this).prev().show();
    });

    /* If a function is defined, then only call it */
    if( typeof( jQuery.fancyfields ) == 'object' ){

        //jQuery('select').fancyfields();

    }

    /* Popup scripts */
    jQuery('.report-bug').on('click', function(e){

        e.preventDefault();
        jQuery('.pop-ups').fadeIn('fast', function(){

            jQuery('#report-popup').fadeIn('slow');
            overlayHide();

        });

    });

    /* Popup scripts */
    jQuery('.get-samples').on('click', function(e){

        e.preventDefault();
        jQuery('.pop-ups').fadeIn('fast', function(){

            jQuery('#samples-popup').fadeIn('slow');
            overlayHide();

        });

    });


    /* Submit a report bug */
    jQuery('.report-submit').on('click', function(){

        var element     =   jQuery(this);
        var parent_elem =   jQuery(this).parents('.popup-content')
        var which_issue =   jQuery('input:radio[name=bugradio]:checked').val();
        var issue_msg   =   jQuery.trim( jQuery('textarea[name="describe_text"]').val() );
        var issue_email =   jQuery.trim( jQuery('input[name="report-myemail"]').val() );

        var ajaxdata    =   {

            action: 'report_bug',
            subject : 'EzIGBT - A bug has been reported',
            issue: which_issue,
            msg: issue_msg,
            mail: issue_email

        };

        jQuery('.action-loader').show();

        jQuery.post( ajaxurl, ajaxdata, function(res){

            var isError =   res.indexOf('error');
            jQuery('.action-loader').hide();
            jQuery(parent_elem).find('.msg-div').html(res);

            if( isError === -1 ){

                jQuery('textarea[name="describe_text"]').val('');
                jQuery('input[name="report-myemail"]').val('');

            }
        });
    });

    /* Download CSV button */
    jQuery('.action-buttons .download-csv').on('click', function(e){
        e.preventDefault();
        var element = jQuery(this);
        var graph_data = jQuery('body').data('graph_data');
        var input_values = new Object();
        var axis_names = new Array('');
        var temp = new Array();
        var title = '';
        var is_compare = jQuery('.tabs-wrapper').hasClass('compare') ? true : false;
        var classes = jQuery(this).parents('.tabcontainer').attr('class');
        var whichTab = classes.split(' ');
        whichTab = whichTab[0];

        /* Collect all inputs */
        jQuery(this).parents('.tabcontainer').children('.controls-wrapper').find('select, input').each(function (index, elem){
            
            if (jQuery(elem)[0].nodeName === "SELECT" && jQuery(elem).val()) {

                if( !is_compare )
                input_values['model'] = jQuery(elem).val();
                else{

                    var all_select = jQuery(element).parents('.tabcontainer').find('select');
                    var tempmode = { "0": all_select[0].value, "1":all_select[1].value, "2":all_select[2].value };
                    input_values["model"] = tempmode;
                }

            } else if (jQuery(elem).val()) {
                title = jQuery(elem).prev('label').text();
                input_values[title] = jQuery(elem).val();
            }
        });

        /* Collect all axis labels */
        jQuery(this).parents('.tabcontainer').find('.axis-wrapper').each(function (index, elem){
            temp = new Array();
            temp[0] = jQuery(this).children('.ez-xaxis').text();
            temp[1] = jQuery(this).children('.ez-yaxis').text();
            axis_names[index] = temp;
        });

        var data = {
            action : 'graph_csv',
            data : graph_data,
            input_values : input_values,
            axis_names : axis_names,
            is_compare : is_compare
        };
        
        jQuery('#ez-ajax-loader').show();
        jQuery('#graph-msg').fadeOut();
        
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: 'json',
            data: data,
            success: function(response){
                if (response.error === false) {
                    window.location = response.data;
                } else {
                    jQuery('#graph-msg').html(response.error_msg).fadeIn();
                }
            },
            error: function (error_obj, msg) {
                console.log(error_obj);
                jQuery('#graph-msg').html(msg).fadeIn();
                jQuery('#ez-ajax-loader').hide();
            }
        }).done(function (){
            jQuery('#ez-ajax-loader').hide();
        });
    });

    /* Close popup function */
    jQuery('.closepop').on('click', function(){

        jQuery(this).parents('.popup').fadeOut('fast', function(){

            jQuery('.pop-ups').fadeOut('slow');

        });

    });

    
    /* Subscribe to updates */
    jQuery('.coming-updates').on('click', function(){
       
        var email   =   jQuery('.coming-updates-email').val();
        var element =   jQuery(this).parents('.popup-content');
        
        var ajaxdata = {
            
            action: 'subscribeme',
            email: email
        }

        jQuery('.action-loader').show();

        jQuery.post(ajaxurl,ajaxdata,function(res){

            var isError =   res.indexOf('error');

            jQuery('.action-loader').hide();
            jQuery(element).find('.msg-div').html(res);

           if(  isError === -1  ){

               var email   =   jQuery('.coming-updates-email').val('');
           }
        });
    });

    if( jQuery('body').hasClass('analyze') || jQuery('body').hasClass('compare')  ){ // condition to avoid error
        jQuery('.tabcontainer select').chosen();
    }

    /* Captcha refresh script */
    jQuery('.captcha-fresh').on('click', function(){
        
        var element = jQuery(this);
        var ajaxdata = {
            action: 'refresh_captcha'
        };
        
        jQuery.post( ajaxurl, ajaxdata, function(res){
            var a = jQuery(res).attr('src')
            jQuery(element).prev().attr('src', a);
            
        });
        
    });
    

});


/* Custom functions */
function overlayHide(){

    jQuery('body').on('click', function(e){

        var x = e.clientX;
        var y = e.clientY

    });

}
