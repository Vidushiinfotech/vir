<!-- Analyze Page -->
<section class="tabs-wrapper compare clearfix">

    <!-- Tab containing div -->
    <div class="tab-holder">

        <ul class="page-tabs"><?php
            $all_calc_status = get_calc_status();

            echo $all_calc_status[6] ? '<li data-tab="tab1" class="selected">Compare IGBTs for Vce and E<sub>ts</sub> at my T<sub>j</sub></li>' : '';
            echo $all_calc_status[7] ? '<li data-tab="tab2">Compare P<sub>loss</sub> vs frequency for 3 IGBTs at my T<sub>j</sub> and D</li>' : '';
            echo $all_calc_status[8] ? '<li data-tab="tab3">What heatsink R<sub>th</sub> do I need for the 3 IGBTs?</li>' : '';
            echo $all_calc_status[9] ? '<li data-tab="tab4">Compare the split in losses for the IGBTs</li>' : '';
            echo $all_calc_status[10] ? '<li data-tab="tab5">Compare I vs f curve</li>' : ''; ?>

        </ul>

    </div>

    <!-- Graph containing div -->
    <div class="graph-holder">
        
        <div id="graph-msg" class="error"></div><?php
             if ($all_calc_status[6]) { ?>

                <!-- Tab 1 -->
                <div class="tab1 tabcontainer">

                    <h2 class="tabcontent-title">Calculate Vce and Ets at my operating T<sub>j</sub></h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab1_chosemode1', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab1_chosemode2', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab1_chosemode3', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_input( 'control intonly', 'mytj', 'My T<sub>j</sub> is =', '0', '', 'C', 'igbt-input mytj', 25, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>

                        <div class="below-line-control center plotme">

                            <span class="plot-range-text">Plot this range:</span>

                            <?php vit_render_input( 'control round cond-iminimax', 'imin', 'I<sub>min</sub>', '', '', 'A', NULL, 0, 'Enter lowest range of current.' ); ?>

                            <?php vit_render_input( 'control round cond-iminimax', 'imax', 'I<sub>max</sub>', '', '', 'A', NULL, 4, 'Enter largest range of current.' ); ?>

                            <div class="center plotme">
                                <input type="hidden" name="validatecurrent" value="1" />
                                <a data-graph-id="tab1_graph1" class="plot-graph-button" href="#">Plot</a>
                            </div>

                        </div>

                    </div>

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

                    <?php vit_render_action_buttons() ?>

                </div><?php
             }

             if ($all_calc_status[7]) { ?>

                <!-- Tab 2 -->
                <div class="tab2 tabcontainer clearfix">

                    <h2 class="tabcontent-title">Ploss vs freq at my T<sub>j</sub>  and D</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab2_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab2_chosemode2', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab2_chosemode3', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <div class="below-line-control center plotme">
                            <?php vit_render_input( 'control tab2-mytj intonly', 'mytj', 'MyT<sub>j</sub>', '', '', '<sup>0</sup>C', NULL, 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>
                            <?php vit_render_input( 'control tab2-myd intonly', 'myd', 'My D is', '', '', '%', NULL, 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>
                            <?php vit_render_input( 'control tab2-myvdc intonly', 'myvdc', 'My V<sub>dc</sub> is', '', '', 'V', NULL, 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>
                        </div>

                        <div class="below-line-control center plotme">

                            <?php vit_render_input( 'control tab2-myi round intonly', 'myi', 'My I is', '', '', 'A', NULL, 10, 'Enter the value of current.' ); ?>

                            <span class="plot-range-text">Plot this range:</span>

                            <?php vit_render_input( 'control tab2-fmin intonly cond-iminimax', 'fmin', 'F<sub>min</sub>', '', '', 'KHz', NULL, 1, 'Enter the min PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <?php vit_render_input( 'control tab2-fmax intonly cond-iminimax', 'fmax', 'F<sub>max</sub>', '', '', 'KHz', NULL, 10, 'Enter the max PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <div class="center plotme"><a data-graph-id="tab2_graph1" class="plot-graph-button" href="#">Plot</a></div>

                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab2-graphcontainer">
                            <div class="axis-wrapper alignleft clearfix">
                                <span class="ez-xaxis"><span>f(KHz)</span></span>
                                <span class="ez-yaxis"><span>Ploss(W)</span></span>
                                <div id="tab2-graph1"></div>
                            </div>
                        </div>

                    </div>

                    <?php vit_render_action_buttons() ?>

                </div><?php
             }

             if ($all_calc_status[8]) { ?>

                <!-- Tab 3 -->
                <div class="tab3 tabcontainer clearfix">

                    <h2 class="tabcontent-title">What heatsink R<sub>th</sub> do I need?</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab3_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab3_chosemode2', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab3_chosemode3', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <div class="below-line-control center plotme">
                            <?php vit_render_input( 'control round', 'mytj', 'My Tj', '', '', '<sup>0</sup>C', '', 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>
                            <?php vit_render_input( 'control round', 'myd', 'My D', '', '', '%', '', 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>
                            <?php vit_render_input( 'control round', 'myrthcs', 'My R<sub>thcs</sub>', '', '', '<sup>0</sup>C/W', '', 1, 'What is the thermal resistance of your ‘isolator’ such as SilPad?' ); ?>
                            <?php vit_render_input( 'control round', 'myvdc', 'My V<sub>dc</sub>', '', '', 'V', '', 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>
                        </div>

                        <div class="below-line-control center plotme">
                            <?php vit_render_input( 'control round', 'myI', 'My I', '', '', 'A', '', 30, 'Enter the value of current.' ); ?>
                            <?php vit_render_input( 'control intonly', 'mytamb', 'My T<sub>amb</sub>', '', '', '<sup>0</sup>C', '', 25, 'What is the max ambient surrounding Temperature around the Heatsink?' ); ?>
                            <?php vit_render_input( 'control intonly', 'mytsink', 'My T<sub>sink</sub>', '', '', '<sup>0</sup>C', '', 80, 'What do you expect your Heatsink Temperature to be?' ); ?>
                        </div>

                        <div class="below-line-control center plotme">

                            <span class="plot-range-text">Plot this range:</span>

                            <?php vit_render_input( 'control intonly', 'fmin', 'F<sub>min</sub>', '', '', 'KH<sub>z</sub>', '', 1, 'Enter the min PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <?php vit_render_input( 'control intonly', 'fmax', 'F<sub>max</sub>', '', '', 'KH<sub>z</sub>', '', 10, 'Enter the max PWM switching frequency  of your PFC, Buck converter or other power conversion equipment' ); ?>

                            <div class="center plotme"><a data-graph-id="tab3-graph1" class="plot-graph-button" href="#">Plot</a></div>

                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab3-graphcontainer">
                            <div class="axis-wrapper alignleft clearfix">
                                <span class="ez-xaxis"><span>f(KHz)</span></span>
                                <span class="ez-yaxis"><span>R<sub>th</sub>(C/W)</span></span>
                                <div id="tab3-graph1"></div>
                            </div>
                        </div>

                    </div>

                    <?php vit_render_action_buttons() ?>

                </div><?php
             }
             
             if ($all_calc_status[9]) { ?>

                <!-- Tab 4 -->
                <div class="tab4 tabcontainer clearfix">

                    <h2 class="tabcontent-title">Show me the split in losses</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab4_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>
                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab4_chosemode2', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>
                        <?php vit_render_models( 'control onchange_ajax cond-all-models', 'tab4_chosemode3', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        <div class="below-line-control center plotme">
                            <?php vit_render_input( 'control round', 'mytj', 'My Tj', '', '', '<sup>0</sup>C', '', 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>
                            <?php vit_render_input( 'control round', 'myd', 'My D is', '', '', '%', NULL, 50, 'Enter Duty cycle of PWM operation. Ton/Ttotal.' ); ?>
                            <?php vit_render_input( 'control intonly', 'myf', 'My f is', '', 'z', 'KH', NULL, 20, 'At what frequency do you want to estimate the split in losses for conduction and switching losses for Diode and IGBT'  ); ?>
                            <?php vit_render_input( 'control intonly', 'myvdc', 'My V<sub>dc</sub> is', '', '', 'V', NULL, 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>
                        </div>
                        
                        <div class="below-line-control center plotme">
                            <?php vit_render_input( 'control round', 'myi', 'My I is', '', '', 'A', '', 4, 'Enter the value of current.' ); ?>
                        </div>

                        <div class="below-line-control center plotme">
                            <a data-graph-id="compare_tab4" class="plot-graph-button" href="#">Plot</a>
                        </div>

                        <div class="tab-graphcontainer clearfix" id="tab4-graphcontainer">
                            <div class="axis-wrapper clearfix alignleft">
                                 <span class="ez-yaxis"><span>watts</span></span>
                                 <div id="compare_tab4"></div>
                             </div>
                        </div>

                        <?php vit_render_action_buttons() ?>

                    </div>

                </div><?php
             }
             
             if ($all_calc_status[10]) { ?>

                <!-- Tab 5 -->
                <div class="tab5 tabcontainer clearfix">

                    <h2 class="tabcontent-title">Calculate I vs f curve</h2>

                    <div class="controls-wrapper">

                        <?php vit_render_models( 'control', 'tab4_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>
                        <?php vit_render_models( 'control', 'tab4_chosemode2', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>
                        <?php vit_render_models( 'control', 'tab4_chosemode3', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

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

                </div><?php
             } ?>

    </div>

</section>
