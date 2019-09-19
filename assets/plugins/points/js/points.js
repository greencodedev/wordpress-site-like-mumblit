 /* Points system
  * @autor Pepe Galvan - LdrMx
  */

	function userpoints_onchange(elem) {
		if(elem.val() != 0){
			$('#div_sent_to_1').removeClass('x-hidden');
		} else {
			$('#div_sent_to_1').addClass('x-hidden');
			$('#div_sent_to_1 .js_users_suggest').val('');
			$('#div_sent_to_1 .user_id').val('');
		}
	}
	
	function from_user_id_suggest_onChanged(value) {
		if(value == 0) {
			$("#user_id_suggest_other").removeClass('x-hidden');
		}
		else {
			$("#user_id_suggest_other").addClass('x-hidden');
			$('#user_id_suggest_other .js_users_suggest').val('');
			$('#user_id_suggest_other .user_id').val('');
		}
	}	
	
	$('body').on('click', '.offer_delete', function(e) { e.preventDefault(); var self =  $(this); var item_id = self.attr('data-id');
	if(confirm('Are you sure you want to delete this offer?')==false) return false; 															  
		 $.ajax({type: 'post', url: site_ajax+"/plugin_requests.php", data:{'f':'admin_points', 'task':'offer_delete', 'item_id':item_id}, cache : false, 
			success : function(result) { 
			if(result.result == 1){ self.closest('tr').remove(); }
			$('#modal').modal('hide'); 																												   }
		});		 
	});
	
	$('body').on('click', '.shop_delete', function(e) { e.preventDefault(); var self =  $(this); var item_id = self.attr('data-id');
	if(confirm('Are you sure you want to delete this item in shop?')==false) return false; 															  
		 $.ajax({type: 'post', url: site_ajax+"/plugin_requests.php", data:{'f':'admin_points', 'task':'shop_delete', 'item_id':item_id}, cache : false, 
			success : function(result) { 
			if(result.result == 1){ self.closest('tr').remove(); }
			$('#modal').modal('hide'); 																												   }
		});		 
	});

	$('body').on('click', '.shop_delete_user', function(e) { e.preventDefault(); var self =  $(this); var item_id = self.attr('data-id');
	if(confirm('Are you sure you want to delete this item purchased?')==false) return false; 																	  
			 $.ajax({type: 'post', url: site_ajax+"/plugin_requests.php", data:{'f':'admin_points', 'task':'shop_delete_user', 'item_id':item_id}, cache : false, 
				success : function(result) { 
				if(result.result == 1){ self.closest('tr').remove(); }
				$('#modal').modal('hide'); 																												   }
			});		 
	});

	function addPointsRow() {
		var el = document.getElementById("pointsrow").cloneNode(true);
		el.id = '';
		var moreRow = document.getElementById("addmorerow");
		moreRow.parentNode.insertBefore(el, moreRow);
	}
	
	//invite form
	$('body').on('submit', '.js_ajax_form_invite', function(e) {
		e.preventDefault();
		var _this = $(this);
		var url = _this.attr('data-url');
		var submit = _this.find('input[type="submit"]');
		var error = _this.find('.alert.alert-danger');
		var success = _this.find('.alert.alert-success');
		/* show any collapsed section if any */
		if(_this.find('.js_hidden-section').length > 0 && !_this.find('.js_hidden-section').is(':visible')) {
			_this.find('.js_hidden-section').slideDown();
			return false;
		}
		/* show loading */
		submit.data('text', submit.val());
		submit.prop('disabled', true);
		submit.val('Loading');
		/* get ajax response */
		$.post(site_ajax+url, $(this).serialize(), function(response) {
			/* hide loading */
			submit.prop('disabled', false);
			submit.val(submit.data('text'));
			/* handle response */
			if(response.error) {
				if(success.is(":visible")) success.hide(); // hide previous alert
				error.html(response.message).slideDown();
			} else if(response.success) {
				if(error.is(":visible")) error.hide(); // hide previous alert
				success.html(response.message).slideDown();
				$('.total_invites').text(response.total_invites);
			} else {
				eval(response.callback);
			}
		}, "json")
		.fail(function() {
			/* hide loading */
			submit.prop('disabled', false);
			submit.val(submit.data('text'));
			/* handle error */
			if(success.is(":visible")) success.hide(); // hide previous alert
			error.html('There is something that went wrong!').slideDown();
		});
	});

	$('body').on('click','.invite_delete', function(event){ event.preventDefault(); 
		if(confirm('Are you sure you want to delete this invitation?')==false) return false; 
				var self = $(this);
		var _this = self.closest('.table-responsive');
		var submit = self;
		var error = _this.find('.alert.alert-danger');
		var success = _this.find('.alert.alert-success');
		/* show loading */
		submit.data('text', submit.html());
		submit.prop('disabled', true);
		submit.html('Loading');	
		
		var tab = $(this).closest('tr');	
		var invite_id = tab.attr('data-id');
		tab.hide();
		$.ajax({ type : 'post', url : site_ajax+ "?f=points&s=invite", data: {'task':'delete_invite', 'invite_id':invite_id}, cache : false,
				success : function(response) { 
					/* hide loading */
					submit.prop('disabled', false);
					submit.html(submit.data('text'));
					if(response.error) {
						if(success.is(":visible")) success.hide(); // hide previous alert
							error.html(response.message).slideDown();
					} else if(response.success) {
						if(error.is(":visible")) error.hide(); // hide previous alert
						success.html(response.message).slideDown();
						$('.total_invites').text(response.total_invites);
						tab.remove();
					} else {
						eval(response.callback);
					}
					//window.location = return_url;
				}
	    });
	});
	$('body').on('click','.re_send_invite', function(event){ event.preventDefault(); 
		var self = $(this);
		var _this = self.closest('.table-responsive');
		var submit = self;
		var error = _this.find('.alert.alert-danger');
		var success = _this.find('.alert.alert-success');
		/* show loading */
		submit.data('text', submit.html());
		submit.prop('disabled', true);
		submit.html('Loading');	
		
		var tab = $(this).closest('tr');	
		var invite_id = tab.attr('data-id');
		$.ajax({ type : 'post', url : site_ajax+ "?f=points&s=invite", data: {'task':'re_send_invite', 'invite_id':invite_id}, cache : false,
				success : function(response) { 
					/* hide loading */
					submit.prop('disabled', false);
					submit.html(submit.data('text'));
					if(response.error) {
						if(success.is(":visible")) success.hide(); // hide previous alert
							error.html(response.message).slideDown();
					} else if(response.success) {
						if(error.is(":visible")) error.hide(); // hide previous alert
						success.html(response.message).slideDown();
					} else {
						eval(response.callback);
					}
				}
	    });																																																				
	});
	
	//show tab
	$('body').on('click','.invite_options li a', function(event){ event.preventDefault(); 
		var id = $(this).attr('data-id');		
		$('.invite_options li').removeClass('active');
		$(this).closest('li').addClass('active'); 		
		$('.invite_content').addClass('x-hidden');
		$('#invite-'+id).removeClass('x-hidden');																																																						
	});

	//admin form
	$('body').on('click', '.point_send', function(event){ event.preventDefault();
		var self = $(this);												  
		var _this = $(this).closest('.point_send_form');
        var get_url = _this.attr('data-url');
        var submit = _this.find('button[type="submit"]');
        var error = _this.find('.alert.alert-danger');
        var success = _this.find('.alert.alert-success');
        /* show any collapsed section if any */
        if(_this.find('.js_hidden-section').length > 0 && !_this.find('.js_hidden-section').is(':visible')) { 
		    _this.find('.js_hidden-section').slideDown();
            return false;
        }	
		$('.point_send_form').ajaxForm({
			url: site_ajax+'/'+get_url, beforeSend: function() {
				/* show loading */
				self.data('text', self.html());
				self.prop('disabled', true);
				self.html('Loading');	
			}, success: function(response) {																						
				if (response.status == 200) { window.location.href = response.href; } else { eval(response.callback); } 
				self.prop('disabled', false);
				self.html(self.data('text'));
			}
        });		
	});