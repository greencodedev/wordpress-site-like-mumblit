/* @author Pp Galvan - LdrMx */
//ACTIVE ADS SCROOLL

/*function fixed_home(){
	if($('.pagelet_ego_ad.ego_in_home').length == 0){
		setTimeout('fixed_home()', 10000);
		return false;
	} else {
		var RH = $('.pagelet_ego_ad.ego_in_home').height();
		var offset = $('.pagelet_ego_ad.ego_in_home').offset();
		var RT = (offset.top)-(50);
		
		//if(ad_rigth_scroll == 1){}	  
		$(window).scroll(function(){  
			if ($(window).scrollTop() >  RT && $(window).width() > 768){ 
				var RW = $('.pagelet_ego_ad.ego_in_home').width();
				$('.ego_in_home #rightColumn').addClass('fixed_elem').css({'top':48, 'width':RW});
			} else { 
				$('.ego_in_home #rightColumn').removeClass('fixed_elem').removeAttr("style");
			}		
		}); 		
	}
} 
*/	
function fixed_profile(){	
	if($('.pagelet_ego_ad.ego_in_profile').length == 0){
		setTimeout('fixed_profile()', 10000);
		return false;
	}else{		 
		var RH = $('.pagelet_ego_ad.ego_in_profile').height();
		var offset = $('.pagelet_ego_ad.ego_in_profile').offset();
		var RT = (offset.top)-(50);
		
		//if(ad_rigth_scroll == 1){}	  
			$(window).scroll(function(){  
				if ($(window).scrollTop() >  RT && $(window).width() > 768){ 
					var RW = $('.pagelet_ego_ad.ego_in_profile').width();
					$('.ego_in_profile#rightColumn').addClass('fixed_elem').css({'top':48, 'width':RW});
				} else { 
					$('.ego_in_profile#rightColumn').removeClass('fixed_elem').removeAttr("style");
				}		
			}); 		
	}
}


/*$(function(){
	fixed_home();
	fixed_profile();
});
*/

/* return rand number of an array*/
function rand(min, max) {
	var offset = min;
	var range = (max - min) + 1;
	var randomNumber = Math.floor( Math.random() * range) + offset;
	return randomNumber;
}


/*load ads in wall
if($('#posts').length!=0){
	if($('.post-container .post').length > 1){
		var checkboxValues = new Array();
		$('.post-container .post').each(function(i){ checkboxValues.push($(this).attr('data-post-id')); });
		//randomNumber = rand(0, checkboxValues.length - 1);
		load_ads_post(checkboxValues[1]);
	}
}	
*/  


/* save ads form */
function saveAds(){
	$('.ads-add-form').ajaxSubmit({ type:'post', url : site_ajax+ "?f=save", data:{'auto':'true'}, cache : false,
		success : function(result){
			$('#ads-id').val(result.ad_id);
		}
	});
}


