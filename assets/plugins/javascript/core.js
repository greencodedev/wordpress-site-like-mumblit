/* @author Pp Galvan - LdrMx */
// run autocomplete
/* focus the input */
$('body').on('click', '.js_autocomplete', function() {
	var input = $(this).find('input').focus();
});


/* show and get the results if any */
$('body').on('keyup', '.js_autocomplete input', function() {
	var _this = $(this);
	var query = _this.val();
	var parent = _this.parents('.js_autocomplete');
	/* change the width of typehead input */
	prev_length = _this.data('length') || 0;
	new_length = query.length;
	if(new_length > prev_length && _this.width() < 250) {
		_this.width(_this.width()+6);
	} else if(new_length < prev_length) {
		_this.width(_this.width()-6);
	}
	_this.data('length', query.length);
	/* check maximum number of tags */
	if(parent.find('ul.tags li').length > 9) {
		return;
	}
	/* check the query string */
	if(query != '') {
		/* check if results dropdown-menu not exist */
		if(_this.next('.dropdown-menu').length == 0) {
			/* construct a new one */
			var offset = _this.offset();
			var posX = offset.left - $(window).scrollLeft();
			if($(window).width() - posX < 180) {
				_this.after('<div class="dropdown-menu auto-complete tl"></div>');
			} else {
				_this.after('<div class="dropdown-menu auto-complete"></div>');
			}
		}
		/* get skipped ids */
		var skipped_ids = [];
		$.each(parent.find('ul.tags li'), function(i,tag) {
			skipped_ids.push($(tag).attr('data-uid'));
		});
		$.post(site_ajax+"/?f=autocomplete&s=users&type=tag", {'query': query, 'skipped_ids': JSON.stringify(skipped_ids)}, function(response) {
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
$('body').on('click focus', '.js_autocomplete input', function() {
	/* check maximum number of tags */
	if($(this).parents('.js_autocomplete').find('ul.tags li').length > 9) {
		return;
	}
	/* only show again if the input & dropdown-menu are not empty */
	if($(this).val() != '' && $(this).next('.dropdown-menu').find('li').length > 0) {
		$(this).next('.dropdown-menu').show();
	}
});


/* hide the results dropdown-menu when clicked outside the input */
$('body').on('click', function(e) {
	if(!$(e.target).is(".js_autocomplete")) {
		$('.js_autocomplete .dropdown-menu').hide();
	}
});


/* add a tag */
$('body').on('click', '.js_tag_add', function(e) {
	var self = $(this);
	var user_id = self.attr('data-uid');
	var name = self.attr('data-username');
	var parent = self.parents('.js_autocomplete');
	var tag = '<li data-uid="'+user_id+'">'+name+'<input type="hidden" name="tag_friends[]" value="'+user_id+'"><button type="button" class="close js_tag_remove" title="Remove"><span>&times;</span></button></li>'
	parent.find('.tags').append(tag);
	parent.find('.who_are_you_with').val('').focus();
});


/* remove a tag */
$('body').on('click', '.js_tag_remove', function() {
	var tag = $(this).parents('li');
	var parent = $(this).parents('.js_autocomplete');
	tag.remove();
	return false;
});


// run user suggest
$('body').on('keyup', '.js_users_suggest', function() {
	var _this = $(this);
	var raw_query = _this.val();
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
		$.post( site_ajax+"/?f=autocomplete&s=users&type=suggest", {'query': query}, function(response) {
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
$('body').on('click focus', '.js_users_suggest', function() {
	var query = $(this).val();
	if(query !== null && query.length > 0) {
		$(this).next('.dropdown-menu').show();
	}
});


/* hide the results dropdown-menu when clicked outside the input */
$('body').on('click', function(e) {
	if(!$(e.target).is(".js_users_suggest")) {
		$('.js_users_suggest').next('.dropdown-menu').hide();
	}
});


/* add an user suggest */
$('body').on('click', '.js_suggest_add', function() {
	var self = $(this).closest('.col-sm-9');											  
	var input1 = self.find('.js_users_suggest');
	var input2 = self.find('.user_id');
	var username = $(this).attr('data-username');
	var userid = $(this).attr('data-uid');
	input2.val(userid);
	input1.val(username).focus();		
});


// delete post
function Delete_Post(post_id) {
	var delete_box = $('#post-' + post_id).find('#delete-post');
	var delete_button = delete_box.find('#delete-all-post');
	$('#post-' + post_id).find('#delete-post .ball-pulse').fadeIn(100);
	$.get(site_ajax, {
	'f': 'post_delete',
	'post_id': post_id,
	'only':'admin'
	}, function (data) {
	if(data.status == 200) {
	  delete_box.modal('hide');
	  setTimeout(function () {
		$('#post-' + post_id).slideUp(200, function () {
		  $(this).remove();
		});
		$('.post_type_share[data-share-id="'+post_id+'"]').closest('.post').parent().slideUp(200, function () {
		  $(this).remove();
		});		
	  }, 300);
	}
	$('#post-' + post_id).find('#delete-post .ball-pulse').fadeOut(100);
	});
}


//plublisher new
$('body').on('click','.js_publisher_tab[data-tab="post"]', 
	 function(e) { e.preventDefault(); 
		 $('.js_publisher-question-remover').click();
		 $('#album-form').hide(); 
		 $('#album-form input').val('');
		 $('.js_publisher_plugin_tab').removeClass('active');
	}
);


$('body').on('click','.btn_classified, .btn_poll', 
	 function() { 
		 $('.js_publisher-question-remover').click();						   	 							  
		 $('.js_publisher_plugin_tab').removeClass('active');
		 $('.js_publisher_tab').removeClass('active');
		 $('.js_publisher_tab[data-tab="post"]').addClass('active');
		 $('#album-form').hide(); 
		 $('#album-form input').val('');							 
	}
);


$('body').on('click','.js_publisher_tab[data-tab="album"]', 
	 function(e) { e.preventDefault(); 
		 $('.js_publisher-question-remover').click();
		 $('#poll-form').hide(); 
		 $('#poll-form input').val('');
	}
);


/*PUBLISHER SHADOW ('.publisher-box .postText').focus(function(){
$(this).closest('.publisher-box').css({'position':'relative', 'z-index':'1049'});
if($('.modal_publisher').length == 0){
	$('body').append('<div class="modal-backdrop fade in modal_publisher"></div>');
} else {
	$('.modal_publisher').remove();	
	$('body').append('<div class="modal-backdrop fade in modal_publisher"></div>');
}
});


$('body').on('click','.modal_publisher', function(e) { e.preventDefault(); 
$('.publisher-box').removeAttr('style');
$('.modal_publisher').remove();
});*/


$('body').on('click','.modal-backdrop', 
  function(e) { e.preventDefault(); 
   if($('.modal[data-id="modal_leader"]').length == 0){
		$('.modal-backdrop').remove();
		$('body').removeClass('modal-open');										
   }
})


$('body').on('click','.modal[data-id="modal_leader"] .close_modal', 
  function(e) { e.preventDefault(); 
	$('.modal-backdrop').remove();
	$('.modal[data-id="modal_leader"]').remove();
	$('body').removeClass('modal-open');
});


//popup
function ___modal(result){ 
	var result_btn = '';
	if(result.btn != ''){
		var result_btn = '<div class="modal-footer">'+result.btn+'</div>';
	}
	var html = '<div class="modal-header">'+
	'<div class="pull-right flip"><button type="button" class="close close_modal" onclick="Wo_CloseModels();"><span>&times;</span></button></div>'+
	'<h4 class="modal-title">'+result.title+'</h4></div>'+
	'<div class="modal-body">'+result.content+'</div>'+result_btn;
	return html; 
}

// modal 
function __modal(href) {
	/* check if the modal not visible, show it */
	if(!$('#modal').is(":visible")){
		$('#modal').modal('show'); $('#modal_leader').remove();
		$('body').append('<div class="modal fade in" data-id="modal_leader" id="modal_leader" role="dialog" aria-hidden="false" style="display: block;"><div class="modal-dialog"><div class="modal-content"> <div class="modal-body"><div class="loader pt10 pb10"></div></div> </div></div></div>');
		$('#modal_leader').modal({show:true});
		if(href){
			$.ajax({type: 'post', url: href, data: {}, cache : false, success:function(result){		 
				if(!result){ 
					var modal_html = '<div class="modal-body pt10 pb10">Error try more late</div>';
				} else {
					 var modal_html = ___modal(result);
				}
				$('#modal_leader .modal-content').html(modal_html);
				$('#modal_leader').modal({show:true});	 
			}});
		}
	}
}


function _modal(type, result) {
	/* check if the modal not visible, show it */
	if(!$('#modal').is(":visible")){ 
		$('#modal').modal('show');
		$('#modal_leader').remove();
		$('body').append('<div class="modal fade in" id="modal_leader" role="dialog" aria-hidden="false" style="display: block;"><div class="modal-dialog"><div class="modal-content"> <div class="modal-header">  <div class="pull-right flip"><button type="button" class="close close_modal" onclick="Wo_CloseModels();"><span>&times;</span></button></div>  <h4 class="modal-title"></h4></div> <div class="modal-body"><div class="loader pt10 pb10"></div></div> </div></div></div>');
		$('#modal_leader').modal({show:true});		
		if(!result){
			$('#modal_leader').remove(); 
		} else {
			$('#modal_leader .modal-title').html(result.title);
			$('#modal_leader .modal-body').html(result.message);
			$('#modal_leader').modal({show:true});	 
		}	
	}
}


/*modal*/
$('body').on('click', '[data-toggle="modal"]', function(e){
	e.preventDefault();
	var url = $(this).attr('data-url');
	if(url){
		__modal(url);
	}
});



$(function() {	
	// run DataTable
	$('.js_dataTable').DataTable({
		"aoColumnDefs": [ {'bSortable': false, 'aTargets': [ -1 ]} ]
	})
});

$('body').on('click', '.js_btn', function(e){ e.preventDefault;
	var self = $(this);												  
	var _this = $(this).closest('.js_form');
	var get_url = _this.attr('data-url');
	var submit = _this.find('button[type="submit"]');
	var error = _this.find('.alert.alert-danger');
	var success = _this.find('.alert.alert-success');
	/* show any collapsed section if any */
	if(_this.find('.js_hidden-section').length > 0 && !_this.find('.js_hidden-section').is(':visible')) { _this.find('.js_hidden-section').slideDown(); return false; }		
	
	$('.js_form').ajaxForm({url: site_ajax+get_url, beforeSend: function() {
	/* show loading */
	self.data('text', self.html());
	self.prop('disabled', true);
	self.html('Loading');	
   }, success: function(response) {																						
   // if (response.status == 200) { window.location.href = response.href; } else { eval(response.callback); } 
		if(response.error) {
			if(success.is(":visible")) success.hide(); // hide previous alert
			error.html(response.message).slideDown();
		} else if(response.success) {
			if(error.is(":visible")) error.hide(); // hide previous alert
			success.html(response.message).slideDown();
		} else {
			eval(response.callback);
		}			
	self.prop('disabled', false);
	self.html(self.data('text'));
	}
 });
});		

// run ajax-forms
$('body').on('submit', '.js_ajax_forms', function(e) {
	e.preventDefault();
	var _this = $(this);
	var url = _this.attr('data-url');
	var submit = _this.find('button[type="submit"]');
	var error = _this.find('.alert.alert-danger');
	var success = _this.find('.alert.alert-success');
	/* show loading */
	submit.data('text', submit.html());
	submit.prop('disabled', true);
	submit.html('Loading');
	/* get ajax response */
	$.post(site_ajax+url, $(this).serialize(), function(response) {
		/* hide loading */
		submit.prop('disabled', false);
		submit.html(submit.data('text'));
		/* handle response */
		if(response.error) {
			if(success.is(":visible")) success.addClass('x-hidden').hide(); // hide previous alert
			error.html(response.message).removeClass('x-hidden').slideDown();
		} else if(response.success) {
			if(error.is(":visible")) error.addClass('x-hidden').hide(); // hide previous alert
			success.html(response.message).removeClass('x-hidden').slideDown();
		} else {
			eval(response.callback);
		}
	}, "json")
	.fail(function() {
		/* hide loading */
		submit.prop('disabled', false);
		submit.html(submit.data('text'));
		/* handle error */
		if(success.is(":visible")) success.addClass('x-hidden').hide(); // hide previous alert
		error.html('There is some thing went worng!').removeClass('x-hidden').slideDown();
	});
});

 // run admin deleter
$('body').on('click', '.js_admin-deleter', function () { if(confirm('Are you sure you want to delete this?')==false) return false;
	var handle = $(this).attr('data-handle');
	var id = $(this).attr('data-id');
	var node = $(this).attr('data-node');
		$.post(site_ajax+"?f=delete_all", {'handle': handle, 'id': id, 'node': node}, function(response) {
			/* check the response */
			if(response.callback) {
				eval(response.callback);
			} else {
				window.location.reload();
			}
		}, 'json')
		.fail(function() {
			__modal();
			$('#modal_leader .modal-content').html('<div class="mb20 mt20 ml10"><h4>There is some thing went worng!<h4></div>');
		});
});