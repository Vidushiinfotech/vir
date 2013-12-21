<?php
// Global Database Connection
global $EZ_DB;

/* Feature Array */
$featuresArray = array(
    
    0=>'Analyze VCE & ETS at my TJ',
    1=>'Ploss vs. Frequency Curve at my Tj',
    2=>'What Heat sink RTH do I need?',
    3=>'Show me the split in losses',
    4=>'Calculate Irms vs. Frequency',
    5=>'Compare 3 IGBTs for VCE & ETS',
    6=>'Compare Ploss vs. frequency for 3 IGBTs',
    7=>'What HS RTH do I need for 3 IGBTs?',
    8=>'Compare split in losses for 3 IGBTs',
    9=>'Compare Irms vs. Frequency for 3 IGBTs',
   10=>'Recommend IGBTs for my Application',
);
?>

<div class="activation clearfix">
    
    <div style="display: none;">
        <img src="<?php echo VIT_IMG.'/show.png' ?>" alt="show" />
        <img src="<?php echo VIT_IMG.'/hide.png' ?>" alt="hide" />
        <img src="<?php echo VIT_IMG.'/active.png' ?>" alt="active" />
        <img src="<?php echo VIT_IMG.'/inactive.png' ?>" alt="inactive" />
    </div>

    <div class="feature-activation">

        <h3 class="activation-heading">Calculators</h3>

        <div class="activation-table">

            <div class="row odd clearfix">
                <div class="col feature-nmbr heading"><strong>Calculator Name</strong></div>
                <div class="col on-off heading"><strong>Activate / Deactivate</strong></div>
            </div><?php

            $query_get_features     =   "SELECT key_value FROM config WHERE key_name ='features_status'";
            $results    =   $EZ_DB->run_query( $query_get_features );
            
            if ( !$results ) { //If does not have features_status then insert it
                $initial_data = array(NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
                $initial_data_serialize = serialize($initial_data);
                $query_set_features  = "INSERT INTO config VALUES ( 'features_status', '$initial_data_serialize' )";
                $EZ_DB->run_query( $query_set_features );
            }
            
            $results = $EZ_DB->run_query( $query_get_features );
            $Data = unserialize( $results['key_value'] );
            
            for( $i=0; $i<=10; $i++ ){

                $class      =   ( !$i || ( $i % 2 == 0 ) ) ? ' even' : ' odd';
                $status     =   $Data[$i+1];
                $msg        =   ( $status ) ? 'Currently Active' : 'Currently Inactive';
                $src        =   ( $status ) ? VIT_IMG.'/inactive.png' : VIT_IMG.'/active.png';
                $title      =   ( $status ) ? 'Deactivate' : 'Activate';

                echo '<div class="row'.$class.' clearfix">';

                    echo '<div class="col feature-nmbr">'. $featuresArray[$i] .'</div>';

                    echo '<div class="col on-off">
                            <span>'. $msg .'</span>
                            <img data-featureno="'.( $i+1 ).'" data-status="'.$status.'" class="feature-toggle" src="'.$src.'" title="'.$title.'" alt="on-off" />
                          </div>';

                echo '</div>';

            } ?>

        </div>

    </div>

    <div class="model-activation">

        <h3 class="activation-heading">Parts</h3>

        <div class="activation-table"><?php

            /* Display Model Names */
            $query      =   "SELECT model_name, include_model FROM models ORDER BY model_name ASC LIMIT 50";
            $results    =   $EZ_DB->run_query( $query, 1 );
            $count = 0; ?>

            <div class="row odd clearfix heading">
                <div class="col model-cols heading model-heading"><strong>Part Names</strong></div>
                <div class="col on-off model-onoff heading model-heading"><strong>Show / Hide</strong></div>
                <div class="col model-delete heading model-heading"><strong>Delete Parts</strong></div>
            </div><?php

            while( $row = mysqli_fetch_assoc( $results ) ){

                $class      =   ( !$count || ( $count % 2 == 0 ) ) ? ' even' : ' odd';
                $message    =   ( $row['include_model'] ) ? 'Currently Shown' : 'Currently Hidden';
                $source     =   ( $row['include_model'] ) ?  VIT_IMG.'/hide.png' : VIT_IMG.'/show.png';
                $flag       =   ( $row['include_model'] ) ? 'show' : 'hide';
                $title       =   ( $row['include_model'] ) ? 'Hide' : 'Show';

                echo '<div class="row'. $class.' clearfix">';

                    echo '<div class="col model-cols">'. $row['model_name'] .'</div>';
                    echo '<div class="col on-off model-onoff">
                            <span class="onoffmsg">'. $message .'</span>
                            <img data-flag="'.$flag.'" data-part="'. $row['model_name'] .'" class="alter-parts" src="'.  $source .'" title="'.$title.'" alt="on-off" />
                          </div>';
                    echo '<div class="col model-delete"><a data-modelname="'. $row['model_name'] .'" href="#">Delete this part</a></div>';

                echo '</div>';

                $count++; // increment counter

            } ?>

            <div data-page="1" id="load-parts">Load More...</div>

        </div>
    </div>
</div>