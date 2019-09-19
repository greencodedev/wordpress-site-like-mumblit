$(function(){
	var c = 0;
	var ct = $('.publisher-box .postText');
	var ic = $('.publisher_colorbox input[name="color"]');
	
	$('body').on('click', '.js_colorbox', function() {
        $(this).toggleClass('active');
        $('.publisher_colorbox').slideToggle('fast');
		ic.val('');
		ct.removeAttr('style').removeClass('color_ph').css({'height' : '75px'});
		$('.publisher_colorbox ._3oi9').removeClass('_23jt');
		$('.publisher_colorbox ._3oi9:first').addClass('_23jt');
		c = 0;
    });
	
	function publisher_color(c){
		var ct = $('.publisher-box .postText');
	    var ic = $('.publisher_colorbox input[name="color"]');
	    var gc = colorbox_array[c];
		
		if(gc['type'] == 1){
			ct.css('cssText','background-color:'+gc['c1']+' !important');
		} else if(gc['type'] == 2){
			ct.css('cssText','background-image:linear-gradient(45deg, '+gc['c1']+' 0%, '+gc['c2']+' 100%) !important');
		} else if(gc['type'] == 3){
			ct.css('cssText','background-image: url('+site_url+'/upload/colorbox/'+gc['src']+'.'+gc['filetype']+') !important');
		}
		
		if(c != 0){ 
			ct.addClass('color_ph');
			ct.css({'color':'#c2c2c2', 'font-size':'30px', 'font-weight':'700', 'line-height':'1.2em', 'padding':'100px 27px', 'text-align':'center', 'background-repeat': 'no-repeat', 'background-size': '100% 100%', 'height' : '245px'});
			ic.val(c);
		} else {
			ct.removeClass('color_ph').css({'height' : '75px'});
			ic.val('');
		}
	}
	
	$('body').on('click', '.publisher_colorbox .btn', function() {
        $('.publisher_colorbox ._3oi9').removeClass('_23jt');
        $(this).find('._3oi9').addClass('_23jt');
		c = $(this).attr('data-id');
		ct.removeAttr('style');		
		publisher_color(c);
    });
	
	$('body').on('keyup','.publisher-box .postText', function(){  
		var self = $(this);
		var text = $(this).val();
		var count_text = parseInt(text.length);
		if(count_text >= colorbox_limit){
			$('.js_colorbox').parent().parent().hide();
			$('.js_colorbox').removeClass('active');
			$('.publisher_colorbox').hide();
			ic.val('');
			ct.removeAttr('style').removeClass('color_ph').css({'height' : '75px'});
			$('.publisher_colorbox ._3oi9').removeClass('_23jt');
			$('.publisher_colorbox ._3oi9:first').addClass('_23jt');
		} else {
			$('.js_colorbox').parent().parent().show();
			if(c != 0){				
				$('.js_colorbox').addClass('active');
				$('.publisher_colorbox').show();
				ic.val(c); 
				$('.publisher_colorbox ._3oi9').removeClass('_23jt');
				$('.publisher_colorbox .btn[data-id="'+c+'"] ._3oi9').addClass('_23jt');
				publisher_color(c);
			}
		}
	});

});