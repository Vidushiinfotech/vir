<?php
/**
 * Manage all admin and site options
 */
global $EZ_DB, $cms;

$uname  =   "SELECT username FROM users WHERE ID = ".$_SESSION['user_id'];
$uname  =   $EZ_DB->run_query($uname);

$analytics  =   "SELECT key_value from config where key_name='analytics'";
$analytics  =   $EZ_DB->run_query($analytics);

$analytics  =   !empty($analytics) ? ($analytics['key_value']) : '';

?>
<div class="admin-settings">

    <div class="manage-credentials manage-wrap">
        
        <h2>Manage Credentials</h2>

        <form id="manage-creds" method="post" action="./">

            <div class="response"></div>

            <div class="field">
                <label for="username">Username</label>
                <input type="text" value="<?php echo $uname['username'] ?>" name="username" />
            </div>

            <div class="field">
                <label for="password">New Password</label>
                <input type="password" name="password" value="" />
            </div>

            <input type="hidden" name="action" value="manage_creds" />
            
            <div class="field">
                <input type="submit" value="Submit" name="manage_creds_submit" />
            </div>

        </form>

    </div>

    <div class="manage-analytics manage-wrap">

        <h2>Analytics code</h2>

        <form id="analytics" method="post" action="./">

            <div class="response"></div>

            <div class="field">
                <textarea name="analytics_code" cols="5" rows="5"><?php echo $analytics ?></textarea>
            </div>
            <input type="hidden" name="action" value="analytics" />

            <div class="field">
                <input type="submit" value="Submit" name="analytics_submit" />
            </div>

        </form>

    </div>

    
</div>
<style>
    #admin-content { border: none }
</style>