/* @author LdrMx */
// run user suggest
$('body').on('keyup', '.share_option_name', function() {
	var _this = $(this);
	var raw_query = _this.val();
	var s = _this.closest('.share_option').find('.share_option_type').val();
	if(raw_query !== null && raw_query.length > 0) {
		var query = raw_query;
		/* check if results dropdown-menu already exist */
		if(_this.next('.dropdown-menu').length == 0) {
			/* construct a new one */
			var offset = _this.offset();
			var posX = offset.left - $(window).scrollLeft();
			if($(window).width() - posX < 180) {
				_this.after('<div class="dropdown-menu auto-complete tl"><div class="loader loader_small ptb10"></div></div>');
			} else {
				_this.after('<div class="dropdown-menu auto-complete"><div class="loader loader_small ptb10"></div></div>');
			}
		}
		$.post( site_ajax, {'f':'autocomplete','s': s, 'query': query, 'only':'admin'}, function(response) {
			if(response.callback) {
				eval(response.callback);
			} else if(response.suggest) {
				_this.next('.dropdown-menu').show().html(response.suggest);
			}
		}, 'json');
	} else {
		/* check if results dropdown-menu already exist */
		if(_this.next('.dropdown-menu').length > 0) {
			_this.next('.dropdown-menu').hide();
		}
	}
});

/* show previous results dropdown-menu when the input is clicked */
$('body').on('click focus', '.share_option_name', function() {
	var query = $(this).val();
	if(query !== null && query.length > 0) {
		$(this).next('.dropdown-menu').show();
	}
});

/* hide the results dropdown-menu when clicked outside the input */
$('body').on('click', function(e) {
	if(!$(e.target).is(".share_option_name")) {
		$('.share_option_name').next('.dropdown-menu').hide();
	}
});

/* add an user suggest */
$('body').on('click', '.js_share_add', function() {
	var self = $(this).closest('.share_option');											  
	var input1 = self.find('.share_option_name');
	var input2 = self.find('.share_option_id');
	var username = $(this).attr('data-username');
	var userid = $(this).attr('data-uid');
	input2.val(userid);
	input1.val(username).focus();		
});

// run bootstrap btn-group
$('body').on('click', '.btn-group a', function (e) {
	e.preventDefault();
	var parent = $(this).parents('.btn-group');
	/* change the value */
	parent.find('input[type="hidden"]').val($(this).attr('data-value'));
	/* copy text to btn-group-text */
	parent.find('.btn-group-text').text($(this).text());
	/* copy icon to btn-group-icon */
	parent.find('.btn-group-icon').attr("class", $(this).find('i.fa').attr("class")).addClass('btn-group-icon');
	/* copy title to tooltip */
	parent.attr('data-original-title', $(this).attr('data-title'));
	parent.attr('data-value', $(this).attr('data-value'));
	if($(this).attr('data-id') != ''){ 
	   $(this).closest('.btn-group').parent().find('.share_option').addClass('x-hidden');
	} else { 
	   $(this).closest('.btn-group').parent().find('.share_option').removeClass('x-hidden');
	}
	$(this).closest('.btn-group').parent().find('.share_option_type').val($(this).attr('data-value'));
	$(this).closest('.btn-group').parent().find('.share_option_id').val($(this).attr('data-id'));
	$(this).closest('.btn-group').parent().find('.share_option_name').val('').attr({'placeholder': $(this).attr('data-placeholder')});
	parent.tooltip();
});

	//share
	function ShareIt(self,post_id){
		__modal(site_ajax+"?f=shareit&only=share&post_id="+post_id)
	}	
	
	/* share post */
	$('body').on('click', '.post_share', function(event) {
		event.preventDefault();
		var loading  = $('.share_publisher_box #formLoading');
		var button = $(this);
		// prepare request data
		loading.show();
		button.attr("disabled", 'disabled');
		$(".share_publisher_box").ajaxForm({ success:  processRegister }).submit(); 
		event.stopPropagation();
	});	
	
	function processRegister(data) {
		var loading  = $('.share_publisher_box #formLoading');
		var button = $('.post_share'); 
		//alert(data.result);
				if(data.result == 1) {
					loading.hide();
					button.removeAttr("disabled");
					
					var who_shares = $(".post[data-post-id="+data.post_id+"]").find('#who_shares');
					var count_shares = parseInt(who_shares.text())+1;
					who_shares.text(count_shares);
					//$('#posts, #posts_hashtag').prepend(data.html);
					Wo_CloseModels();
				} else {
					$('#modal_leader .modal-content').html('<div class="mb20 mt20 ml10"><h4>Error no can share this post. try more late!!!<h4></div>');
				}
	}


	/*mention*/
	function active_mention(self) { 
		self.triggeredAutocomplete({ hidden: '#hidden_inputbox', source: site_ajax + "?f=mention&only=admin", trigger: "@" });
	}
	
	
	function ShareNow(self, id){
		if(!id){ return false; }
		self.closest('.post').find('.extra_notify').html('<div class="box_share_notify"><i class="fa fa-check"></i> Was shared in you profile.</div>');
		$.ajax({ type: 'post', url: site_ajax, data: {'f':'share_new_post', 'post_id':id, 'only':'share' }, cache : false,
			success: function(result) { 
				 if(result.result == 1){ 
					 //self.closest('.post').find('.extra_notify').html('<div class="box_share_notify"><i class="fa fa-check"></i> Was shared in you profile.</div>');
				 } else { 
					 self.closest('.post').find('.extra_notify').html('<div class="box_share_notify"><h4>Error no can share this post. try more late!!!<h4></div>');
				 }
				 setTimeout('clear_extra_notify('+id+');',5000);
			 }
		});
	}
	
	
	function clear_extra_notify(id){
		$('#post-'+id).find('.extra_notify').html('');
	}