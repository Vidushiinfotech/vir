$(document).ready(function(){jQuery(".ez-tab-list-wrapper a").click(function(b){b.preventDefault();jQuery(".tab-content-wrapper .tabcontent").hide();var a=jQuery(this).attr("href");jQuery(a).fadeIn();jQuery(".ez-tab-list-wrapper li").removeClass("active");jQuery(this).parent("li").addClass("active")});jQuery("body").addClass("has-js");$("a.topopup").click(function(){var a=$(this).attr("href");loading();setTimeout(function(){loadPopup(a)},500);return false});$("div.close").hover(function(){$("span.ecs_tooltip").show()},function(){$("span.ecs_tooltip").hide()});$("div.close").click(function(){disablePopup()});$(this).keyup(function(a){if(a.which==27){disablePopup()}});$("div#backgroundPopup").click(function(){disablePopup()});jQuery(".accordion .visible").on("click",function(){var a=false;if(jQuery(this).hasClass("expand")){jQuery(this).next().slideUp();jQuery(this).removeClass("expand");return false}jQuery(".accordion .visible").each(function(){jQuery(this).removeClass("expand")});if(true){jQuery(".visible").next().slideUp();jQuery(this).next().slideDown();jQuery(this).addClass("expand")}});jQuery("#cform-submit").on("click",function(){alert("hi")})});popupStatus=0;function loading(){$("div.loader").show()}function closeloading(){$("div.loader").fadeOut("normal")}function loadPopup(a){if(a&&popupStatus==0){closeloading();$(a).fadeIn(320);$("#backgroundPopup").css("opacity","0.9");$("#backgroundPopup").fadeIn(1);popupStatus=1}}function disablePopup(){if(popupStatus==1){$(".popup-content").fadeOut();$("#backgroundPopup").fadeOut();popupStatus=0}}function setupLabel(){if($(".label_check input").length){$(".label_check").each(function(){$(this).removeClass("c_on")});$(".label_check input:checked").each(function(){$(this).parent("label").addClass("c_on")})}if($(".label_radio input").length){$(".label_radio").each(function(){$(this).removeClass("r_on")});$(".label_radio input:checked").each(function(){$(this).parent("label").addClass("r_on")})}}$(document).ready(function(){$(".label_check, .label_radio").click(function(){setupLabel()});setupLabel()});jQuery(document).ready(function(a){jQuery(".subscript").on("click",function(){jQuery(this).hide();jQuery(this).prev().focus()});jQuery(".sub").on("blur",function(){var b=jQuery(this).val();b=jQuery.trim(b);if(b===""){jQuery(this).next().show();jQuery(this).val("")}})});