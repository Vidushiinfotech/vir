<!-- Analyze Page -->
<section class="tabs-wrapper clearfix">

    <!-- Tab containing div -->
    <div class="tab-holder">

        <ul class="page-tabs">

            <li data-tab="tab1" class="selected">Analyse Vce and Ets at my T<sub>j</sub></li>
            <li data-tab="tab2">P<sub>loss</sub> vs freq at my T<sub>j</sub> and D</li>
            <li data-tab="tab3">What heatsink R<sub>th</sub> do I need?</li>
            <li data-tab="tab4">Show me the split in losses</li>
            <li data-tab="tab5">Calculate I vs f curve</li>

        </ul>

    </div>

    <!-- Graph containing div -->
    <div class="graph-holder">
        
        <div id="graph-msg" class="error"></div>

        <!-- Tab 1 -->
        <div class="tab1 tabcontainer">

            <h2 class="tabcontent-title">Calculate Vce and Ets at my operating T<sub>j</sub></h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control', 'tab1_chosemodel' ) ?>

                <?php vit_render_input( 'control', 'mytj', 'My T<sub>j</sub> is =', '0', '', 'C', 'igbt-input mytj' ); ?>

                <span class="plot-range-text">Plot this range:</span>

                <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>

                <?php vit_render_input( 'control round', 'imax', 'I<sub>max</sub>', '', '', 'A' ); ?>

                <div class="center plotme"><a data-graph-id="tab1_graph1" class="plot-graph-button" href="#">Plot</a></div>

            </div>

            <div class="tab-graphcontainer clearfix" id="tab1-graphcontainer">
                <div class="axis-wrapper alignleft clearfix">
                    <span class="ez-xaxis">I<sub>c</sub></span>
                    <span class="ez-yaxis"><span>Vceon</span></span>
                    <div id="tab1-graph1"></div>
                </div>
                <div class="axis-wrapper alignright clearfix">
                    <span class="ez-xaxis">I<sub>c</sub></span>
                    <span class="ez-yaxis"><span>Ets</span></span>
                    <div id="tab1-graph2"></div>
                </div>
                
            </div>

            <?php vit_render_action_buttons() ?>

        </div>

        <!-- Tab 2 -->
        <div class="tab2 tabcontainer clearfix">

            <h2 class="tabcontent-title">Ploss vs freq at my T<sub>j</sub>  and D</h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control', 'tab2_chosemodel' ) ?>

                <?php vit_render_input( 'control', 'tab2_ip1', '0', '', 'C' ); ?>

                <?php vit_render_input( 'control', 'tab2_ip2', '', '', '%' ); ?>

                <?php vit_render_input( 'control', 'tab2_ip3', '', '', 'V' ); ?>

                <div class="below-line-control center plotme">

                    <?php vit_render_input( 'control', 'tab2_ip4', '', 'z', 'H' ); ?>

                    <span class="separator">to</span>

                    <?php vit_render_input( 'control', 'tab2_ip5', '', 'z', 'H' ); ?>

                </div>

                <div class="below-line-control center plotme">
                    <a class="plot-graph-button" href="#">Plot</a>
                </div>

                <div class="tab-graphcontainer clearfix" id="tab2-graphcontainer">
                    <div id="tab2-graph1"></div>
                </div>

            </div>

            <?php vit_render_action_buttons() ?>

        </div>

        <!-- Tab 3 -->
        <div class="tab3 tabcontainer clearfix">

            <h2 class="tabcontent-title">What heatsink R<sub>th</sub> do I need?</h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control', 'tab3_chosemodel' ) ?>

                <?php vit_render_input( 'control', 'tab3_ip1', '0', '', 'C' ); ?>

                <?php vit_render_input( 'control', 'tab3_ip2', '', '', '%' ); ?>

                <?php vit_render_input( 'control', 'tab3_ip3', '0', '', 'C/w' ); ?>

                <div class="below-line-control center plotme">

                    <?php vit_render_input( 'control', 'tab3_ip4', '0', '', 'C' ); ?>

                    <?php vit_render_input( 'control', 'tab3_ip5', '0', '', 'C' ); ?>
                    
                    <?php vit_render_input( 'control', 'tab3_ip6', '', '', 'V' ); ?>

                </div>

                <div class="below-line-control center plotme">

                    <span>Plot this range:</span>

                    <?php vit_render_input( 'control', 'tab3_ip7', '', 'z', 'H' ); ?>

                    <?php vit_render_input( 'control', 'tab3_ip8', '', 'z', 'H' ); ?>

                </div>

                <div class="below-line-control center plotme">
                    <a href="#">Plot</a>
                </div>

                <div class="tab-graphcontainer clearfix" id="tab3-graphcontainer">
                    <div id="tab3-graph1"></div>
                </div>

            </div>

            <?php vit_render_action_buttons() ?>

        </div>

        <!-- Tab 4 -->
        <div class="tab4 tabcontainer clearfix">

            <h2 class="tabcontent-title">Show me the split in losses</h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control', 'tab4_chosemodel' ) ?>

                <?php vit_render_input( 'control', 'tab4_ip1', '0', '', 'C' ); ?>
                
                <?php vit_render_input( 'control', 'tab4_ip2', '', '', '%' ); ?>
                
                <?php vit_render_input( 'control', 'tab4_ip3', '', 'z', 'H' ); ?>

                <div class="below-line-control center plotme">

                    <?php vit_render_input( 'control', 'tab4_ip4', '', '', 'V' ); ?>

                </div>

                <div class="below-line-control center plotme">
                    <a href="#">Plot</a>
                </div>

                <?php vit_render_action_buttons() ?>

            </div>

        </div>

        <!-- Tab 5 -->
        <div class="tab5 tabcontainer clearfix">

            <h2 class="tabcontent-title">Calculate I vs f curve</h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control', 'tab5_chosemodel' ) ?>

                <?php vit_render_input( 'control', 'tab5_ip1', '0', '', 'C' ); ?>

                <?php vit_render_input( 'control', 'tab5_ip2', '', '', '%' ); ?>

                <?php vit_render_input( 'control', 'tab5_ip3', '0', '', 'C/w' ); ?>

                <div class="below-line-control center plotme">

                    <?php vit_render_input( 'control', 'tab5_ip4', '', '', 'V' ); ?>

                    <?php vit_render_input( 'control', 'tab5_ip5', '0', '', 'C' ); ?>

    </div>

                <div class="below-line-control center plotme">

                    <span>Plot this range:&nbsp;&nbsp;</span>

                    <?php vit_render_input( 'control', 'tab5_ip6', '', 'z', 'H' ); ?>

                    <?php vit_render_input( 'control', 'tab5_ip7', '', 'z', 'H' ); ?>

                </div>

            </div>

        </div>

    </div>

</section>