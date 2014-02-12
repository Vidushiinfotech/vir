jQuery(document).ready( function() {

    /* Download PDF button onClick event */
    jQuery('.action-buttons .download-pdf').on('click', function(e) {

        e.preventDefault();
        var notLoggedin    =   ( jQuery(this).parents('.action-buttons').hasClass('not-logged-in') ) ? true : false;

        if( notLoggedin ){
            jQuery('.pop-ups').fadeIn('fast', function(){
                jQuery('#no-logged-in').fadeIn('slow');
                overlayHide();
            });
            return false;
        }

        var isAnalyze   =   jQuery(this).parents('section.analyze');
        var isCompare   =   jQuery(this).parents('section.compare');
        var isRecommend =   jQuery(this).parents('section.recommend');
        var tabwrapper  =   jQuery(this).parents('.tabcontainer')

        if( isAnalyze.length > 0 ){

            /* For tab1 */
            if( jQuery(tabwrapper).hasClass('tab1') ){

                /* Code for tab1 action */
                var v1= jQuery( ".select_model select").val();
                var v2= jQuery( ".intonly input").val();
                var v3= jQuery('[name=imin]').val();
                var v4= jQuery('[name=imax]').val();

                html2canvas([document.getElementById('tab1-graphcontainer')], {

                onrendered: function (canvas) {

                    document.getElementById('canvas').appendChild(canvas);
                    var data1 = canvas.toDataURL('image/png');
                    // AJAX call to send `data` to a PHP file that creates an image from the dataURI string and saves it to a directory on the server

                    jQuery.ajax({

                      type: "POST",
                      url: ajaxurl,
                      data: {
                              action: 'analyze_tab1_pdf',
                              image: data1,
                              v1 : v1,
                              v2 : v2,
                              v3 : v3,
                              v4 : v4

                      }

                    }).done(function( respond ) {

                        var url = 'admin/download.php?site=any&file=' + respond;
                        window.location = url;

                    });

                  }

                });
                /* Code for tab1 action ends here */

            }

            /* For tab 2 */
            if( jQuery(tabwrapper).hasClass('tab2') ){

                /* Code for tab1 action */
                var modelname   =   jQuery(tabwrapper).find('select').val();
                var temp        =   jQuery('[name=tab2_ip1]').val();
                var myd         =   jQuery('[name=tab2_ip2]').val();
                var myvdc       =   jQuery('[name=tab2_ip3]').val();
                var myI         =   jQuery('[name=tab2_ip4]').val();
                var fmin        =   jQuery('[name=tab2_ip5]').val();
                var fmax        =   jQuery('[name=tab2_ip6]').val();

                html2canvas([document.getElementById('tab2-graphcontainer')], {

                onrendered: function (canvas) {

                    document.getElementById('canvas').appendChild(canvas);
                    var data1 = canvas.toDataURL('image/png');
                    // AJAX call to send `data` to a PHP file that creates an image from the dataURI string and saves it to a directory on the server

                    jQuery.ajax({

                      type: "POST",
                      url: ajaxurl,

                      data: {

                        action: 'analyze_tab2_pdf',
                        image: data1,
                        modelname : modelname,
                        temp : temp,
                        myd : myd,
                        myvdc : myvdc,
                        myI: myI,
                        fmin:fmin,
                        fmax: fmax

                      }

                    }).done(function( respond ) {

                        var url = 'admin/download.php?site=any&file=' + respond;
                        window.location = url;

                    });

                  }

                });

                /* Code for tab1 action ends here */   
            }

            /* For tab 3 */
            if( jQuery(tabwrapper).hasClass('tab3') ){

                /* Code for tab3 action */
                var modelname   =   jQuery(tabwrapper).find('select').val();
                var mytj        =   jQuery('[name=mytj]').val();
                var myd         =   jQuery('[name=myd]').val();
                var myrthcs     =   jQuery('[name=myrthcs]').val();
                var myI         =   jQuery('[name=myI]').val();
                var mytamb      =   jQuery('[name=mytamb]').val();
                var mytsink     =   jQuery('[name=mytsink]').val();
                var myvdc       =   jQuery('[name=myvdc]').val();
                var fmin        =   jQuery('[name=fmin]').val();
                var fmax        =   jQuery('[name=fmax]').val();

                html2canvas([document.getElementById('tab3-graphcontainer')], {

                onrendered: function (canvas) {

                    document.getElementById('canvas').appendChild(canvas);
                    var data1 = canvas.toDataURL('image/png');
                    // AJAX call to send `data` to a PHP file that creates an image from the dataURI string and saves it to a directory on the server

                    jQuery.ajax({

                      type: "POST",
                      url: ajaxurl,

                      data: {

                            action: 'analyze_tab3_pdf',//_pdf
                            image: data1,
                            modelname : modelname,
                            mytj : mytj,
                            myd : myd,
                            myvdc : myvdc,
                            myI: myI,
                            fmin:fmin,
                            fmax: fmax,
                            myrthcs: myrthcs,
                            mytamb:mytamb,
                            mytsink: mytsink

                      }

                    }).done(function( respond ) {

                        var url = 'admin/download.php?site=any&file=' + respond;
                        window.location = url;

                    });

                  }

                });
                /* Code ends here */

            }

            /* For tab 4 */
            if( jQuery(tabwrapper).hasClass('tab4') ){

                var graph_id = jQuery(this).data('graph-id');
                var modelname = jQuery('select[name="tab4_chosemodel"]').val();
                var mytj = jQuery('input[name="tab4_ip1"]').val();
                var myd = jQuery('input[name="tab4_ip2"]').val();
                var myf = jQuery('input[name="tab4_ip3"]').val();
                var myi = jQuery('input[name="tab4_ip4"]').val();
                var myvdc = jQuery('input[name="tab4_ip5"]').val();

                html2canvas([document.getElementById('tab4-graphcontainer')], {

                    onrendered: function (canvas) {

                        document.getElementById('canvas').appendChild(canvas);
                        var data1 = canvas.toDataURL('image/png');

                        jQuery.ajax({

                              type: "POST",
                              url: ajaxurl,

                              data: {

                                    action: 'analyze_tab4_pdf',//_pdf
                                    image: data1,
                                    modelname : modelname,
                                    mytj : mytj,
                                    myd : myd,
                                    myf : myf,
                                    myI: myi,
                                    myvdc: myvdc

                              }

                        }).done(function( respond ) {

                                var url = 'admin/download.php?site=any&file=' + respond;
                                window.location = url;

                        });

                    }
 
                });
                
            }

            /* Tab 5 code */
            if( jQuery(tabwrapper).hasClass('tab5') ){

                var graph_id = jQuery(this).data('graph-id');
                var modelname = jQuery('select[name="tab5_chosemodel"]').val();
                var mytj = jQuery('input[name="tab5_ip1"]').val();
                var myd = jQuery('input[name="tab5_ip2"]').val();
                var rth = jQuery('input[name="tab5_ip3"]').val();
                var myvdc = jQuery('input[name="tab5_ip4"]').val();
                var tsink = jQuery('input[name="tab5_ip5"]').val();
                var fmin = jQuery('input[name="tab5_ip6"]').val();
                var fmax = jQuery('input[name="tab5_ip7"]').val();
                
                html2canvas([document.getElementById('tab5-graphcontainer')], {
                    
                    onrendered: function (canvas) {
                        
                        document.getElementById('canvas').appendChild(canvas);
                        var data1 = canvas.toDataURL('image/png');

                        jQuery.ajax({

                              type: "POST",
                              url: ajaxurl,

                              data: {

                                    action: 'analyze_tab5_pdf',//_pdf
                                    image: data1,
                                    modelname : modelname,
                                    mytj : mytj,
                                    myd : myd,
                                    fmin : fmin,
                                    fmax : fmax,
                                    myvdc: myvdc,
                                    rth: rth,
                                    tsink: tsink

                              }

                        }).done(function( respond ) {

                                var url = 'admin/download.php?site=any&file=' + respond;
                                window.location = url;

                        });

                    }
                    
                })

            }

        }

        /* Graph is of compare */
        if( isCompare.length > 0 ){

            if( jQuery(tabwrapper).hasClass('tab1') ){

                /* Code for tab1 action */
                var model1  =   jQuery(tabwrapper).find('select[name="tab1_chosemode1"]').val();
                var model2  =   jQuery(tabwrapper).find('select[name="tab1_chosemode2"]').val();
                var model3  =   jQuery(tabwrapper).find('select[name="tab1_chosemode3"]').val();
                var usertemp=   jQuery(tabwrapper).find('input[name="mytj"]').val();
                var imin    =   jQuery(tabwrapper).find('input[name="imin"]').val();
                var imax    =   jQuery(tabwrapper).find('input[name="imax"]').val();

                html2canvas([document.getElementById('tab1-graphcontainer')], {

                onrendered: function (canvas) {

                    //console.log(canvas); exit;

                    document.getElementById('canvas').appendChild(canvas);
                    var data1 = canvas.toDataURL('image/png');
                    // AJAX call to send `data` to a PHP file that creates an image from the dataURI string and saves it to a directory on the server
                    var data = {
                            action: 'compare_tab1_pdf',
                            image: data1,
                            model1: model1,
                            model2: model2,
                            model3: model3,
                            usertemp: usertemp,
                            imin: imin,
                            imax: imax
                      }

                    jQuery.ajax({

                      type: "POST",
                      url: ajaxurl,
                      data: data

                    }).done(function( respond ) {

                        var url = 'admin/download.php?site=any&file=' + respond;
                        window.location = url;

                    });

                  }

                });
                /* Code for tab1 action ends here */

            }
            
            /* For compare tab 2 */
            if( jQuery(tabwrapper).hasClass('tab2') ){

                    /* Code for tab1 action */
                    var modelname1  =   jQuery(tabwrapper).find('select[name="tab2_chosemodel"]').val();
                    var modelname2  =   jQuery(tabwrapper).find('select[name="tab2_chosemode2"]').val();
                    var modelname3  =   jQuery(tabwrapper).find('select[name="tab2_chosemode3"]').val();
                    var temp        =   jQuery(tabwrapper).find('[name="mytj"]').val();
                    var myd         =   jQuery(tabwrapper).find('[name="myd"]').val();
                    var myvdc       =   jQuery(tabwrapper).find('[name="myvdc"]').val();
                    var myI         =   jQuery(tabwrapper).find('[name="myi"]').val();
                    var fmin        =   jQuery(tabwrapper).find('[name="fmin"]').val();
                    var fmax        =   jQuery(tabwrapper).find('[name="fmax"]').val();

                    html2canvas([document.getElementById('tab2-graphcontainer')], {

                    onrendered: function (canvas) {

                            document.getElementById('canvas').appendChild(canvas);
                            var data1 = canvas.toDataURL('image/png');
                            // AJAX call to send `data` to a PHP file that creates an image from the dataURI string and saves it to a directory on the server

                            jQuery.ajax({

                              type: "POST",
                              url: ajaxurl,

                              data: {

                                    action: 'compare_tab2_pdf',
                                    image: data1,
                                    modelname1 : modelname1,
                                    modelname2 : modelname2,
                                    modelname3 : modelname3,
                                    temp : temp,
                                    myd : myd,
                                    myvdc : myvdc,
                                    myI: myI,
                                    fmin:fmin,
                                    fmax: fmax

                              }

                            }).done(function( respond ) {

                                    var url = 'admin/download.php?site=any&file=' + respond;
                                    window.location = url;

                            });

                      }

                    });

                    /* Code for tab1 action ends here */   
            }


            /* For compare tab 3 */
            if( jQuery(tabwrapper).hasClass('tab3') ){

                /* Code for tab1 action */
                var modelname1  =   jQuery(tabwrapper).find('select[name="tab3_chosemodel"]').val();
                var modelname2  =   jQuery(tabwrapper).find('select[name="tab3_chosemode3"]').val();
                var modelname3  =   jQuery(tabwrapper).find('select[name="tab3_chosemode2"]').val();
                var mytj        =   jQuery(tabwrapper).find('[name=mytj]').val();
                var myd         =   jQuery(tabwrapper).find('[name=myd]').val();
                var myrthcs     =   jQuery(tabwrapper).find('[name=myrthcs]').val();
                var myI         =   jQuery(tabwrapper).find('[name=myI]').val();
                var mytamb      =   jQuery(tabwrapper).find('[name=mytamb]').val();
                var mytsink     =   jQuery(tabwrapper).find('[name=mytsink]').val();
                var myvdc       =   jQuery(tabwrapper).find('[name=myvdc]').val();
                var fmin        =   jQuery(tabwrapper).find('[name=fmin]').val();
                var fmax        =   jQuery(tabwrapper).find('[name=fmax]').val();

                html2canvas([document.getElementById('tab3-graphcontainer')], {
                    
                    onrendered: function (canvas) {
                        
                        document.getElementById('canvas').appendChild(canvas);
                        
                        var data1 = canvas.toDataURL('image/png');
                        var data = {

                            action: 'compare_tab3_pdf',//_pdf
                            image: data1,
                            modelname1 : modelname1,
                            modelname2 : modelname2,
                            modelname3 : modelname3,
                            mytj : mytj,
                            myd : myd,
                            myvdc : myvdc,
                            myI: myI,
                            fmin:fmin,
                            fmax: fmax,
                            myrthcs: myrthcs,
                            mytamb:mytamb,
                            mytsink: mytsink

                        }

                        jQuery.ajax({

                          type: "POST",
                          url: ajaxurl,
                          data: data

                        }).done(function( respond ) {

                            var url = 'admin/download.php?site=any&file=' + respond;
                            window.location = url;

                        });

                    }

                });

            }
            
            /* For tab 4 */
            if( jQuery(tabwrapper).hasClass('tab4') ){

                var graph_id = jQuery(this).data('graph-id');
                var modelname1 = jQuery('select[name="tab4_chosemodel"]').val();
                var modelname2  =   jQuery(tabwrapper).find('select[name="tab4_chosemode2"]').val();
                var modelname3  =   jQuery(tabwrapper).find('select[name="tab4_chosemode3"]').val();
                var mytj = jQuery(tabwrapper).find('input[name="mytj"]').val();
                var myd = jQuery(tabwrapper).find('input[name="myd"]').val();
                var myf = jQuery(tabwrapper).find('input[name="myf"]').val();
                var myi = jQuery(tabwrapper).find('input[name="myi"]').val();
                var myvdc = jQuery(tabwrapper).find('input[name="myvdc"]').val();

                html2canvas([document.getElementById('tab4-graphcontainer')], {

                    onrendered: function (canvas) {

                        document.getElementById('canvas').appendChild(canvas);
                        var data1 = canvas.toDataURL('image/png');

                        jQuery.ajax({

                              type: "POST",
                              url: ajaxurl,

                              data: {

                                action: 'compare_tab4_pdf',//_pdf
                                image: data1,
                                modelname1 : modelname1,
                                modelname2 : modelname2,
                                modelname3 : modelname3,
                                mytj : mytj,
                                myd : myd,
                                myf : myf,
                                myI: myi,
                                myvdc: myvdc

                              }

                        }).done(function( respond ) {

                                var url = 'admin/download.php?site=any&file=' + respond;
                                window.location = url;

                        });

                    }
                    
                });

                
            }


        }
        
        /* Check for recommend */
        if( isRecommend.length > 0 ){

            var formdata    =   jQuery("#recommend-form").serialize();

            html2canvas([document.getElementById('recommend-table')], {

                onrendered: function (canvas) {

                    document.getElementById('canvas').appendChild(canvas);
                    var data1 = canvas.toDataURL('image/png');

                    var ajaxdata    =   {

                        action: 'recommend_pdf',
                        formdata: formdata,
                        image: data1
                    };

                    jQuery.post( ajaxurl, ajaxdata, function(response){

                        var url = 'admin/download.php?site=any&file=' + response;
                        window.location = url;

                    });
                    
                }
                
            });

        }

    });
    
});