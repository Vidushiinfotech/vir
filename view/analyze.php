<!-- Analyze Page -->
<section class="analyze tabs-wrapper clearfix">

    <!-- Tab containing div -->
    <div class="tab-holder">

        <ul class="page-tabs"><?php
            $all_calc_status = get_calc_status();

            echo $all_calc_status[1] ? '<li data-tab="tab1">Analyse Vce and Ets at my T<sub>j</sub></li>' : '';
            echo $all_calc_status[2] ? '<li data-tab="tab2">P<sub>loss</sub> vs freq at my T<sub>j</sub> and D</li>' : '';
            echo $all_calc_status[3] ? '<li data-tab="tab3">What heatsink R<sub>th</sub> do I need?</li>' : '';
            echo $all_calc_status[4] ? '<li data-tab="tab4">Show me the split in losses</li>' : '';
            echo $all_calc_status[5] ? '<li data-tab="tab5">Calculate I vs f curve</li>' : ''; ?>

        </ul>

    </div>

    <!-- Graph containing div -->
    <div class="graph-holder">

        <div id="graph-msg" class="error"></div><?php
            if ($all_calc_status[1]) { ?>

                <!-- Tab 1 -->
                <div class="tab1 tabcontainer">

                    <h2 class="tabcontent-title">Calculate Vce and Ets at my operating T<sub>j</sub></h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax', 'tab1_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_input( 'control intonly', 'mytj', 'My T<sub>j</sub> is =', 'o', '', 'C', 'igbt-input mytj', 25, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>

                        <span class="plot-range-text">Plot this range:</span>

                        <?php vit_render_input( 'control round cond-iminimax', 'imin', 'I<sub>min</sub>', '', '', 'A', NULL, NULL, 'Enter lowest range of current.' ); ?>

                        <?php vit_render_input( 'control round cond-iminimax', 'imax', 'I<sub>max</sub>', '', '', 'A', NULL, NULL, 'Enter largest range of current.' ); ?>

                        <div class="center plotme"><a data-graph-id="tab1_graph1" class="plot-graph-button" href="#">Plot</a></div>
                        
                        <div class="tab-graphcontainer clearfix" id="tab1-graphcontainer">
                            <div class="axis-wrapper alignleft clearfix">
                                <span class="ez-xaxis">I<sub>c</sub>(A)</span>
                                <span class="ez-yaxis"><span>Vceon(V)</span></span>
                                <div id="tab1-graph1"></div>
                            </div>
                            <div class="axis-wrapper alignright clearfix">
                                <span class="ez-xaxis">I<sub>c</sub>(A)</span>
                                <span class="ez-yaxis"><span>Ets(&micro;J)</span></span>
                                <div id="tab1-graph2"></div>
                            </div>
                        </div>

                    </div>

                    

                    <?php vit_render_action_buttons() ?>

                </div><?php
                
            }
            
            if ($all_calc_status[2]) { ?>

                <!-- Tab 2 -->
                <div class="tab2 tabcontainer clearfix">

                    <h2 class="tabcontent-title">Ploss vs freq at my T<sub>j</sub> and D</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax', 'tab2_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_input( 'control intonly', 'tab2_ip1', 'My T<sub>j</sub>', 'o', '' ,'C', '', 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>

                        <?php vit_render_input( 'control intonly', 'tab2_ip2', 'My D is', '', '', '%', '', 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>

                        <?php vit_render_input( 'control intonly', 'tab2_ip3', 'My V<sub>dc</sub> is', '', '', 'V', '', 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>

                        <div class="below-line-control center plotme">

                            <?php vit_render_input( 'control round', 'tab2_ip4', 'My I is', '', '', 'A', 'model-data', 1, 'Enter the value of current.' ); ?>

                            <?php vit_render_input( 'control cond-iminimax', 'tab2_ip5', 'F<sub>min</sub>', '', 'z', 'KH', '', 1, 'Enter the min PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <span class="separator">to</span>

                            <?php vit_render_input( 'control cond-iminimax', 'tab2_ip6', 'F<sub>max</sub>', '', 'z', 'KH', '', 10, 'Enter the max PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                        </div>

                        <div class="below-line-control center plotme">
                            <a data-graph-id="tab2_graph1" class="plot-graph-button kela" href="#">Plot</a>
                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab2-graphcontainer">
                            <div class="axis-wrapper clearfix">
                                <span class="ez-xaxis">Fsw(KHz)</span>
                                <span class="ez-yaxis"><span>P<sub>loss</sub>(w)</span></span>
                                <div id="tab2-graph1"></div>
                            </div>
                        </div>
                    </div>

                    <?php vit_render_action_buttons() ?>

                </div><?php 

            }

        
//        use these titles for fields
//        My Rthcs is: What is the thermal resistance of your ‘isolator’ such as SilPad?
//        My Tamb is: What is the max ambient surrounding Temperature around the Heatsink?
//        My Tsink is: What do you expect your Heatsink Temperature to be?
//        My f is: At what frequency do you want to estimate the split in losses for conduction and switching losses for Diode and IGBT
            
            if ($all_calc_status[3]) { ?>
                
                <!-- Tab 3 -->
                <div class="tab3 tabcontainer clearfix">

                    <h2 class="tabcontent-title">What heatsink R<sub>th</sub> do I need?</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax', 'tab3_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_input( 'control intonly', 'mytj', 'My T<sub>j</sub>', 'o', '', 'C', '', 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>

                        <?php vit_render_input( 'control intonly', 'myd', 'My D', '', '', '%', '', 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>

                        <?php vit_render_input( 'control intonly', 'myrthcs', 'My R<sub>thcs</sub>', 'o', '', 'C/w', '', 1, 'What is the thermal resistance of your ‘isolator’ such as SilPad?' ); ?>

                        <div class="below-line-control center plotme">
                            
                            <?php vit_render_input( 'control round', 'myI', 'My I', '', '', 'A', 'model-data', 1, 'Enter the value of current.' ); ?>

                            <?php vit_render_input( 'control intonly', 'mytamb', 'My T<sub>amb</sub>', 'o', '', 'C', '', 25, 'What is the max ambient surrounding Temperature around the Heatsink?' ); ?>

                            <?php vit_render_input( 'control intonly', 'mytsink', 'My T<sub>sink</sub>', 'o', '', 'C', '', 80, 'What do you expect your Heatsink Temperature to be?' ); ?>

                            <?php vit_render_input( 'control intonly', 'myvdc', 'My V<sub>dc</sub>', '', '', 'V', '', 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>

                        </div>

                        <div class="below-line-control center plotme">

                            <span>Plot this range:</span>

                            <?php vit_render_input( 'control cond-iminimax', 'fmin', 'F<sub>min</sub>', '', 'z', 'KH', '', 1, 'Enter the min PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <?php vit_render_input( 'control cond-iminimax', 'fmax', 'F<sub>max</sub>', '', 'z', 'KH', '', 10, 'Enter the max PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                        </div>

                        <div class="below-line-control center plotme">
                            <a data-graph-id="analyze_tab3" href="#" class="plot-graph-button">Plot</a>
                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab3-graphcontainer">
                            <div class="axis-wrapper clearfix">
                                <span class="ez-xaxis">f&nbsp;(KHz)</span>
                                <span class="ez-yaxis"><span>R<sub>th</sub>&nbsp;(C/W)</span></span>
                                <div id="analyze_tab3"></div>
                            </div>
                        </div>

                    </div>

                    <?php vit_render_action_buttons() ?>

                </div><?php 
                
            }
            
            if ($all_calc_status[4]) { ?>

                <!-- Tab 4 -->
                <div class="tab4 tabcontainer clearfix">

                    <h2 class="tabcontent-title">Show me the split in losses</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax', 'tab4_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_input( 'control intonly', 'tab4_ip1', 'My T<sub>j</sub> is', 'o', '', 'C', NULL, 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion'); ?>

                        <?php vit_render_input( 'control', 'tab4_ip2', 'My D is', '', '', '%', NULL, 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>

                        <?php vit_render_input( 'control', 'tab4_ip3', 'My f is', '', 'z', 'KH', NULL, 20, 'At what frequency do you want to estimate the split in losses for conduction and switching losses for Diode and IGBT'  ); ?>

                        <div class="below-line-control center plotme">

                            <?php vit_render_input( 'control round', 'tab4_ip4', 'My I is', '', '', 'A', '', 4, 'Enter the value of current.' ); ?>

                            <?php vit_render_input( 'control', 'tab4_ip5', 'My V<sub>dc</sub> is', '', '', 'V', NULL, 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>

                        </div>

                        <div class="below-line-control center plotme">
                            <a data-graph-id="tab4-graph1" class="plot-graph-button" href="#">Plot</a>
                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab4-graphcontainer">
                            <div class="axis-wrapper clearfix alignleft">
                                <!--<span class="ez-xaxis">X axis</span>-->
                                <span class="ez-yaxis"><span>watts</span></span>
                                <div id="tab4-graph1"></div>
                            </div>
                        </div>

                        <?php vit_render_action_buttons() ?>

                    </div>

                </div><?php
                
            }
            
            if ($all_calc_status[5]) { ?>

                <!-- Tab 5 -->
                <div class="tab5 tabcontainer clearfix">

                    <h2 class="tabcontent-title">Calculate I vs f curve</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax', 'tab5_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_input( 'control intonly', 'tab5_ip1', 'My T<sub>j</sub> is', 'o', '', 'C', NULL, 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion'); ?>

                        <?php vit_render_input( 'control', 'tab5_ip2', 'My D is', '', '', '%', NULL, 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>

                        <?php vit_render_input( 'control intonly', 'tab5_ip3', 'My R<sub>thcs</sub>', 'o', '', 'C/w', '', 1, 'What is the thermal resistance of your ‘isolator’ such as SilPad?' ); ?>

                        <div class="below-line-control center plotme">

                            <?php vit_render_input( 'control', 'tab5_ip4', 'My V<sub>dc</sub> is', '', '', 'V', NULL, 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>

                            <?php vit_render_input( 'control', 'tab5_ip5', 'My T<sub>sink</sub> is', 'o', '', 'C', NULL, 80, 'Enter Tsink' ); ?>

                        </div>

                        <div class="below-line-control center plotme">

                            <span>Plot this range:&nbsp;&nbsp;</span>

                            <?php vit_render_input( 'control cond-iminimax', 'tab5_ip6', 'F<sub>min</sub>', '', 'z', 'KH', '', 1, 'Enter the min PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <?php vit_render_input( 'control cond-iminimax', 'tab5_ip7', 'F<sub>max</sub>', '', 'z', 'KH', '', 10, 'Enter the max PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                        </div>

                        <div class="below-line-control center plotme">
                            <a data-graph-id="analyze_tab5" class="plot-graph-button" href="#">Plot</a>
                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab5-graphcontainer">
                            <div class="axis-wrapper clearfix alignleft">
                                <span class="ez-xaxis">F(KHz)</span>
                                <span class="ez-yaxis"><span>I&nbsp;(A)</span></span>
                                <div id="analyze_tab5"></div>
                            </div>
                        </div>

                        <?php vit_render_action_buttons() ?>

                    </div>

                </div><?php
                
            } ?>

    </div>

</section>