/* url details */
$('body').on('submit', '#pre-website-ads-container form', function(e) { e.preventDefault();
	var form = $(this);
	var input = form.find('.input_get_website');
	var button = form.find('button');
	var error = form.find('.alert');
	var c = $('.website-ads-add-container');
	var content = input.val(); 
	var url = content.match(/(https?:\/\/|www\.)(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/);
	if(url &&  url.length > 0){
		error.fadeOut();
		input.attr('disabled', 'disabled');
		button.attr('disabled', 'disabled');
		$.ajax({ type:'post', url : site_ajax+"?f=ads_get_website", data:{'link':input.val(), 'only':'ads'},
			success : function(data){
				if(data){
					if(data.result == 1){
						c.html(data.template); saveAds();
					} 
				} else {
					input.removeAttr('disabled');
					button.removeAttr('disabled');	
				}
			}
		});
	} else { 
		error.slideDown(); input.removeAttr('disabled'); button.removeAttr('disabled');
	}
});


/*upload image a ads page and url*/
$('body').on('change', '#ads-image-input', function(e) { e.preventDefault();
	var form = $('.ads-add-form');
	var indicator = $('.ad-image-indicator')
	var button = $('.ads-image-container .btn-file');
	var error = $('#ads-image-error');
	var input = $('.ads-image-hidden-input');
	var ads_image = $('.ads-image');
	var t = $(this);
	var ad_id = $('#ads-id').val();
	indicator.show();
	button.attr('disabled', 'disabled');
	form.ajaxSubmit({ type :'post', url: site_ajax+"?f=upload_image", data:{'ad_id':ad_id}, cache : false,
		success : function(result) {
			if(result.result == 0){
				___modal(result);
			} else {					
				indicator.hide();
				t.val('');
				if (result == '') {
					error.slideDown();
					setTimeout(function(){ error.slideUp(); }, 5000);
				} else {
					indicator.hide();
					button.removeAttr('disabled');
					saveAds();
					input.val(result.image);
					ads_image.attr('src', result.image);
				}
			}// end call
		}
	});
});



/*get plans*/
$('body').on('change', '.ads-plan-type', function(e) { e.preventDefault();
	var l = $('.ads-plans');
	var v = $(this).val();
	l.attr('disabled', 'disabled');
	$.ajax({ url : site_ajax+"?f=load_plans", data : {'type': v}, cache : false, success : function(data) {
			l.html(data.html);
			l.removeAttr('disabled');
			if ($('.ads-add-form').length) { saveAds(); }
		}
	});
});


/*change locations all auto save*/
$('body').on('change', '.ad-all-location', function(e) { e.preventDefault();
	var v = $(this);
	var c = $('.each-ads-location')
	if (v.prop('checked')) {
		//alert('kola')
		c.find('input[type=checkbox]').each(function() {
			$(this).prop('checked', 'checked');
			$(this).prop('value', $(this).data('value'));
		});
	} else {
		v.removeAttr('checked');
		c.find('input[type=checkbox]').each(function() {
			$(this).removeAttr('checked').removeAttr('value');
		});
	}
	saveAds();
});

/*change locations for place auto save*/
$('body').on('change', '.each-ads-location input[type=checkbox]', function(e) { e.preventDefault();
	$('.ad-all-location').removeAttr('checked');
	if ($(this).prop('checked')) {
		$(this).prop('value', $(this).data('value'));
	} else {
		$(this).removeAttr('value');
	}
	saveAds();
});

/*button "save ad" click and return to list*/
$('body').on('submit', '.ads-add-form', function(e) { e.preventDefault();
	var form = $(this);
	form.ajaxSubmit({type:'post', url: site_ajax+"?f=save", cache : false, success : function(result) {
			if (result.result == 1) { window.location = 'index.php?link1=campaign'; } 
			else { 
			   _modal('#_modal-message', result);
			}
		}
	});
	return false;
});

/* auto save if change */
$('body').on('change', '.ads-add-form select[name="gender"]', function() { saveAds(); });
/* auto save if change */
$('body').on('change', '.ads-add-form select[name="plan_id"]', function() { saveAds(); });
/* auto save if change */
$('body').on('change', '.ads-add-form input[name="campaign_name"]', function() { saveAds(); });
/* auto save if change */
$('body').on('change', '.ads-add-form input[name="title"]', function() { saveAds(); });
/* auto save if change */
$('body').on('change', '.ads-add-form textarea[name="description"]', function() { saveAds(); });


$('body').on('keyup', '.ads-add-form input[name="title"]',function(){ 
	var title = $(this).val();
	var count_title = parseInt(title.length);
	var new_count_t = parseInt( 25 - count_title );
	$('.js_count_t').html(new_count_t);
	if(new_count_t < 0){ $('.error_t').addClass('_red_active'); } 
	else { $('.error_t').removeClass('_red_active'); }
});


$('body').on('keyup', '.ads-add-form textarea[name="description"]',function(){ 
	var desc = $(this).val();
	var count_desc = parseInt(desc.length);
	var new_count_d = parseInt( 99 - count_desc );
	$('.js_count_d').html(new_count_d);
	if(new_count_d < 0){ $('.error_d').addClass('_red_active'); } 
	else { $('.error_d').removeClass('_red_active'); }
});	


/*button "save and activate"*/
$('body').on('click', '.ad-save-and-activate', function(e) { e.preventDefault();
	var form = $('.ads-add-form');
	form.ajaxSubmit({ type : 'post', url : site_ajax+ "?f=save", cache : false,
		success : function(data) {
			if (data.result == 1) { window.location = 'index.php?link1=campaign_activate&ad_id='+data.ad_id; } 
			else { eval(data.callback); }
		}
	});
});


/*get page*/
$('body').on('click', '#ads-page-continue-button', function(e) { e.preventDefault();
	var form = $('#ad-page-pre-form');
	var select = form.find('select');
	select.attr('disabled', 'disabled');
	form.css('opacity', '0.5');
	var container = $('.page-ads-add-container');
	var pageId = select.val();	
	$.ajax({ type : 'post', url : site_ajax+ "?f=ads_get_page", data : {'page_id' : pageId}, cache : false,
		success : function(data) {
			if(data.callback){
				eval(data.callback);
			} else {
				container.html(data.template); saveAds();
			}
		}
	});
});


/*cancel ads*/
$('body').on('click', '.delete-ad-button', function(e) { e.preventDefault();
	var return_url = $(this).attr('href');
	var ad_id = $('#ads-id').val();
	var text = $(this).data('warning');
	if(confirm('Are you sure you want to delete this ads?')==false) return false; 
	if (ad_id == ''){ window.location = return_url; }
	$.ajax({ type : 'post', url : site_ajax+ "?f=delete", data: {'ad_id':ad_id}, cache : false,
		success : function(data) {
			window.location = return_url;
		}
	});
});


/*delete ads of list*/
$('body').on('click', '.delete-ads-from-list', function(e) { e.preventDefault();
	var self = $(this);																					
	var ad_id = self.attr('data-id');
	self.closest('tr').hide();
	$.ajax({type: 'post', url: site_ajax+ "?f=delete", data: {'ad_id':ad_id}, cache : false,
		success:function(result){ 
			if(result.result == '1'){
				self.closest('tr').remove();
			} else {
				self.closest('tr').show();
			}
		}
	});
});


$('body').on('click', '.ads-click-link', function() { 
	var ad_id = $(this).data('id');
	$.ajax({ url : site_ajax+ "?f=click", data: {'ad_id':ad_id} });
});


/*get preview ads*/
$('body').on('click', '.preview-ad', function(e){ e.preventDefault();
	$('.ads-add-form').ajaxSubmit({type:'post', url: site_ajax+"?f=save", data: {'auto':'true'}, cache : false,
			success : function(result) {
				$('#ads-id').val(result.ad_id);
				if (result.ad_id != 0) {
				   var url =  site_ajax+ "?f=ads_preview&ad_id="+result.ad_id;
				   __modal(url);
				} else { $('#_modal').modal('hide'); }
			}
	});
});


//close
function AdsCloseBox(self, ad_id){
	self.closest('.list-group').remove();
}


//delete
function AdsDeleteBox(self, ad_id){ if(confirm('Are you sure you want to delete this ads?')==false) return false; 
	self.closest('.list-group').remove();
	$.ajax({type: 'post', url: site_ajax+"?f=delete&ad_id="+ad_id, data: {}, cache:false, success:function(result){ if(result.result == 1){ } } });
}


//edit
function AdsEditBox(self, ad_id){
	window.location = 'index.php?link1=campaign_edit&ad_id='+ad_id;
}


/*btn post disable*/	
function AdsDisableBox(self, ad_id){ 
	if(confirm('Are you sure you want pause this ads?')==false) return false;
	var status = self.attr('data-value');
	$.ajax({type: 'post', url: site_ajax+"?f=ads_status&ad_id="+ad_id+"&status="+status, data: {}, 
		cache:false, success:function(result){
			if(result.result == 1){
				if(status == 0){ 
					self.attr({'data-value':'1'});
					self.find('.status_do').text('Enable');
				} else { 
					self.attr({'data-value':'0'});
					self.find('.status_do').text('Disable');
				}
			}
		}
	});
}


$(function(){
	load_ads_post(0);
}); 


function open_payment_box(type, id){
	__modal(site_ajax+"?f=ads_"+type+"&only=ads&ad_id="+id);
}