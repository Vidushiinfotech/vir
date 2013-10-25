    <!------content------>
    <div class="content">
    	<div class="recommend">
			<div class="action">
                <div class="line1">
                
                <span class="degr marginR20">
                <input type="text" onblur="if(value=='') value = 'My DC bus voltage is'" 
    onfocus="if(value=='My DC bus voltage is') value = ''" value="My DC bus voltage is" />
                
                <small class="unit">V</small></span>
                <span class="degr ">
                <input type="text" onblur="if(value==''){ value = 'My I load is =' }" 
    onfocus="if(value=='My I load is ='){ value = '' }" value="My I load is =" />
               <small class="unit">A</small></span>
                
                </div>
                <div class="line2">
                <span class="degr marginR20"><span class="rel"><input type="text" class="sub" value="" /><label class="subscript">My T<sub>j</sub> is =</label></span><small class="unit"><sup>o</sup>C</small></span>
                
                <span class="degr marginR20"><span class="rel"><input type="text" value="" class="sub" /><label class="subscript">My f<sub>pwm</sub> is=</label></span><small class="unit">HZ</small></span>
                <span class="degr"><span class="rel"><input type="text" value="" class="sub" /><label class="subscript">My V<sub>dc</sub> is</label></span><small class="unit">V</small></span>
                </div>
                <div class="line3">
                <span class="lft"><label>My Package type is</label></span>
                <span class="fl"><select class="select" size="1">
                  <option selected="selected">Package type</option>
                  <option>Package type</option>
                  <option>Package type</option>
                  <option>Package type</option>
                </select></span>
                </div>
                <div class="btn-grn recmd">
                    <span><a href="#">Recommend</a></span>
                </div>
                
            </div>
            <div class="table">
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
  <tr class="col-1">
    <td align="center" valign="middle">IRGB4036</td>
    <td align="center" valign="middle">5</td>
    <td align="center" valign="middle">7</td>
    <td align="center" valign="middle">12</td>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
  <tr class="col-2">
    <td align="center" valign="middle">STGX4560</td>
    <td align="center" valign="middle">4</td>
    <td align="center" valign="middle">8</td>
    <td align="center" valign="middle">12</td>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
  <tr class="col-1">
    <td align="center" valign="middle">IFX12004</td>
    <td align="center" valign="middle">3</td>
    <td align="center" valign="middle">8</td>
    <td align="center" valign="middle">11</td>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
  <tr class="col-2">
    <td align="center" valign="middle">TOSxg65</td>
    <td align="center" valign="middle">6</td>
    <td align="center" valign="middle">6</td>
    <td align="center" valign="middle">12</td>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
  <tr class="col-1">
    <td align="center" valign="middle">IRGB4036</td>
    <td align="center" valign="middle">6</td>
    <td align="center" valign="middle">5</td>
    <td align="center" valign="middle">11</td>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
  </tbody>
              </table>

            </div>
          <div class="button-holder">
                <span>
                <div class="btn-blu marginR10">
                    <span><a href="#" class="bug topopup">Report a bug</a></span>
                </div>
                </span>
                <span>
                <div class="btn-blu marginR10">
                    <span><a href="#" class="download topopup_2">Download PDF</a></span>
                </div>
                </span>
                <span>
                <div class="btn-blu marginR10">
                    <span><a href="#" class="download topopup_3">Download CSV</a></span>
                </div>
                </span>
                <span>
                <div class="btn-blu ">
                    <span><a href="#" class="arrow topopup_4">Get Samples</a></span>
                </div>
                </span>
            </div>
        </div>
   	    
    </div>
    <!------content------>

<!------Popup------>
<div id="toPopup">
	<div class="head">
    	<div class="poplogo"></div>
        <div class="error">
        	<span class="fl"><img src="images/error.jpg" width="64" height="57" /></span>
            <h3>Thank you for reporting a
