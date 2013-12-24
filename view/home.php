<?php
/**
 * Home Template
 */
if( !empty( $_POST ) ){

    $response = vit_add_subscribers();

} ?>
<div class="message">
    <div class="box">This is highly visual, beautiful depiction of an IGBT in a circuit</div>
</div>
<h1 class="center">The fastest way to select the Perfect IGBT</h1>
<div class="home-content clear clearfix">
    <div class="left">
        <h2>Get Started</h2>
        <ul>
        <li class="model"><a href="<?php echo return_site_url() . 'index.php?page=analyze'; ?>">Model a Part</a></li>
        <li class="compare"><a href="<?php echo return_site_url() . 'index.php?page=compare'; ?>">Compare Parts</a></li>
        <li class="recommend"><a href="<?php echo return_site_url() . 'index.php?page=recommend'; ?>">Get Recommendations</a></li>
        </ul>
    </div>
    <div class="right">
        <h2>Get early access and updates</h2>
        <?php if( !empty( $_POST ) ) echo $response; ?>
        <p>Be the first to know when we add powerful tools and new IGBT’s to help you find the right IGBT for your circuit. Subscribe for updates</p>
    <form action="." method="POST" class="sign-up-form">
        <input required="required" name="user_email" type="email" value="" placeholder="Email Address" />
        <input type="submit" value="Submit" id="cform-submit" />
        </form>
    </div>
</div>