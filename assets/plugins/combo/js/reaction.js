$(document).click(function(e){
	if($(e.target).closest(".story-react-container").length == 0){
		$('.emotionreaction').html('');
	}
});   


function ReactionsHtmL(post_id, result){
	var rHtml = '';
	$.each(result.result, function(i,val){
		rHtml += '<span data-title="'+val.text+'" class="btn" onclick="set_reaction($(this), \''+val.key+'\', '+post_id+');"><img src="'+val.src+'" alt="'+val.text+'" data-title="'+val.text+'"></span>';
	});
	return rHtml;
}


$('body').on('mouseover','.story-react-container',function(){ 
	if($(this).find('.emotionreaction').html() == ''){ 
		var post_id = $(this).closest('.post').attr('data-post-id');
		var reac_html = ReactionsHtmL(post_id, reactions_array);
		$(this).find('.emotionreaction').html('<div class="story-react-wrapper" style="position: absolute;">'+reac_html+'</div>');
	}
});	


/* click icon show progress */
function progressIconLoader(e) {
    e.each(function() {
        return progress_icon_elem = $(this).find("i.progress-icon"), default_icon = progress_icon_elem.attr("data-icon"), hide_back = !1, 1 == progress_icon_elem.hasClass("hide") && (hide_back = !0), 1 == $(this).find("i.icon-spinner").length ? (progress_icon_elem.removeClass("icon-spinner").removeClass("icon-spin").addClass("icon-" + default_icon), 1 == hide_back && progress_icon_elem.hide()) : progress_icon_elem.removeClass("icon-" + default_icon).addClass("icon-spinner icon-spin").show(), !0
    })
}

/* new image */
function progressImageLoader(e) {
    e.each(function() {
        return elm = $(this), "none" == elm.css("display")?(elm.next("i.progress-icon").remove(),elm.show()):(elm.hide(),elm.after('<i class="fa fa-spinner fa-spin progress-icon"></i>'))
    })
}

// Like story
function set_reaction(self, reaction, post_id){   
    story_container = $(".post[data-post-id="+post_id+"]");
    like_button = story_container.find('.story-like-btn');
    like_activity_button = story_container.find('.story-like-activity');
    progressIconLoader(like_button);
    progressImageLoader(like_button.find('img.progress-img'));
	
    $.get( site_ajax,{'f':'combo', 's':'like', 'reaction': reaction, 'post_id': post_id, 'only':'combo' },function(data){
            if(data.status == 200){   
			     if(data.liked == true){
				    /*show likes count*/
                    like_button.after(data.button_html).remove();
                    like_activity_button.html(data.activity_html);
                    story_container.find('.js_post-likes').show();				
				} else {
				    /*hidden likes count*/
                    like_button.after(data.button_html).remove();
                    like_activity_button.html(data.activity_html);
					if(parseInt(story_container.find('#likes').text()) == 0) {
                       story_container.find('.js_post-likes').hide();
                    }	
                }
            }
	});

}

//coment photo theater
$('body').on('click', '.comment-image a', function(event){ event.preventDefault(); ;
	var img = $(this).attr('href');
	$('body').append('<div class="lightbox-container"><div class="lightbox-backgrond" onclick="Wo_CloseLightbox();"></div><div class="lightbox-content"><div class="story-img"><img src="'+img+'" alt="media"></div></div></div>');
});