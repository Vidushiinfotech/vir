<?php
/*
 * Footer file
 */
?>

            </div><!-- End Content Wrapper -->
            
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
    </body>
</html>
