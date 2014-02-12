<?php
/*
 * Footer file
 */
?>

            </div><!-- End Content Wrapper -->
            <div id="canvas" style="display: none;"></div>
            
            <footer id="footer-wrapper" class="clearfix">
                <ul class="submenu clearfix">
                    <li><a href="<?php echo return_site_url(); ?>">Home</a></li><?php
                    
                    global $EZ_DB;
                    $page_list = array();
                    $result = $EZ_DB->run_query("SELECT slug, title from pages", true);
                    
                    if (is_object($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $page_list[] = $row;
                        }
                    }

                    if ($page_list) {
                        foreach ($page_list as $page) { ?>
                            <li><a href="<?php echo return_site_url() . '' . $page['slug']; ?>"><?php echo $page['title']; ?></a></li><?php
                        }
                    } ?>
                </ul>
            </footer>

        </div><!-- End of Container Wrapper -->

        <!-- Pop-up divs -->
        <div class="pop-ups">

            <div id="report-popup" class="popup">

                <div class="report-popup-inside popup-inside">

                    <div class="popup-header clearfix">

                        <div class="head-part">
                            <img src="<?php echo VIT_IMG.'/logo.png' ?>" alt="logo" />
                        </div>
                        <div class="head-part thanks">
                            <img class="err-img" src="<?php echo VIT_IMG.'/error.jpg' ?>" />
                            <h3 class="thanks-msg">Thank you for reporting a problem with the calculator!</h3>
                        </div>
                        <img class="closepop" title="Close" src="<?php echo VIT_IMG.'/closebox.png' ?>" alt="close popup" />
                    </div>

                    <div class="popup-content clearfix">
                        
                        <div class="msg-div"></div>

                        <div class="radio">
                            <ul class="radio-list">
                                <li><input checked="checked" type="radio" value="Part number is wrong" name="bugradio" />Part number is wrong</li>
                                <li><input type="radio" value="Calculated values are wrong" name="bugradio" />Calculated values are wrong</li>
                                <li><input type="radio" value="Graph axes incorrect" name="bugradio" />Graph axes incorrect</li>
                                <li><input type="radio" value="Problems with downloading files" name="bugradio" />Problems with downloading files</li>
                                <li><input type="radio" value="Downloaded files do not match" name="bugradio" />Downloaded files do not match</li>
                                <li><input type="radio" value="Other" name="bugradio" />Other</li>
                            </ul>
                        </div>

                        <div class="and-or">
                            <span>and/or</span>
                        </div>

                        <div class="describe">
                            <div class="describe-text"><textarea placeholder="Describe issue here..." rows="5" cols="20" name="describe_text"></textarea></div>
                            <div>My email is... (optional) so we can thank you and send updates on new tools</div>
                            <div><input placeholder="Email" type="text" name="report-myemail" value="<?php echo empty( $_SESSION['user_email'] ) ? '' : $_SESSION['user_email']; ?>" /></div>
                            <div>
                                <input type="button" name="report_submit" class="report-submit" value="Submit" />
                                <img class="action-loader" src="<?php echo VIT_IMG.'/loader.gif' ?>" alt="Loader" />
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div id="samples-popup" class="popup">

                <div class="report-popup-inside popup-inside">

                    <div class="popup-header clearfix">

                        <div class="head-part">
                            <img src="<?php echo VIT_IMG.'/logo.png' ?>" alt="logo" />
                        </div>
                        <img class="closepop" title="Close" src="<?php echo VIT_IMG.'/closebox.png' ?>" alt="close popup" />
                    </div>

                    <div class="popup-content clearfix">

                        <div class="msg-div"></div>

                        <div class="coming-soon">
                            <h2 class="center">We are sorry</h2>
                            <div class="coming-msg">
                                <p>The feature you have chosen is in development. It will be available soon...</p>
                                <p>you can choose to 'Subscribe to Updates' to be notified when it is ready.</p>
                            </div>
                            <div class="center"><!--#14ABF1-->
                                <p><input class="coming-updates-email" placeholder="Email id" type="email" name="subemail" value="" /></p>
                                <p>
                                    <input class="coming-updates" type="button" name="subscribeme" value="Subscribe to updates" />
                                </p>
                                <p><img class="action-loader" src="<?php echo VIT_IMG.'/loader.gif' ?>" alt="Loader" /></p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div id="no-logged-in" class="popup">
                <div class="report-popup-inside popup-inside">
                    <h3 class="thanks-msg">Please login to perform this action</h3>
                    <img class="closepop" title="Close" src="<?php echo VIT_IMG.'/closebox.png' ?>" alt="close popup" />
                </div>
            </div>

        </div>

        <!-- Pop-up divs ends here -->
        
    </body>
</html>
