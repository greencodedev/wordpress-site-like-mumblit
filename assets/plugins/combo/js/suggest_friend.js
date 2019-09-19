/* HTML SUGGEST IN POSTS */
function HtmlWallFriends(friends){ var Html = "";
	$.each(friends, function( i, fof ) {
		Html += '<li><div class="sug-card">'+
		'<a href="'+site_url+'/'+fof.user_name+'"><img src="'+fof.user_picture+'" alt="'+fof.user_name+' Profile Picture" /></a> '+
		'<div class="sug-details">'+fof.full_name+'</div>'+
		'<div>'+fof.btn+'</div>'+
		'</div></li>';
	});	
	return Html;
}


/* load sugest list */	 
function load_friend_suggest(id){ 
	if($('.posts_load .post').length == 0){ 
		setTimeout('load_friend_suggest(0)', 10000);
		return false;
	}
	$.ajax({type: 'post', url: site_ajax+"/?f=autocomplete&s=new_users", data:{'only':'admin'},
		cache : false, success : function(result) {
			if(result.count != 0&& result.result == 1){	   
				HtmlWallF = HtmlWallFriends(result.fof);
				if(id == 0){ 
					$('.posts_load .post:eq(2)').closest('.post-container').before('<div class="list-group list-group -default list-group -sug-post"> <div class="list-group -heading"><strong>'+__['People you may know']+'</strong> <a class="l_m_f_s_1 '+pull_right+'" style="cursor:pointer;" onclick="load_more_friend_suggest($(this));"><i class="fa fa-repeat progress-icon" data-icon="repeat"></i></a> <div class="list-group -body" style="max-width: 542px; padding:0; overflow: scroll; overflow-y: hidden; position: relative;"><ul>'+HtmlWallF+'</ul><div></div>');
				} else { 
					$('#post-'+id).closest('.post-container').before('<div class="list-group list-group -default list-group -sug-post"> <div class="list-group -heading"><strong>'+__['People you may know']+'</strong> <a class="l_m_f_s_1 '+pull_right+'" style="cursor:pointer;" onclick="load_more_friend_suggest($(this));"><i class="fa fa-repeat progress-icon" data-icon="repeat"></i></a> <div class="list-group -body" style="max-width: 542px; padding:0; overflow: scroll; overflow-y: hidden; position: relative;"><ul>'+HtmlWallF+'</ul><div></div>');
				}
			}
		} 
	}); 
}


/* LOAD IN START OF WEBSITE */	
$(function(){ 
	load_friend_suggest(0);
});	


$('body').on('click','.l_m_f_s_1', function(e){ e.preventDefault(); });


/* LOAD MORE */
function load_more_friend_suggest(self){
	self.find('.progress-icon').removeClass('fa-repeat').addClass('fa-spinner fa-spin');
	$.ajax({type: 'post', url: site_ajax+"/?f=autocomplete&s=new_users", 
		cache : false, success : function(result) {
			if(result.count != 0&& result.result == 1){	   
				HtmlWallF = HtmlWallFriends(result.fof);
				self.parent().find('.list-group -body ul').html(HtmlWallF);
				self.find('.progress-icon').removeClass('fa-spinner fa-spin').addClass('fa-repeat');
			}
		} 
	}); 
}