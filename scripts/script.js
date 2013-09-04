/* 
	author: istockphp.com
*/
jQuery(function($) {
	
	// click trigger for popup 1
	$("a.topopup").click(function() {
			loading(); // loading
			setTimeout(function(){ // then show popup, deley in .5 second
				loadPopup(); // function show popup 
			}, 500); // .5 second
	return false;
	});
	// click trigger for popup 2
	$("a.topopup_2").click(function() {
			loading(); // loading
			setTimeout(function(){ // then show popup, deley in .5 second
				loadPopup_2(); // function show popup 
			}, 500); // .5 second
	return false;
	});
	// click trigger for popup 3
	$("a.topopup_3").click(function() {
			loading(); // loading
			setTimeout(function(){ // then show popup, deley in .5 second
				loadPopup_3(); // function show popup 
			}, 500); // .5 second
	return false;
	});
	// click trigger for popup 3
	$("a.topopup_4").click(function() {
			loading(); // loading
			setTimeout(function(){ // then show popup, deley in .5 second
				loadPopup_4(); // function show popup 
			}, 500); // .5 second
	return false;
	});
	
	/* event for close the popup */
	$("div.close").hover(
					function() {
						$('span.ecs_tooltip').show();
					},
					function () {
    					$('span.ecs_tooltip').hide();
  					}
				);
	
	$("div.close").click(function() {
		disablePopup();  // function close pop up
	});
	
	$(this).keyup(function(event) {
		if (event.which == 27) { // 27 is 'Ecs' in the keyboard
			disablePopup();  // function close pop up
		}  	
	});
	
	$("div#backgroundPopup").click(function() {
		disablePopup();  // function close pop up
	});
	
	$('a.livebox').click(function() {
		alert('Hello World!');
	return false;
	});
	
	
	 /************** start: functions. **************/
	function loading() {
		$("div.loader").show();  
	}
	function closeloading() {
		$("div.loader").fadeOut('normal');  
	}
	
	var popupStatus = 0; // set value
	
	// load popup 1
	function loadPopup() { 
		if(popupStatus == 0) { // if value is 0, show popup
			closeloading(); // fadeout loading
			$("#toPopup").fadeIn(0500); // fadein popup div
			$("#backgroundPopup").css("opacity", "0.9"); // css opacity, supports IE7, IE8
			$("#backgroundPopup").fadeIn(0001); 
			popupStatus = 1; // and set value to 1
		}	
	}
	// load popup 2
	function loadPopup_2() { 
		if(popupStatus == 0) { // if value is 0, show popup
			closeloading(); // fadeout loading
			$("#toPopup_2").fadeIn(0500); // fadein popup div
			$("#backgroundPopup").css("opacity", "0.9"); // css opacity, supports IE7, IE8
			$("#backgroundPopup").fadeIn(0001); 
			popupStatus = 1; // and set value to 1
		}	
	}
	// load popup 3
	function loadPopup_3() { 
		if(popupStatus == 0) { // if value is 0, show popup
			closeloading(); // fadeout loading
			$("#toPopup_3").fadeIn(0500); // fadein popup div
			$("#backgroundPopup").css("opacity", "0.9"); // css opacity, supports IE7, IE8
			$("#backgroundPopup").fadeIn(0001); 
			popupStatus = 1; // and set value to 1
		}	
	}
	// load popup 4
	function loadPopup_4() { 
		if(popupStatus == 0) { // if value is 0, show popup
			closeloading(); // fadeout loading
			$("#toPopup_4").fadeIn(0500); // fadein popup div
			$("#backgroundPopup").css("opacity", "0.9"); // css opacity, supports IE7, IE8
			$("#backgroundPopup").fadeIn(0001); 
			popupStatus = 1; // and set value to 1
		}	
	}
	
	// function close popups
	function disablePopup() {
		if(popupStatus == 1) { // if value is 1, close popup
			
			$("#toPopup").fadeOut("normal");  
			$("#toPopup_2").fadeOut("normal");  
			$("#toPopup_3").fadeOut("normal");  
			$("#toPopup_4").fadeOut("normal");
			
			$("#backgroundPopup").fadeOut("normal");  
			popupStatus = 0;  // and set value to 0
		}
	}
	
	/************** end: functions. **************/
}); // jQuery End