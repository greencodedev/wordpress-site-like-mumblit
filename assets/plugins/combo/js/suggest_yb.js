/*tag*/
 $(function(){

	// run publisher
    /* toggle publisher_yb */
    $('body').on('click', '.js_yb', function() {
        $(this).toggleClass('active');
        $('.publisher_yb').slideToggle('fast');
        $('.publisher_yb').find('input').focus();
    });
	
	/* toggle activated publisher-meta */
    $('body').on('keyup', '.publisher_yb input', function() {
        if($(this).val() == '') {
            $('.js_yb').removeClass('activated');
        } else {
            $('.js_yb').addClass('activated');
        }
    });
	
});
 
 

// run yb autocomplete
    /* focus the input */
    $('body').on('click', '.js_yb_autocomplete', function() {
        var input = $(this).find('input').focus();
    });
    
	function FeelSuggestMedia(id,title,desc,src,url){
		html = '<li><div class="data-container clickable small js_yb_li" data-uid="'+url+'" data-username="'+title+'"><img class="data-avatar" src="'+src+'" alt=""><div class="data-content"><div><strong>'+title+'</strong></div><div>'+desc+'</div></div></div></li>';
		return html;
	}
	
	/* show and get the results if any */
    $('body').on('keyup', '.js_yb_autocomplete input', function() {
        var _this = $(this);
        var query = _this.val();
        var parent = _this.parents('.js_yb_autocomplete');
        /* change the width of typehead input */
        prev_length = _this.data('length') || 0;
        new_length = query.length;
        if(new_length > prev_length && _this.width() < 250) {
            _this.width(_this.width()+6);
        } else if(new_length < prev_length) {
            _this.width(_this.width()-6);
        }
        _this.data('length', query.length);

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
            
			var query = encodeURIComponent(query);
			//youtube		   
			var playListURL = 'https://www.googleapis.com/youtube/v3/search?part=snippet&key=AIzaSyC-SUAxUo65Q2iY7erkT96tfqvPwE5ay0M&maxResults=5&q='+query;
			var videoURL = 'www.youtube.com/watch?v=';
			
			$.getJSON(playListURL, function(data){
				var SearchHtml = '';
				$.each(data.items, function(i, videoy) {
				   var VideoID = videoy.id.videoId;							
				   var VideoTitle = videoy.snippet.title;
				   var VideoURL = videoURL+videoy.id.videoId;
				   var VideoDesc = videoy.snippet.description;
				   var VideoThumb = 'https://img.youtube.com/vi/'+videoy.id.videoId+'/default.jpg';
					   SearchHtml += FeelSuggestMedia(VideoID,VideoTitle,VideoDesc,VideoThumb,VideoURL);
				});
				_this.next('.dropdown-menu').html(SearchHtml).fadeIn(); 
			});
			
        } else {
            /* check if results dropdown-menu already exist */
            if(_this.next('.dropdown-menu').length > 0) {
                _this.next('.dropdown-menu').hide();
            }
        }
    });
    
	
    
	/* hide the results dropdown-menu when clicked outside the input */
    $('body').on('click', function(e) {
        if(!$(e.target).is(".js_yb_autocomplete")) {
            $('.js_yb_autocomplete .dropdown-menu').hide();
        }
    });
	
	
    /* add a tag */
    $('body').on('click', '.js_yb_li', function(e) {
		var url = $(this).attr('data-uid');
		$('.js_yb').removeClass('active');
        $('.publisher_yb').css({'display':'none'});
        $('.publisher_yb').find('input').val('');
		var text_original = $('.publisher-box .postText').val();
		$('.publisher-box .postText').val(text_original+' https://'+url).keyup();
		var text_new = $('.publisher-box .postText').val();
    });