problem with the calculator!</h3>
      </div>
        <div class="close"></div>
      <span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
        </div>
       	
		<div id="popup_content"> <!--your content start-->
        	<div class="bugreport-content">
            <div class="clear">
            	<div class="fl">
                	<form>
                   	  <p>
                    	  <label class="label_radio r_on">
                    	    <input type="radio" name="RadioGroup1" value="radio" checked="checked" id="RadioGroup1_0" />
                    	    Part number is wrong</label>
                   	    <br />
                    	  <label class="label_radio">
                    	    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1" />
                    	    calculated values are wrong</label>
                   	    <br />
                          <label class="label_radio">
                    	    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1" />
                    	    graph axes incorrect</label>
                    	  <br />
                          <label class="label_radio">
                    	    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1" />
                    	    problems with downloading files</label>
                    	  <br />
                          <label class="label_radio">
                    	    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1" />
                    	    downloaded files do not match</label>
                    	  <br />
                          <label class="label_radio">
                    	    <input type="radio" name="RadioGroup1" value="radio" id="RadioGroup1_1" />
                    	    other</label>
                    	  <br />
                          
                  	  </p>
                    </form>
                </div>
                <div class="fc">and/or</div>
                <div class="fr">
                	<textarea cols="" rows="6">Describe the issue...</textarea>
                    <p>My email is... (optional) so we can thank you and
send updates on new tools</p>
					<input name="" class="text" type="text" />
                    <div class="btn-blu center">
                    	<span><a href="#">Submit</a></span>
                </div>
              </div>
            </div>
			</div>
        </div> <!--your content end-->
    
    </div>
<div id="toPopup_2">
	<div class="head">
    	<div class="poplogo"></div>
        <div class="close"></div>
        <span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
        </div>
       	
		<div id="popup_content"> <!--your content start-->
        	<div class="download-content">
            <p>
            To be able to download the files,<br />
you will need to<br />
‘Subscribe to Updates’<br />
If you have done this already,<br /> 
either enter your email-id here<br />
or<br />
login on the homepage<br />

<small>Thank you for using ezIGBT!</small></p>
			<div class="clear">
            	<span class="fl"><input class="text" name="" onblur="if(value=='') value = 'Enter Email Id'" 
    onfocus="if(value=='Enter Email Id') value = ''" value="Enter Email Id" type="text" /></span>
                <span class="fr"><div class="btn-blu">
                    <span><a href="#">GO</a></span>
                </div></span>
            </div>
			</div>
        </div> <!--your content end-->
    
    </div>
<div id="toPopup_3">
	<div class="head">
    	<div class="poplogo"></div>
        <div class="close"></div>
        <span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
        </div>
       	
		<div id="popup_content"> <!--your content start-->
        	<div class="download-content">
            <p>
            To be able to download the files,<br />
you will need to<br />
‘Subscribe to Updates’<br />
If you have done this already,<br /> 
either enter your email-id here<br />
or<br />
login on the homepage<br />

<small>Thank you for using ezIGBT!</small></p>
			<div class="clear">
            	<span class="fl"><input class="text" name="" onblur="if(value=='') value = 'Enter Email Id'" 
    onfocus="if(value=='Enter Email Id') value = ''" value="Enter Email Id" type="text" /></span>
                <span class="fr"><div class="btn-blu">
                    <span><a href="#">GO</a></span>
                </div></span>
            </div>
			</div>
        </div> <!--your content end-->
    
    </div>
<div id="toPopup_4">
	<div class="head">
    	<div class="poplogo"></div>
        <div class="close"></div>
        <span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
        </div>
       	
		<div id="popup_content"> <!--your content start-->
        	<div class="comingsoon-content">
            <h3>We are Sorry</h3>
            <p>
            The feature you have chosen is in development.<br />
It will be available soon...<br /><br />

you can choose to ‘Subscribe to Updates’ to be notified when it is ready.</p>
			<div class="clear">
            	<div><input class="text" name="" onblur="if(value=='') value = 'Email Id'" 
    onfocus="if(value=='Email Id') value = ''" value="Email Id" type="text" /></div>
                <div class="btn-blu center">
                    <span><a href="#">Subscribe to Updates</a></span>
                </div>
            </div>
			</div>
        </div> <!--your content end-->
    
    </div>

<div class="loader"></div>
<div id="backgroundPopup"></div>