/* 
 * Custom jQuery and js for ezIGBT Graph drawing
*/
//Ready function.
jQuery(document).ready(function (){

    /* Bind ajax request to select on chagne */
    jQuery('.onchange_ajax > select').each(function (index, elem){
        jQuery(elem).on('change', function (){
            var cond = check_ajax_req_cond(this); // Check other conditons before sending request
            var model_name = jQuery(this).val();
            if (cond && model_name) {
                jQuery(this).parents('.controls-wrapper').find('a.plot-graph-button').trigger('click');
            }
        });
    });

     /* Bind ajax request to all inputs of analyze tab on chagning value */
    jQuery('.controls-wrapper input[type="text"]').on('change', function(){

        if( jQuery('body').hasClass('recommend') ){

            fill_default_values(jQuery('.recommend .controls-wrapper'));            
            jQuery(this).parents('.controls-wrapper').find('.plot-graph-button').trigger('click');
            return false;
            
        }

        var model = jQuery(this).parents('.controls-wrapper').find('.select_model > select').val();
        if ( model ) {
            var msg = validate_value(this); //Validate input
            var cond = check_ajax_req_cond(this); // Check other conditons before sending request
            if ( msg === true ) {
                if ( cond ) {
                    jQuery(this).parents('.controls-wrapper').find('.plot-graph-button').trigger('click');
                }
            } else {
                alert(msg);
            }
        } else {
            jQuery('#graph-msg').html('Please Choose an IGBT').fadeIn();
        }

    });

    previousPoint = null;

    var g_tt_ids = new Array(
            '#tab1-graph1', 
            '#tab1-graph2', 
            '#tab2-graph1',
            '#tab4-graph1',
            '#analyze_tab3',
            '#tab3-graph1',
            '#compare_tab4',
            '#analyze_tab5',
            '#compare_tab5'
        );

    //Bind hover event for every graph
    if (!jQuery('body').hasClass('ie8')) {

        g_tt_ids.forEach(function(graph_id) {

            /* Change floting point for few graphs */
            var floting_point = 3;
            if (graph_id === '#analyze_tab3' || graph_id === "#tab3-graph1") {
                floting_point = 6;
            }

            jQuery( graph_id ).bind("plothover", function(event, pos, item) {

                if (item) {

                    if (previousPoint != item.datapoint) {
                        previousPoint = item.datapoint;

                        jQuery("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(floting_point),
                            y = item.datapoint[1].toFixed(floting_point);
                        showGraphTooltip(item.pageX, item.pageY, x, y, event);

                    }

                } else {
                    jQuery("#tooltip").remove();
                    previousPoint = null;
                }
            });
        });
    }

    /* For analyze tab1 */
    jQuery('.analyze .tab1 a.plot-graph-button').click(function (e){
        e.preventDefault();

        fill_default_values(jQuery('.analyze .tab1 .controls-wrapper'));

        var current_tab = jQuery(this).parents('.controls-wrapper');
        var graph_id = jQuery(this).data('graph-id');
        var modal_id = jQuery(current_tab).find('select[name="tab1_chosemodel"]').val();
        var mytj = jQuery(current_tab).find('input[name="mytj"]').val();
        var imin = jQuery(current_tab).find('input[name="imin"]').val();
        var imax = jQuery(current_tab).find('input[name="imax"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {
                        action: graph_id,
                        modal_id: modal_id,
                        mytj: mytj,
                        imin: imin,
                        imax: imax,
            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({

                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,
                success: function(response){

                    if (response.error === false) {
                        
                        var series1 = {
                                            data: '',
                                            color: '#74C3E4',
                                            label: 'at '+mytj+'<sup>0</sup>C',
                                        };
                        var series2 = {
                                            data: '',
                                            color: '#439123',
                                            label: 'at 25<sup>0</sup>C',
                                        };
                        var series3 = {
                                            data: '',
                                            color: '#DA251D',
                                            label: 'at T<sub>jMAX<sub>',
                                        };

                        //Common options for all series
                        var options = {
                                        lines: { show: true },
                                        points: { show: true },
                                        legend: { position: 'nw' },
                                        grid: { hoverable: true },
                                    };

                        /* Plot VCEon graph */
                        if (response.data[0]) {
                            var VCE_response = response.data[0];
                            series1.data = VCE_response[0]; //user
                            series2.data = VCE_response[1]; //room
                            series3.data = VCE_response[2]; //Max
                            var graph1_series = [series1,series2,series3];
                            var graph_obj = jQuery.plot(jQuery("#tab1-graph1"), graph1_series, options);
                            store_graph_data(graph_obj, 0);
                        }
                        
                        /* Plot ETS graph */
                        if (response.data[1]) {
                            var ETS_response = response.data[1];
                            series1.data = ETS_response[0]; //user
                            series2.data = ETS_response[1]; //room
                            series3.data = ETS_response[2]; //Max
                            var graph2_series = [series1,series2,series3];
                            var graph_obj2 = jQuery.plot(jQuery("#tab1-graph2"), graph2_series, options);
                            store_graph_data(graph_obj2, 1);
                        }
                    } else {
                        var graph_obj = jQuery.plot(jQuery("#tab1-graph1"), '');
                        store_graph_data(graph_obj, 0);
                        var graph_obj2 = jQuery.plot(jQuery("#tab1-graph2"), '');
                        store_graph_data(graph_obj2, 1);
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                    }
                },
                error: function (error_obj, msg) {
                    console.log(error_obj);
                    var graph_obj = jQuery.plot(jQuery("#tab1-graph1"), '');
                    store_graph_data(graph_obj, 0);
                    var graph_obj2 = jQuery.plot(jQuery("#tab1-graph2"), '');
                    store_graph_data(graph_obj2, 1);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();
                }
            }).done(function (){
                jQuery('#ez-ajax-loader').hide();
            });

        }
    });

    /* Tab 2 analyze */
    jQuery('.analyze .tab2 .plot-graph-button').on( 'click', function (e){ //.analyze .tab2 .plot-graph-button

        e.preventDefault();

        fill_default_values(jQuery('.analyze .tab2 .controls-wrapper'));

        var graph_id = jQuery(this).data('graph-id');
        var modal_id = jQuery('.tab2 select').val();
        var current_tab = jQuery(this).parents('.controls-wrapper');
        var mytj = jQuery(current_tab).find('input[name="tab2_ip1"]').val();
        var myd = jQuery(current_tab).find('input[name="tab2_ip2"]').val();
        var myvdc = jQuery(current_tab).find('input[name="tab2_ip3"]').val();
        var myi = jQuery(current_tab).find('input[name="tab2_ip4"]').val();
        var fmin = jQuery(current_tab).find('input[name="tab2_ip5"]').val();
        var fmax = jQuery(current_tab).find('input[name="tab2_ip6"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {

                action: graph_id,
                model_name: modal_id,
                mytj: mytj,
                myd: myd,
                myvdc: myvdc,
                myi: myi,
                fmin: fmin,
                fmax: fmax

            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({

                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,

                success: function(response){

                    jQuery('#ez-ajax-loader').hide();
                    jQuery('#graph-msg').fadeOut();

                    if (response.error === false) {
                        
                        var series = {

                            data: response.data,
                            color: '#439123',
                            label: ' at '+mytj+'C',

                        };

                        //Common options for all series
                        var options = {
                            lines: { show: true },
                            points: { show: true },
                            legend: { position: 'nw' },
                            grid: { hoverable: true,
                                    markings: function(){

                                        var markingArr = [ 0.1, 0.2 , 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1, 2 , 
                                                           3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 
                                                           90, 100 ] // List all possible values

                                        var markings = [];
                                        fmin = parseInt(fmin);
                                        fmax = parseInt(fmax);

                                        var x = fmin;

                                        for( var i = 0; i<= markingArr.length; i++ ){

                                            if( markingArr[i] >= fmin && markingArr[i] <= fmax ){
       
                                                markings.push( { xaxis: { from: markingArr[i], to: markingArr[i] }, lineWidth: 0.2,  color:"#444" } );

                                            }
                                        }

                                        return markings;

                                    }

                            }, // grid ends here.
                            xaxis: {

                                ticks: getTicks(fmin, fmax),
                                tickDecimals:1,
                                tickColor: '#A0A0A0',
                                transform: log_base_10,
                                inverseTransform: antilog_base_10

                            }
                        };

                        var graph_obj = jQuery.plot( jQuery("#tab2-graph1"), [series], options);
                        store_graph_data(graph_obj, 0);
                        
                    } else {
                        var graph_obj = jQuery.plot(jQuery("#tab2-graph1"), '');
                        store_graph_data(graph_obj, 0);
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                    }

                },// ajax success function ends

                error: function (error_obj, msg) {
                    console.log(error_obj);
                    var graph_obj = jQuery.plot(jQuery("#tab2-graph1"), '');
                    store_graph_data(graph_obj, 0);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();
                }

            }); // ajax request ends

        } // if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined ))

    }); // tab2 plot on click function
    
    
    /* For analyze tab3 */
    jQuery('.analyze .tab3 a.plot-graph-button').click(function (e){
        e.preventDefault();
        
        fill_default_values(jQuery('.analyze .tab3 .controls-wrapper'));

        var current_tab = jQuery(this).parents('.controls-wrapper');
        var graph_id = jQuery(this).data('graph-id');
        var modal_id = jQuery(current_tab).find('select[name="tab3_chosemodel"]').val();
        var mytj = jQuery(current_tab).find('input[name="mytj"]').val();
        var myd = jQuery(current_tab).find('input[name="myd"]').val();
        var myrthcs = jQuery(current_tab).find('input[name="myrthcs"]').val();
        var myI = jQuery(current_tab).find('input[name="myI"]').val();
        var mytamb = jQuery(current_tab).find('input[name="mytamb"]').val();
        var mytsink = jQuery(current_tab).find('input[name="mytsink"]').val();
        var myvdc = jQuery(current_tab).find('input[name="myvdc"]').val();
        var fmin = jQuery(current_tab).find('input[name="fmin"]').val();
        var fmax = jQuery(current_tab).find('input[name="fmax"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {
                        action: graph_id,
                        modal_id: modal_id,
                        mytj: mytj,
                        myd : myd,
                        myrthcs : myrthcs,
                        myI : myI,
                        mytamb : mytamb,
                        mytsink : mytsink,
                        myvdc : myvdc,
                        fmin: fmin,
                        fmax: fmax,
            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({

                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,
                success: function(response){

                    if (response.error === false) {
                        
                        var series = {
                                        data: response.data,
                                        color: '#74C3E4',
                                        label: 'at '+mytj+'<sup>0</sup>C',
                                    };
                       
                        //Common options for all series
                        var options = {
                                        lines: { show: true },
                                        points: { show: true },
                                        legend: { position: 'nw' },
                                        grid: { hoverable: true,
                                                markings: function(){

                                                    var markingArr = [ 0.1, 0.2 , 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1, 2 , 
                                                                       3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 
                                                                       90, 100 ] // List all possible values

                                                    var markings = [];
                                                    fmin = parseInt(fmin);
                                                    fmax = parseInt(fmax);

                                                    var x = fmin;

                                                    for( var i = 0; i<= markingArr.length; i++ ){

                                                        if( markingArr[i] >= fmin && markingArr[i] <= fmax ){

                                                            markings.push( { xaxis: { from: markingArr[i], to: markingArr[i] }, lineWidth: 0.2,  color:"#444" } );

                                                        }
                                                    }

                                                    //console.log(markings)
                                                    return markings;

                                                }

                                        },
                                        xaxis: {

                                            ticks: getTicks(fmin, fmax, true ),
                                            tickDecimals:1,
                                            transform: log_base_10,
                                            inverseTransform: antilog_base_10

                                        }

                                    };

                        var graph_obj = jQuery.plot(jQuery('#'+graph_id), [series], options);
                        store_graph_data(graph_obj, 0);
                        
                    } else {
                        var graph_obj = jQuery.plot(jQuery('#'+graph_id), '');
                        store_graph_data(graph_obj, 0);
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                    }
                },
                error: function (error_obj, msg) {
                    console.log(error_obj);
                    var graph_obj = jQuery.plot(jQuery('#'+graph_id), '');
                    store_graph_data(graph_obj, 0);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();
                }
            }).done(function (){
                jQuery('#ez-ajax-loader').hide();
            });

        }
    });


    /* Analyze Tab 4 */
    jQuery('.analyze .tab4 .plot-graph-button').on( 'click', function (e){

        e.preventDefault();

        fill_default_values(jQuery('.analyze .tab4 .controls-wrapper'));

        var graph_id = jQuery(this).data('graph-id');
        var modal_id = jQuery('select[name="tab4_chosemodel"]').val();
        var mytj = jQuery('input[name="tab4_ip1"]').val();
        var myd = jQuery('input[name="tab4_ip2"]').val();
        var myf = jQuery('input[name="tab4_ip3"]').val();
        var myi = jQuery('input[name="tab4_ip4"]').val();
        var myvdc = jQuery('input[name="tab4_ip5"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {
                action: graph_id,
                modal_id: modal_id,
                mytj: mytj,
                myd: myd,
                myf: myf,
                myi: myi,
                myvdc: myvdc,
            };
            
            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({
                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,

                success: function(response){
                    jQuery('#ez-ajax-loader').hide();
                    jQuery('#graph-msg').fadeOut();

                    if (response.error === false) {

                        var series = {
                            data: response.data,
                            color: '#4982D1',
                        };

                        //Common options for all series
                        var options = {
                            lines: { show: false },
                            points:{ show: false },
                            grid: { hoverable: true },
                            bars: {     show: true, 
                                        lineWidth: 0,
                                        fill: true,
                                        align: "center",
                                        fillColor: { colors: ["#9EC0FD", "#4982D1"] }
                                  },
                            xaxis: {
                                        ticks: [[2, "IGBT cond"], [4, "IGBT sw"], [6, "Diode cond"], [8, "Diode sw"]]
                                    }
                                };

                        var graph_obj = jQuery.plot( jQuery("#"+graph_id), [series], options);
                        store_graph_data(graph_obj, 0);

                    } else {
                        var graph_obj = jQuery.plot(jQuery("#"+graph_id), '');
                        store_graph_data(graph_obj, 0);
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                    }

                },// ajax success function ends

                error: function (error_obj, msg) {
                    console.log(error_obj);
                    var graph_obj = jQuery.plot(jQuery("#"+graph_id), '');
                    store_graph_data(graph_obj, 0);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();
                }

            }); // ajax request ends

        } // if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined ))

    }); // tab4 plot on click function

    /* Analyze Tab 5 */
    jQuery('.analyze .tab5 .plot-graph-button').on( 'click', function (e){

        e.preventDefault();

        fill_default_values(jQuery('.analyze .tab5 .controls-wrapper'));

        var graph_id = jQuery(this).data('graph-id');
        var modal_id = jQuery('select[name="tab5_chosemodel"]').val();
        var mytj = jQuery('input[name="tab5_ip1"]').val();
        var myd = jQuery('input[name="tab5_ip2"]').val();
        var myrthcs = jQuery('input[name="tab5_ip3"]').val();
        var myvdc = jQuery('input[name="tab5_ip4"]').val();
        var tsink = jQuery('input[name="tab5_ip5"]').val();
        var fmin = jQuery('input[name="tab5_ip6"]').val();
        var fmax = jQuery('input[name="tab5_ip7"]').val();
        
        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {

                action: graph_id,
                modal_id: modal_id,
                mytj: mytj,
                myd: myd,
                myvdc: myvdc,
                fmin: fmin,
                fmax: fmax,
                tsink: tsink,
                myrthcs: myrthcs
            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({

                    type: "POST",
                    url: graph_ajaxurl,
                    dataType: 'json',
                    data: data,

                    success: function(response){

                            jQuery('#ez-ajax-loader').hide();
                            jQuery('#graph-msg').fadeOut();

                            if (response.error === false) {

                                    var series = {

                                            data: response.data,
                                            color: '#439123',
                                            label: ' at '+mytj+'C',

                                    };

                                    //Common options for all series
                                    var options = {

                                            lines: { show: true },
                                            points: { show: true },
                                            legend: { position: 'nw' },
                                            grid: { hoverable: true,
                                                    markings: function(){

                                                        var markingArr = [ 0.1, 0.2 , 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1, 2 , 
                                                        3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 
                                                        90, 100 ] // List all possible values

                                                        var markings = [];
                                                        fmin = parseInt(fmin);
                                                        fmax = parseInt(fmax);

                                                        var x = fmin;

                                                        for( var i = 0; i<= markingArr.length; i++ ){

                                                        if( markingArr[i] >= fmin && markingArr[i] <= fmax ){

                                                        markings.push( { xaxis: { from: markingArr[i], to: markingArr[i] }, lineWidth: 0.2,  color:"#444" } );

                                                        }
                                                        }

                                                        //console.log(markings)
                                                        return markings;

                                                    }

                                                   },
                                            xaxis: {

                                                ticks: getTicks(fmin, fmax, true ),
                                                tickDecimals:1,
                                                transform: log_base_10,
                                                inverseTransform: antilog_base_10

                                            }

                                    };

                                    var graph_obj = jQuery.plot( jQuery("#analyze_tab5"), [series], options);
                                    store_graph_data(graph_obj, 0);

                            } else {

                                    var graph_obj = jQuery.plot(jQuery("#analyze_tab5"), '');
                                    store_graph_data(graph_obj, 0);
                                    jQuery('#graph-msg').html(response.error_msg).fadeIn();
                            }

                    },// ajax success function ends

                    error: function (error_obj, msg) {

                            var graph_obj = jQuery.plot(jQuery("#analyze_tab5"), '');
                            store_graph_data(graph_obj, 0);
                            jQuery('#graph-msg').html(msg).fadeIn();
                            jQuery('#ez-ajax-loader').hide();
                    }

            }); // ajax request ends



            
        }


    }); // tab5 plot on click function

    /* For compare tab1 */
    jQuery('.compare .tab1 a.plot-graph-button').click(function (e){
        e.preventDefault();
        
        fill_default_values(jQuery('.compare .tab1 .controls-wrapper'));
        
        var current_tab = jQuery(this).parents('.controls-wrapper');
        var graph_id = jQuery(this).data('graph-id');
        var modal_id1 = jQuery(current_tab).find('select[name="tab1_chosemode1"]').val();
        var modal_id2 = jQuery(current_tab).find('select[name="tab1_chosemode2"]').val();
        var modal_id3 = jQuery(current_tab).find('select[name="tab1_chosemode3"]').val();
        var mytj = jQuery(current_tab).find('input[name="mytj"]').val();
        var imin = jQuery(current_tab).find('input[name="imin"]').val();
        var imax = jQuery(current_tab).find('input[name="imax"]').val();
        var currentvalidate = jQuery('input[name="validatecurrent"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {

                action: 'compare_tab1',
                modal_id1: modal_id1,
                modal_id2: modal_id2,
                modal_id3: modal_id3,
                mytj: mytj,
                imin: imin,
                imax: imax,
                currentvalidate:currentvalidate

            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            //Send Ajax Request
            jQuery.ajax({

                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,
                success: function(response){
                    
                    if ( response.error === false ) {
                        
                        var series1 = {
                                            data: '',
                                            color: '#74C3E4',
                                            label: 'Model '+modal_id1,
                                        };
                        var series2 = {
                                            data: '',
                                            color: '#439123',
                                            label: 'Model '+modal_id2,
                                        };
                        var series3 = {
                                            data: '',
                                            color: '#8B4789',
                                            label: 'Model '+modal_id3,
                                    };

                        var options = {
                            lines: { show: true },
                            points: { show: true },
                            legend: { position: 'nw' },
                            grid: { hoverable: true },
                        };

                        /* Plot VCEon graph */
                        if (response.data[0]) {

                            var VCE_response = response.data[0];
                            series1.data = VCE_response[0]; //user
                            series2.data = VCE_response[1]; //room
                            series3.data = VCE_response[2]; //max
                            var VCEon_series = [series1,series2,series3];

                            //Common options for all series
                            var graph_obj = jQuery.plot(jQuery("#tab1-graph1"), VCEon_series, options);
                            store_graph_data(graph_obj, 0);

                        }

                        /* Plot ETS graph */
                        if (response.data[1]) {

                            var ETS_response = response.data[1];
                            series1.data = ETS_response[0]; //user
                            series2.data = ETS_response[1]; //room
                            series3.data = ETS_response[2]; //max
                            var ETS_series = [series1,series2,series3];
                           
                            var graph_obj2 = jQuery.plot(jQuery("#tab1-graph2"), ETS_series, options);
                            store_graph_data(graph_obj2, 1);
                        }

                    }else {
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                        var graph_obj = jQuery.plot(jQuery("#tab1-graph1"), '');
                        store_graph_data(graph_obj, 0);
                        var graph_obj2 = jQuery.plot(jQuery("#tab1-graph2"), '');
                        store_graph_data(graph_obj2, 1);
                    }

                },
                error: function (error_obj, msg) {

                    console.log(error_obj);

                    var graph_obj = jQuery.plot(jQuery("#tab1-graph1"), '');
                    store_graph_data(graph_obj, 0);
                    var graph_obj2 = jQuery.plot(jQuery("#tab1-graph2"), '');
                    store_graph_data(graph_obj2, 1);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();

                }
            }).done(function (){
                jQuery('#ez-ajax-loader').hide();
            });

        }

    }); //Compare tab1 click function end

    /* For compare tab2 */
    jQuery('.compare .tab2 a.plot-graph-button').on('click', function(e){

        e.preventDefault();
        fill_default_values(jQuery('.compare .tab2 .controls-wrapper'));

        var current_tab = jQuery(this).parents('.controls-wrapper');
        var graph_id = jQuery(this).data('graph-id');
        var modal_id1 = jQuery('select[name="tab2_chosemodel"]').val();
        var modal_id2 = jQuery(current_tab).find('select[name="tab2_chosemode2"]').val();
        var modal_id3 = jQuery(current_tab).find('select[name="tab2_chosemode3"]').val();

        var myi = jQuery(current_tab).find('.tab2-myi input[name="myi"]').val();
        var mytj = jQuery(current_tab).find('.tab2-mytj input[name="mytj"]').val();
        var myd = jQuery(current_tab).find('.tab2-myd input[name="myd"]').val();
        var myvdc = jQuery(current_tab).find('.tab2-myvdc input[name="myvdc"]').val();
        var fmin = jQuery(current_tab).find('.tab2-fmin input[name="fmin"]').val();
        var fmax = jQuery(current_tab).find('.tab2-fmax input[name="fmax"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )){

            var data = {

                action: 'compare_tab2',
                modal_id1: modal_id1,
                modal_id2: modal_id2,
                modal_id3: modal_id3,
                mytj: mytj,
                myd:myd,
                myi:myi,
                myvdc:myvdc,
                fmin: fmin,
                fmax: fmax

            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({

                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,

                success: function(response){

                    if ( response.error === false ) {

                        var series1 = {
                            data: '',
                            color: '#74C3E4',
                            label: 'Model '+modal_id1,
                        };
                        var series2 = {
                            data: '',
                            color: '#439123',
                            label: 'Model '+modal_id2,
                        };
                        var series3 = {
                            data: '',
                            color: '#8B4789',
                            label: 'Model '+modal_id3,
                        };

                        var options = {
                            lines: { show: true },
                            points: { show: true },
                            legend: { position: 'nw' },
                            grid: { hoverable: true,

                                    markings: function(){

                                        var markingArr = [ 0.1, 0.2 , 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1, 2 , 
                                                           3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 
                                                           90, 100 ] // List all possible values

                                        var markings = [];
                                        fmin = parseInt(fmin);
                                        fmax = parseInt(fmax);

                                        var x = fmin;

                                        for( var i = 0; i<= markingArr.length; i++ ){

                                            if( markingArr[i] >= fmin && markingArr[i] <= fmax ){
       
                                                markings.push( { xaxis: { from: markingArr[i], to: markingArr[i] }, lineWidth: 0.2,  color:"#444" } );

                                            }
                                        }

                                        return markings;

                                    }

                            },
                            
                            xaxis:{

                                ticks: getTicks(fmin, fmax),
                                tickDecimals:1,
                                tickColor: '#A0A0A0',
                                transform: log_base_10,
                                inverseTransform: antilog_base_10

                            }
                        };

                        /* Plot VCEon graph */
                        if ( response.data ) {

                            var ploss_response = response.data;
                            series1.data = ploss_response[0]; //user
                            series2.data = ploss_response[1]; //room
                            series3.data = ploss_response[2]; //max
                            var ploss_series = [series1,series2,series3];

                            //Common options for all series
                            var graph_obj = jQuery.plot(jQuery("#tab2-graph1"), ploss_series, options);
                            store_graph_data(graph_obj, 0);

                        }                       

                    }else {

                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                        var graph_obj = jQuery.plot(jQuery("#tab2-graph1"), '');
                        store_graph_data(graph_obj, 0);

                    } // if ( response.error === false )
                },

                error: function (error_obj, msg) {

                    var graph_obj = jQuery.plot(jQuery("#tab2-graph1"), '');
                    store_graph_data(graph_obj, 0);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();

                }

            }).done(function (){

                jQuery('#ez-ajax-loader').hide();

            });

        } // if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined ))
    });

    /* For compare tab3  */
    jQuery('.compare .tab3 a.plot-graph-button').on('click', function(e){

            e.preventDefault();
            fill_default_values(jQuery('.compare .tab3 .controls-wrapper'));

            var current_tab = jQuery(this).parents('.controls-wrapper');
            var graph_id = jQuery(this).data('graph-id');
            var modal_id1 = jQuery(current_tab).find('select[name="tab3_chosemodel"]').val();
            var modal_id2 = jQuery(current_tab).find('select[name="tab3_chosemode2"]').val();
            var modal_id3 = jQuery(current_tab).find('select[name="tab3_chosemode3"]').val();
            var mytj = jQuery(current_tab).find('input[name="mytj"]').val();
            var myd = jQuery(current_tab).find('input[name="myd"]').val();
            var myrthcs = jQuery(current_tab).find('input[name="myrthcs"]').val();
            var myI = jQuery(current_tab).find('input[name="myI"]').val();
            var mytamb = jQuery(current_tab).find('input[name="mytamb"]').val();
            var mytsink = jQuery(current_tab).find('input[name="mytsink"]').val();
            var myvdc = jQuery(current_tab).find('input[name="myvdc"]').val();
            var fmin = jQuery(current_tab).find('input[name="fmin"]').val();
            var fmax = jQuery(current_tab).find('input[name="fmax"]').val();

            if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

                    var data = {

                        action: graph_id,
                        modal_id1: modal_id1,
                        modal_id2: modal_id2,
                        modal_id3: modal_id3,
                        mytj: mytj,
                        myd : myd,
                        myrthcs : myrthcs,
                        myI : myI,
                        mytamb : mytamb,
                        mytsink : mytsink,
                        myvdc : myvdc,
                        fmin: fmin,
                        fmax: fmax,

                    };
                    
                    jQuery('#ez-ajax-loader').show();
                    jQuery('#graph-msg').fadeOut();
                    
                    jQuery.ajax({

                        type: "POST",
                        url: graph_ajaxurl,
                        dataType: 'json',
                        data: data,
                        success: function(response){

                            if (response.error === false) {

                                var series1 = {
                                    data: response.data[0],
                                    color: '#74C3E4',
                                    label: 'Model '+modal_id1,
                                };
                                var series2 = {
                                    data: response.data[1],
                                    color: '#439123',
                                    label: 'Model '+modal_id2,
                                };
                                var series3 = {
                                    data: response.data[2],
                                    color: '#8B4789',
                                    label: 'Model '+modal_id3,
                                };

                                //Common options for all series
                                var options = {
                                                lines: { show: true },
                                                points: { show: true },
                                                legend: { position: 'nw' },
                                                grid: { hoverable: true,

                                                markings: function(){

                                                    var markingArr = [ 0.1, 0.2 , 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1, 2 , 
                                                                       3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 
                                                                       90, 100 ] // List all possible values

                                                    var markings = [];
                                                    fmin = parseInt(fmin);
                                                    fmax = parseInt(fmax);

                                                    var x = fmin;

                                                    for( var i = 0; i<= markingArr.length; i++ ){

                                                        if( markingArr[i] >= fmin && markingArr[i] <= fmax ){

                                                            markings.push( { xaxis: { from: markingArr[i], to: markingArr[i] }, lineWidth: 0.2,  color:"#444" } );

                                                        }
                                                    }

                                                    return markings;

                                                }

                                                },

                                                xaxis: {

                                                    ticks: getTicks(fmin, fmax, true ),
                                                    tickDecimals:1,
                                                    transform: log_base_10,
                                                    inverseTransform: antilog_base_10

                                                }
                                            };

                                var graph_obj = jQuery.plot(jQuery('#'+graph_id), [series1,series2,series3], options);
                                store_graph_data(graph_obj, 0);

                            } else {

                                var graph_obj = jQuery.plot(jQuery('#'+graph_id), '');
                                store_graph_data(graph_obj, 0);
                                jQuery('#graph-msg').html(response.error_msg).fadeIn();
                            }
                        },
                        error: function (error_obj, msg) {
                            
                             console.log(error_obj);

                            var graph_obj = jQuery.plot(jQuery('#'+graph_id), '');
                            store_graph_data(graph_obj, 0);
                            jQuery('#graph-msg').html(msg).fadeIn();
                            jQuery('#ez-ajax-loader').hide();
                        }
                    }).done(function (){
                        jQuery('#ez-ajax-loader').hide();
                    });
            } 
    });

    /* Compare Tab 4 */
    jQuery('.compare .tab4 .plot-graph-button').on( 'click', function (e){

        e.preventDefault();
        fill_default_values(jQuery('.compare .tab4 .controls-wrapper'));

        var current_tab = jQuery(this).parents('.controls-wrapper');
        var graph_id = jQuery(this).data('graph-id');
        var modal_id1 = jQuery('select[name="tab4_chosemodel"]').val();
        var modal_id2 = jQuery('select[name="tab4_chosemode2"]').val();
        var modal_id3 = jQuery('select[name="tab4_chosemode3"]').val();
        var mytj = jQuery(current_tab).find('input[name="mytj"]').val();
        var myd = jQuery(current_tab).find('input[name="myd"]').val();
        var myf = jQuery(current_tab).find('input[name="myf"]').val();
        var myi = jQuery(current_tab).find('input[name="myi"]').val();
        var myvdc = jQuery(current_tab).find('input[name="myvdc"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {
                action : graph_id,
                modal_id1 : modal_id1,
                modal_id2 : modal_id2,
                modal_id3 : modal_id3,
                mytj: mytj,
                myd: myd,
                myf: myf,
                myi: myi,
                myvdc: myvdc,
            };
            
            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({
                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,

                success: function(response){
                    jQuery('#ez-ajax-loader').hide();
                    jQuery('#graph-msg').fadeOut();

                    if (response.error === false) {

                        var series1 = {
                            data: response.data[0],
                            color: '#7A9FCF',
                            label: 'Model: '+modal_id1,
                            bars: {
                                fillColor: { colors: ["#7A9FCF", "#87B1E5"] }
                            }
                        };
                        var series2 = {
                            data: response.data[1],
                            color: '#CF3A3A',
                            label: 'Model: '+modal_id2,
                            bars: {
                                fillColor: { colors: ["#CF3A3A", "#D66262"] }
                            }
                        };
                        var series3 = {
                            data: response.data[2],
                            color: '#8FDEB2',
                            label: 'Model: '+modal_id3,
                            bars: {
                                fillColor: { colors: ["#8FDEB2", "#ADEAC8"] }
                            }
                        };

                        //Common options for all series
                        var options = {
                            lines: { show: false },
                            points:{ show: false },
                            legend: { position: 'nw' },
                            grid: { hoverable: true },
                            bars: {     show: true, 
                                        lineWidth: 0,
                                        fill: true,
                                        align: "center",
                                  },
                            xaxis: {
                                        ticks: [[2, "IGBT cond"], [6, "IGBT sw"], [10, "Diode cond"], [14, "Diode sw"]]
                                    }
                        };
                                
                        var graph_obj = jQuery.plot( jQuery("#"+graph_id), [series1,series2,series3], options);
                        store_graph_data(graph_obj, 0);

                    } else {
                        var graph_obj = jQuery.plot(jQuery("#"+graph_id), '');
                        store_graph_data(graph_obj, 0);
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                    }

                },// ajax success function ends

                error: function (error_obj, msg) {
                    console.log(error_obj);
                    var graph_obj = jQuery.plot(jQuery("#"+graph_id), '');
                    store_graph_data(graph_obj, 0);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();
                }

            }); // ajax request ends

        } // if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined ))
    });

    /* Compare tab5 */
    jQuery('.compare .tab5 .plot-graph-button').on( 'click', function (e){

        e.preventDefault();

        fill_default_values(jQuery('.compare .tab5 .controls-wrapper'));

        var graph_id  = jQuery(this).data('graph-id');
        var modal_id1 = jQuery('select[name="tab5_chosemodel"]').val();
        var modal_id2 = jQuery('select[name="tab5_chosemode1"]').val();
        var modal_id3 = jQuery('select[name="tab5_chosemode2"]').val();
        var mytj = jQuery('input[name="tab5_ip1"]').val();
        var myd = jQuery('input[name="tab5_ip2"]').val();
        var myrthcs = jQuery('input[name="tab5_ip3"]').val();
        var myvdc = jQuery('input[name="tab5_ip4"]').val();
        var tsink = jQuery('input[name="tab5_ip5"]').val();
        var fmin = jQuery('input[name="tab5_ip6"]').val();
        var fmax = jQuery('input[name="tab5_ip7"]').val();

        if ( ( graph_id !== undefined ) && ( graph_ajaxurl !== undefined )) {

            var data = {

                action: graph_id,
                modal_id1: modal_id1,
                modal_id2: modal_id2,
                modal_id3: modal_id3,
                mytj: mytj,
                myd: myd,
                myvdc: myvdc,
                fmin: fmin,
                fmax: fmax,
                tsink: tsink,
                myrthcs: myrthcs

            };

            jQuery('#ez-ajax-loader').show();
            jQuery('#graph-msg').fadeOut();

            jQuery.ajax({
                type: "POST",
                url: graph_ajaxurl,
                dataType: 'json',
                data: data,

                success: function(response){
                    jQuery('#ez-ajax-loader').hide();
                    jQuery('#graph-msg').fadeOut();

                    if (response.error === false) {

                        var series1 = {

                            data: response.data[0],
                            color: '#7A9FCF',
                            label: 'Model: '+modal_id1
                        };

                        var series2 = {
                            data: response.data[1],
                            color: '#CF3A3A',
                            label: 'Model: '+modal_id2
                        };

                        var series3 = {
                            data: response.data[2],
                            color: '#8FDEB2',
                            label: 'Model: '+modal_id3
                        };

                        //Common options for all series
                        var options = {

                            lines: { show: true },
                            points: { show: true },
                            legend: { position: 'nw' },
                            grid: { hoverable: true,
                                markings: function(){

                                    var markingArr = [ 0.1, 0.2 , 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1, 2 , 
                                    3, 4, 5, 6, 7, 8, 9, 10, 20, 30, 40, 50, 60, 70, 80, 
                                    90, 100 ] // List all possible values

                                    var markings = [];
                                    fmin = parseInt(fmin);
                                    fmax = parseInt(fmax);

                                    var x = fmin;

                                    for( var i = 0; i<= markingArr.length; i++ ){

                                        if( markingArr[i] >= fmin && markingArr[i] <= fmax ){

                                            markings.push( { xaxis: { from: markingArr[i], to: markingArr[i] }, lineWidth: 0.2,  color:"#444" } );

                                        }
                                    }

                                    //console.log(markings)
                                    return markings;
                                }

                            },
                            
                            xaxis: {

                                ticks: getTicks(fmin, fmax, true ),
                                tickDecimals:1,
                                transform: log_base_10,
                                inverseTransform: antilog_base_10

                            }
                        };

                        var graph_obj = jQuery.plot( jQuery("#"+graph_id), [series1,series2,series3], options);
                        store_graph_data(graph_obj, 0);

                    } else {
                        var graph_obj = jQuery.plot(jQuery("#"+graph_id), '');
                        store_graph_data(graph_obj, 0);
                        jQuery('#graph-msg').html(response.error_msg).fadeIn();
                    }

                },// ajax success function ends

                error: function (error_obj, msg) {
                    console.log(error_obj);
                    var graph_obj = jQuery.plot(jQuery("#"+graph_id), '');
                    store_graph_data(graph_obj, 0);
                    jQuery('#graph-msg').html(msg).fadeIn();
                    jQuery('#ez-ajax-loader').hide();
                }

            }).done(function (){
                jQuery('#ez-ajax-loader').hide();
            });

        }

    }); // tab5 plot on click function

    // Recommend tab
    jQuery('.recommend .plot-graph-button').on( 'click', function (e){

        e.preventDefault();
        fill_default_values( jQuery('.recommend .controls-wrapper') );
        var data = jQuery("#recommend-form").serialize();
        var elem = jQuery(this);

        var ajaxdata = {

            action: 'recommend',
            data: data
        };

        jQuery('#recommend-table').css('opacity',0);
        jQuery('#ez-ajax-loader').hide();
        jQuery('#graph-msg').hide();

        jQuery( "#recommend-table tbody tr" ).hide();

         jQuery.ajax({

            type: "POST",
            url: graph_ajaxurl,
            dataType: 'json',
            data: ajaxdata,

            success: function( response ){

                console.log(response);

                if( response.error === false ){

                    var models = response.data.models;
                    var plosses= response.data.plosses;

                    for( var i=0; i< response.data.models.length; i++ ){

                        jQuery('tr.col-'+(i+1)+' .igbt').html(response.data.models[i]);
                        jQuery('tr.col-'+(i+1)+' .ploss').html( parseFloat(response.data.plosses[i]).toFixed(1) );
                        jQuery('tr.col-'+(i+1)+' .pconds').html( parseFloat(response.data.pconds[i]).toFixed(1) );
                        jQuery('tr.col-'+(i+1)+' .psws').html( parseFloat(response.data.psws[i]).toFixed(1) );
                        jQuery('tr.col-'+(i+1)+' .deltatj').html( parseFloat(response.data.deltaTjs[i]).toFixed(1) );

                        jQuery( "#recommend-table tr.col-"+(i+1) ).show();

                    }

                    jQuery('#recommend-table').css('opacity',1);

                    jQuery('body').data('recommend', response.data);

                }else {

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

}); // jQuery document ready function

/* Custom functions */
/**
 * Tooltip Callback
 * @param {type} xPOS Top positon of point
 * @param {type} yPOS Left positon of point
 * @param {type} x Value of x point
 * @param {type} y Value of y point
 * @param {type} event Event object
 * @returns {undefined}  */
function showGraphTooltip(xPOS, yPOS, x, y, event) {

    var contents = '';
    var x_label = '';
    var y_label = '';
    var sep = false;

    if (typeof event.currentTarget !== 'undefined') {
        var selector = event.currentTarget;
        x_label = jQuery(selector).parent('.axis-wrapper').children('.ez-xaxis').html();
        y_label = jQuery(selector).parent('.axis-wrapper').children('.ez-yaxis').html();
    }

    if (x_label && typeof x_label !== 'undefined') {
        contents = x_label + '=' + x;
        sep = true;
    }
    if (y_label && typeof y_label !== 'undefined') {
        if (sep) {
            contents += ', ';
        }
        contents += y_label + '=' + y;
    }

    jQuery('<div id="tooltip">' + contents + '</div>').css({
        top: yPOS + 15,
        left: xPOS + 5,
    }).appendTo("body").fadeIn(200);
}

/**
 * Fill default values from data attributes
 * @param {type} wrapper_obj
 * @returns {undefined}
 */
function fill_default_values(wrapper_obj) {

    var model_data = false;
    if (wrapper_obj.length > 0) {
        jQuery(wrapper_obj).find('input[type="text"]').each(function (index, elem){

            /* Compare 1 fixing, for max current */
            if( jQuery.trim( jQuery(elem).val() ) === '' && jQuery(elem).attr('name') === 'imax' && jQuery('body').hasClass('compare') && jQuery(wrapper_obj).parent().hasClass('tab1') ){

                var all_select = jQuery(wrapper_obj).parents('.tabcontainer').find('select');
                var maxValue = Math.max( parseInt( jQuery(all_select[0]).find(':selected').data('default') ), parseInt(jQuery(all_select[1]).find(':selected').data('default')), parseInt(jQuery(all_select[2]).find(':selected').data('default')) );
                jQuery(this).prev('label').hide();
                jQuery(this).val(maxValue*8);
                jQuery('input[name="validatecurrent"]').val(0);
                return true;
            }else if( jQuery.trim( jQuery(elem).val() ) !== '' && jQuery(elem).attr('name') === 'imax' && jQuery('body').hasClass('compare') && jQuery(wrapper_obj).parent().hasClass('tab1') ){

                jQuery('input[name="validatecurrent"]').val(1);
            }
                

            var current_value = jQuery(this).val();
            var default_value = false;
            if (jQuery('body').hasClass('analyze') && jQuery(this).hasClass('model-data')) {

                default_value = jQuery(wrapper_obj).find('select').find(':selected').data('default');

                if( ( ( current_value !== default_value) && (current_value !== '') ) && ( ( typeof localStorage.lastpart !=='undefined' ) && ( localStorage.lastpart === jQuery(wrapper_obj).find('select').find(':selected').val() ) ) )
                    model_data = false;
                else
                    model_data = true;

                localStorage.lastpart = jQuery(wrapper_obj).find('select').find(':selected').val();

            } else {
                model_data = false;
                default_value = jQuery(this).data('default');
            }

            /* Update Values of fields */
            if (model_data) {
                jQuery(this).prev('label').hide();
                jQuery(this).val(default_value);
            } else if (!current_value && ( default_value || default_value ===0 )) {
                jQuery(this).prev('label').hide();
                jQuery(this).val(default_value);
            }

        });
    }
}

/**
 * Validate input of user
 * @param {obj} element
 * @returns {String|Boolean}
 */
function validate_value(element) {
    var value = jQuery(element).val();
    var parent_span = jQuery(element).parent('span');
    
    /* For rounding of integers */
    if (jQuery(parent_span).hasClass('round')) {
        if( jQuery.trim( value ) !== '' ){
            if( isNaN( parseInt( value ) ) ){
                return 'Please enter an integer value';
            }else{
                value = Math.round( value );
                jQuery(element).val(value);
            }
        }
    } 
    
    /* Check and alert for integers only inputs */
    if (jQuery(parent_span).hasClass('intonly')) {
        if( jQuery.trim( value ) !== '' ){
            if( isNaN( parseInt( value ) ) ){
                return 'Please enter an integer value';
            }
        }
    }
    
    return true;
}

/**
 * Check input logical conditions before sending request
 * @param {obj} element
 * @returns {bool} true if cond is true else false
 */
function check_ajax_req_cond(element) {

    var class_name = '';
    if (jQuery(element).parent('span').hasClass('cond-iminimax')) {
        class_name = 'cond-iminimax';
    } else if (jQuery(element).parent('span').hasClass('cond-all-models')) {
        class_name = 'cond-all-models';
    }
    
    if (class_name) {
        var all_elements = jQuery(element).parents('.controls-wrapper').find('.'+class_name);
        for (var i = 1; i <= all_elements.length; i++) {
            var single_elem = all_elements[i-1];
            var input_value = jQuery(single_elem).children('input, select').val();
            if (input_value === '') {
                return false;
            }
        }
    }
    
    return true;
}

/**
 * Store graph data points into body data
 * @param {type} graph_obj Returning object of graph
 * @param {type} index Index of array for storing graph data. for first graph 0 and for seceond graph 1
 * @returns {undefined}
 */
function store_graph_data(graph_obj, index) {

    var data = graph_obj.getData();
    var points = new Array();
    var data_to_store = new Array('', '');
    var previous_data = jQuery('body').data('graph_data') ? jQuery('body').data('graph_data') : new Array('', '');

    data_to_store[0] = previous_data[0]; /* for double graph: Store previous value of graph one if it is storing second graph values */
    data_to_store[1] = ''; /* for single graph: remove another 2nd graph value  */
    
    for (var prop in data) {
        if (typeof data[prop] !== 'undefined') {
            points.push(data[prop].data);
        }
    }
    
    data_to_store[index] = points;
    jQuery('body').data('graph_data', data_to_store);
}

/**
 * Calculate log base 10
 * @param {type} v
 * @returns {unresolved}
 */
function log_base_10(v) {

    var a = Math.log(v) / Math.log(20);
    return a;
}

/**
 * Calculate antilog base 10
 * @param {type} v
 * @returns {@exp;Math@call;pow}
 */
function antilog_base_10(v) {

    return Math.pow(20, v);
}

/**
 * Return the ticks for x-axis
 * @param {type} fmin
 * @param {type} fmax
 * @returns {getTicks.ticks|Array}
 */
function getTicks( fmin, fmax, heatsink ){

    var ticks = [];
    var finalTicks = [];

    fmin = parseFloat(fmin);
    fmax = parseFloat(fmax);

    if( heatsink )
        fmax = fmax;

    var plausibleTicks  =   [0.1,1,10,100]

    for( var i=0; i<= plausibleTicks.length; i++ ){

        if( plausibleTicks[i] >= fmin && plausibleTicks[i] <= fmax ){

            finalTicks.push( plausibleTicks[i] );

        }
    }

    if(  ( finalTicks.length === 0 ) || ( jQuery.inArray(fmin , finalTicks) < 0 ) ){

            finalTicks.push( fmin );
    }

    return finalTicks;

}