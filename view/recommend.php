<!-- Recommend Page -->
<section class="recommend tabs-wrapper clearfix">

    <!-- Graph containing div -->
    <div class="graph-holder">

        <div id="graph-msg" class="error"></div>

            <!-- Tab 1 -->
            <div class="tab1 tabcontainer">

                <div class="controls-wrapper">

                    <?php vit_render_input( 'control intonly', 'myvdc', 'My DC Bus Voltage is', '', '', 'V', '', 320, 'My DC Bus Voltage is' ); ?>

                    <?php vit_render_input( 'control round', 'myI', 'My I Load is', '', '', 'A', 'model-data', 1, 'Enter Current Load' ); ?>

                     <?php vit_render_input( 'control intonly', 'mytj', 'My T<sub>j</sub>', 'o', '', 'C', '', 100, 'Enter an approximate junction temperature you expect the IGBT to reach during power conversion' ); ?>

                     <?php vit_render_input( 'control intonly', 'mytj', 'My F<sub>pwm</sub> is', 'o', '', 'C', '', 100, 'Enter Fpwm' ); ?>

                    <?php vit_render_input( 'control', 'tab4_ip5', 'My V<sub>dc</sub> is', '', '', 'V', NULL, 320, 'What is the expected DC bus voltage of your Inverter, UPS or motor drive' ); ?>

                    <div class="below-line-control center plotme">
                        <?php vit_render_models( 'control onchange_ajax', 'tab1_chosemodel', 'You can choose from many discrete IGBTs made by companies such as IR, Infineon, Fairchild, Toshiba, Renesas, ST, IXYS, ON etc' ) ?>
                    </div>

                    <div class="center plotme"><a data-graph-id="tab1_graph1" class="plot-graph-button" onclick="return false;" href="#">Recommend</a></div>

                    <div class="tab-graphcontainer clearfix" id="tab2-graphcontainer">

                        <table width="60%" border="0" cellspacing="0" cellpadding="0">

                           <thead>
                            <tr>
                              <td align="center" valign="middle">Part Number</td>
                              <td align="center" valign="middle">Pcond</td>
                              <td align="center" valign="middle">PSw</td>
                              <td align="center" valign="middle">Ptotal</td>
                              <td align="center" valign="middle">dT<sub>jc</sub></td>
                            </tr>
                           </thead>

                            <tbody>

                            <tr class="col-1 odd">
                              <td align="center" valign="middle">IRGB4036</td>
                              <td align="center" valign="middle">5</td>
                              <td align="center" valign="middle">7</td>
                              <td align="center" valign="middle">12</td>
                              <td align="center" valign="middle">&nbsp;</td>
                            </tr>

                            <tr class="col-2 even">
                              <td align="center" valign="middle">STGX4560</td>
                              <td align="center" valign="middle">4</td>
                              <td align="center" valign="middle">8</td>
                              <td align="center" valign="middle">12</td>
                              <td align="center" valign="middle">&nbsp;</td>
                            </tr>

                            <tr class="col-1 odd">
                              <td align="center" valign="middle">IFX12004</td>
                              <td align="center" valign="middle">3</td>
                              <td align="center" valign="middle">8</td>
                              <td align="center" valign="middle">11</td>
                              <td align="center" valign="middle">&nbsp;</td>
                            </tr>

                            <tr class="col-2 even">
                              <td align="center" valign="middle">TOSxg65</td>
                              <td align="center" valign="middle">6</td>
                              <td align="center" valign="middle">6</td>
                              <td align="center" valign="middle">12</td>
                              <td align="center" valign="middle">&nbsp;</td>
                            </tr>

                            <tr class="col-1 odd">
                              <td align="center" valign="middle">IRGB4036</td>
                              <td align="center" valign="middle">6</td>
                              <td align="center" valign="middle">5</td>
                              <td align="center" valign="middle">11</td>
                              <td align="center" valign="middle">&nbsp;</td>
                            </tr>

                          </tbody>

                        </table>

                    </div>

                </div>

                <?php vit_render_action_buttons() ?>

            </div>
    </div>

</section>