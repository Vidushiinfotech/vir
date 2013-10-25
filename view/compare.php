<!-- Analyze Page -->
<section class="tabs-wrapper compare clearfix">

    <!-- Tab containing div -->
    <div class="tab-holder">

        <ul class="page-tabs">

            <li data-tab="tab1" class="selected">Compare IGBTs for Vce and E<sub>ts</sub> at my T<sub>j</sub></li>
            <li data-tab="tab2">Compare P<sub>loss</sub> vs frequency for 3 IGBTs at my T<sub>j</sub> and D</li>
            <li data-tab="tab3">What heatsink R<sub>th</sub> do I need for the 3 IGBTs?</li>
            <li data-tab="tab4">Compare the split in losses for the IGBTs</li>
            <li data-tab="tab5">Compare I vs f curve</li>

        </ul>

    </div>

    <!-- Graph containing div -->
    <div class="graph-holder">
        
        <div id="graph-msg" class="error"></div>

        <!-- Tab 1 -->
        <div class="tab1 tabcontainer">

            <h2 class="tabcontent-title">Calculate Vce and Ets at my operating T<sub>j</sub></h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control zzzz', 'tab1_chosemodel' ) ?>

                <?php vit_render_models( 'control', 'tab1_chosemode2' ) ?>

                <?php vit_render_models( 'control', 'tab1_chosemode3' ) ?>

                <?php vit_render_input( 'control', 'mytj', 'My T<sub>j</sub> is =', '0', '', 'C', 'igbt-input mytj' ); ?>

                <div class="below-line-control center plotme">

                    <span class="plot-range-text">Plot this range:</span>

                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>

                    <?php vit_render_input( 'control round', 'imax', 'I<sub>max</sub>', '', '', 'A' ); ?>

                    <div class="center plotme">
                        <a data-graph-id="tab1_graph1" class="plot-graph-button" href="#">Plot</a>
                    </div>

                </div>

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
                
                <?php vit_render_models( 'control', 'tab2_chosemode2' ) ?>
                
                <?php vit_render_models( 'control', 'tab2_chosemode3' ) ?>

                <div class="below-line-control center plotme">
                    <?php vit_render_input( 'control tab2-mytj', 'mytj', 'MyT<sub>j</sub>', '', '', '<sup>0</sup>C' ); ?>
                    <?php vit_render_input( 'control tab2-myd', 'myd', 'My D is', '', '', '%' ); ?>
                    <?php vit_render_input( 'control tab2-myvdc', 'myvdc', 'My V<sub>dc</sub> is', '', '', 'V' ); ?>
                </div>

                <div class="below-line-control center plotme">

                    <?php vit_render_input( 'control tab2-myi', 'myi', 'My I is', '', '', 'A' ); ?>

                    <span class="plot-range-text">Plot this range:</span>

                    <?php vit_render_input( 'control tab2-fmin', 'fmin', 'F<sub>min</sub>', '', '', 'H<sub>z</sub>' ); ?>

                    <?php vit_render_input( 'control tab2-fmax', 'fmax', 'F<sub>max</sub>', '', '', 'H<sub>z</sub>' ); ?>

                    <div class="center plotme"><a data-graph-id="tab2_graph1" class="plot-graph-button" href="#">Plot</a></div>

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

                <?php vit_render_models( 'control', 'tab3_chosemode2' ) ?>

                <?php vit_render_models( 'control', 'tab3_chosemode3' ) ?>

                <div class="below-line-control center plotme">
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                </div>

                <div class="below-line-control center plotme">
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                </div>

                <div class="below-line-control center plotme">

                    <span class="plot-range-text">Plot this range:</span>

                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>

                    <?php vit_render_input( 'control round', 'imax', 'I<sub>max</sub>', '', '', 'A' ); ?>

                    <div class="center plotme"><a data-graph-id="tab2_graph1" class="plot-graph-button" href="#">Plot</a></div>

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
                <?php vit_render_models( 'control', 'tab4_chosemode2' ) ?>
                <?php vit_render_models( 'control', 'tab4_chosemode3' ) ?>

                <div class="below-line-control center plotme">
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                </div>

                <div class="below-line-control center plotme">
                    <a href="#">Plot</a>
                </div>

                <div class="tab-graphcontainer clearfix" id="tab3-graphcontainer">
                    <div id="tab3-graph1"></div>
                </div>

                <?php vit_render_action_buttons() ?>

            </div>

        </div>

        <!-- Tab 5 -->
        <div class="tab5 tabcontainer clearfix">

            <h2 class="tabcontent-title">Calculate I vs f curve</h2>

            <div class="controls-wrapper">

                <?php vit_render_models( 'control', 'tab4_chosemodel' ) ?>
                <?php vit_render_models( 'control', 'tab4_chosemode2' ) ?>
                <?php vit_render_models( 'control', 'tab4_chosemode3' ) ?>

                <div class="below-line-control center plotme">
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                </div>

                <div class="below-line-control center plotme">
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>
                </div>

                <div class="below-line-control center plotme">

                    <span class="plot-range-text">Plot this range:</span>

                    <?php vit_render_input( 'control round', 'imin', 'I<sub>min</sub>', '', '', 'A' ); ?>

                    <?php vit_render_input( 'control round', 'imax', 'I<sub>max</sub>', '', '', 'A' ); ?>

                    <div class="center plotme"><a data-graph-id="tab2_graph1" class="plot-graph-button" href="#">Plot</a></div>

                </div>

                <div class="tab-graphcontainer clearfix" id="tab3-graphcontainer">
                    <div id="tab3-graph1"></div>
                </div>

                <?php vit_render_action_buttons() ?>

            </div>

        </div>

    </div>

</section>
