<?php
/**
 * Signup template 
 */
?>

<h1 class="center">Sign Up for Updates</h1>
<div class="signup-form clear">
    <form class="signp">

        <div>
            <label class="lebel">Email:</label>
            <input name="" type="text" class="text" />
        </div>
        
        <div>
            <label class="lebel">My primary application is</label>
            <select name="primary_application" id="primary-application">
                <option value="Appliance Motor Drives">Appliance Motor Drives</option>
                <option value="Industrial Motor Drives">Industrial Motor Drives</option>
                <option value="UPS">UPS</option>
                <option value="Solar Inverters">Solar Inverters</option>
                <option value="General Purpose Inverter">General Purpose Inverter</option>
                <option value="PFC">PFC</option>
                <option value="Welding">Welding</option>
                <option value="Power Supplies">Power Supplies</option>
                <option value="HID">HID</option>
                <option value="Other Application">Other Application</option>
            </select>
            
        </div>

        <div>
            <label class="lebel">Please enter the word 
                as shown below:</label>
            <input name="" type="text" class="text" />
            <div class="captcha"><img src="images/captcha.jpg" width="243" height="64" /></div>
        </div>

        <div class="btn-grn">
            <span><a href="#">Submit</a></span>
        </div>
        <span class="cancel"><a href="#">Cancel</a></span>
    </form>
</div>