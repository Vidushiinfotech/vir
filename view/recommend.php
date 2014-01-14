<!-- Recommend Page -->
<section class="recommend tabs-wrapper clearfix">

    <!-- Graph containing div -->
    <div class="graph-holder">

        <div id="graph-msg" class="error"></div>

            <!-- Tab 1 -->
            <div class="tab1 tabcontainer">

                <div class="controls-wrapper">

                    <form action="./" method="post" id="recommend-form" name="recommend_form">

                        <?php vit_render_input( 'control intonly', 'myvdc', 'My DC Bus Voltage is', '', '', 'V', '', 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>

                        <?php vit_render_input( 'control round', 'myI', 'My I Load is', '', '', 'A', 'model-data', 5, 'Enter Current Load' ); ?>

                         <?php vit_render_input( 'control intonly', 'mytj', 'My T<sub>j</sub>', 'o', '', 'C', '', 125, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>

                         <?php vit_render_input( 'control intonly', 'myf', 'My F<sub>pwm</sub> is', '', '', 'W', '', 16, 'Enter Fpwm' ); ?>

                        <?php vit_render_input( 'control intonly', 'myd', 'My D', '', '', '%', NULL, 50, 'My D in %' ); ?>

                        <div class="below-line-control center plotme">

                            <?php vit_render_input( 'control intonly', 'mytcase', 'My T<sub>case</sub>', 'o', '', 'C', '', 80, 'Enter Tcase' ); ?>

                            <?php //vit_render_models( 'control onchange_ajax', 'partmodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>

                        </div>

                    </form>

                    <div class="center plotme"><a data-graph-id="recommend" class="plot-graph-button" href="#">Recommend</a></div>

                    <div class="tab-graphcontainer clearfix" id="tab2-graphcontainer">

                        <table id="recommend-table" width="60%" border="0" cellspacing="0" cellpadding="0">

                           <thead>
                                <tr>
                                  <td align="center" valign="middle">Part Number</td>
                                  <td align="center" valign="middle">Ploss</td>
                                </tr>
                           </thead>

                            <tbody>

                                <tr class="col-1 odd">
                                  <td class="igbt" align="center" valign="middle">IRGB4036</td>
                                  <td class="ploss" align="center" valign="middle">5</td>
                                </tr>

                                <tr class="col-2 even">
                                  <td class="igbt" align="center" valign="middle">STGX4560</td>
                                  <td class="ploss" align="center" valign="middle">4</td>
                                </tr>

                                <tr class="col-3 odd">
                                  <td class="igbt" align="center" valign="middle">IFX12004</td>
                                  <td class="ploss" align="center" valign="middle">3</td>
                                </tr>

                          </tbody>

                        </table>

                    </div>

                </div>

                <?php vit_render_action_buttons() ?>

            </div>
    </div>

</section>