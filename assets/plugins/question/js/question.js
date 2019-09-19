/* @author Pp Galvan - LdrMx */
     
/* publisher tabs */
$('body').on('click', '.js_publisher_plugin_tab[data-tab="question"]', function() {
	var publisher = $('.publisher');
	var tab = $(this).attr('data-tab');      
	if(tab == 'question'){
		create_wall_question();
	} else {
		/*tab version plugin*/
		$('.publisher-meta[data-meta="'+tab+'"]').show();
	}
	/* remove active class from all tabs */
	$('.js_publisher_tab').removeClass('active');
	/* hide & remove album meta */
	$('#album-form').hide(); 
	$('#album-form input').val('');	
	$('#poll-form').hide(); 
	$('#poll-form input').val('');
	$(".postText").css('height', '75px');
	$(".publisher-box-footer").slideDown(100);
});
	
 // see more
function see_more_question(self) {
    if(self.hasClass('done')){ return; }
    var _this = self;
	var tab = _this.closest('.panel-body').find('.dataContainer:last');
    var user_id = tab.attr('data-owner');
    var question_id = tab.attr('data-question');
    var loading = _this.find('.loader');
	var text = _this.find('span');
	var remove = _this.attr('data-remove');
   
   /* show loader & hide text */
    _this.addClass('loading');
    text.hide();
   loading.removeClass('x-hidden');
    
	/* get & load data */
    $.post( site_ajax , {'f':'question_more', 'owner_id': user_id, 'question_id': question_id}, function(response) {
        
		_this.removeClass('loading');
        text.show();
        loading.addClass('x-hidden');
        
		/* check the response */
        if(response.callback) { eval(response.callback); } 
		else {
            if(response.html) {
                    self.parent().before(response.html);
            } else {
                if(remove) {
                    _this.remove();
                } else {
                    _this.addClass('done');
                    text.text(__['There is no more data to show']);
                }
            }
        }
    }, 'json')
    .fail(function() {
        _this.removeClass('loading');
        text.show();
        loading.addClass('x-hidden');
        _modal('#_modal-message', {title: 'Error', message: 'There is some thing went worng!'});
    });
}

 
    // run see-more-question
    $('body').on('click', '.js_see-more_question', function (e) { e.preventDefault(); see_more_question($(this)); });
   
   $('.js_see-more-infinite-question').bind('inview', function (event, visible) {
        if(visible == true) { see_more_question($(this)); }
    });


 /* create id question*/
 $('.publisher-video').before('<div class="publisher-question"></div>');
  
	 /*add a option more to question*/ 
	  function QuestObj(self){ 
	  selft.removeClass('last');
	  selft.parent().after('<p class="mb10"><input class="uiInput shareQuestionOption last" name="options[]" type="text" placeholder="+ '+__["Add an option"]+'..." /></p>'); 
	 
	 $('.shareQuestionOption.last').focus(function(){  selft = $(this); if(selft.hasClass('last')){  QuestObj(self); } });
	 $('.shareQuestionOption.last').focus(function(){ if($(this).hasClass('active')) { return; } 
	 
	 if($(this).data('value') == undefined){ $(this).data('value', $(this).val()); } if($(this).val() == $(this).data('value')){ $(this).val(''); } if($(this).val() == ''){ $(this).addClass("active"); if($(this).hasClass('uiTextArea')){ $(this).parent().next().show(); } } });
	 
	 $('.shareQuestionOption.last').blur(function(){ if($(this).val() == ''){ $(this).val($(this).data('value')); $(this).removeClass("active"); if($(this).hasClass('uiTextArea') && !$(this).hasClass('shareNews')){ $(this).parent().next().hide(); } } });
 }

/* active focus and evalue*/
function active_functions_question(){  
	$('.postQuestion').find('.uiInput, .uiTextArea').focus(function(){
		if($(this).hasClass('active')) {
			return;
		}
		if($(this).data('value') == undefined){ 
			$(this).data('value', $(this).val());
		}
		if($(this).val() == $(this).data('value')){
			$(this).val('');
		} if($(this).val() == ''){
			$(this).addClass("active");
			if($(this).hasClass('uiTextArea')){
				$(this).parent().next().show();
			}
		}
	});
	$('.postQuestion').find('.uiInput, .uiTextArea').blur(function(){
		if($(this).val() == ''){
			$(this).val($(this).data('value'));
			$(this).removeClass("active");
			if($(this).hasClass('uiTextArea') && !$(this).hasClass('shareNews')){
				$(this).parent().next().hide();
			}
		}
	});  
	$('.postQuestion').find('.shareQuestionOption.last').focus(function(){ selft = $(this); if(selft.hasClass('last')){ QuestObj(self); } });
}

function create_wall_question(){ 
/*add tab form in publisher */
	/* show upload loader */
    var publisher = $('.publisher');		
	if($('.postQuestion').length == 0){
		$('.postText').attr({'placeholder': __["Ask something"]});	
		$('.publisher-question').html('<div class="publisherContent postQuestion item"><button type="button" class="close js_publisher-question-remover" title="Remove"><span>×</span></button><div class="pb10 pr10 pl10"><div class="tcenter"><p class="mb10"><input class="uiInput shareQuestionOption" type="text" name="options[]" placeholder="+ '+__["Add an option"]+'..." /></p><p class="mb10"><input class="uiInput shareQuestionOption" type="text" name="options[]" placeholder="+ '+__["Add an option"]+'..." /></p><p class="mb10"><input class="uiInput shareQuestionOption last" type="text" name="options[]" placeholder="+ '+__["Add an option"]+'..." /></p></div></div></div>').show();		
		active_functions_question();
		/* add the attachment to publisher data */
		var question = publisher.data('question');	
	}
}

    $(function(){
			/*add tab form in publisher */
		$('body').on('click', '.js_question', function(e){
			e.preventDefault();  
			$(".postText").css('height', '75px');
			$(".publisher-box-footer").slideDown(100);
				/* show upload loader */
			var publisher = $('.publisher');	
			if($('.postQuestion').length == 0){
				$('.publisher-question').html('<div class="publisherContent postQuestion item"><button type="button" class="close js_publisher-question-remover" title="Remove"><span>×</span></button><div class="pt10 pb10 pr10 pl10"><p class="mb10"><div class="pt10 pb10"><div class="pubQuestionOptions"><span><strong>'+__["Question Options"]+':</strong></span></div></div><div class="tcenter"><p class="mb10"><input class="uiInput shareQuestionOption" type="text" name="options[]" placeholder="+ '+__["Add an option"]+'..." /></p><p class="mb10"><input class="uiInput shareQuestionOption" type="text" name="options[]" placeholder="+ '+__["Add an option"]+'..." /></p><p class="mb10"><input class="uiInput shareQuestionOption last" type="text" name="options[]" placeholder="+ '+__["Add an option"]+'..." /></p></div></div></div>').show();	
				active_functions_question();
			} else {
				$(this).toggleClass('active');
				$('.publisher-question').slideToggle('fast');	
			}
		});
  	
	/* publisher tab question remover and liberate in post */
    $('body').on('click', '.js_publisher-question-remover', function() {
		    $('.postText').attr({'placeholder' : 'What\'s going on? #Hashtag.. @Mention.. Link.'});	
    	    var item = $(this).closest('.publisher-question .item');
        // remove the attachment from publisher data
        	$('.publisher').removeData('question');
        	$('.publisher-question').hide();
			$('.js_publisher_plugin_tab').removeClass('active');
			$('.js_publisher_tab[data-tab="post"]').addClass('active');		
        // remove the attachment item 
            item.remove();
    });


/*--- POST POLL ---*/

   /* select an aswer */
	$('body').on('click', '.questionBtn, .questionRadioBtn, .questionResultsBar', function(e) {				 
   /* get input id */
   var radioBtn = $(this).parents('tr:first').find('input');
   
   /* stop action doble click */
   if(radioBtn.attr("disabled")){ return; }			
   
   /*get votes and check if my user select a option*/
   var parent = $(this).parents('.dataContainer');			
   var total = parent.find("input[name='question[votes]']");
   var selected = parent.find("input[name='question[selected]']");
			
   /* disalble all inputs */
   parent.find('input.questionBtn').each(function(i) { $(this).attr("disabled", 'disabled'); });
   
   /*check if vote exist*/
   if(selected.val() != '' && radioBtn.attr('data-option') == selected.val()) { var action = 'unselect'; } else { var action = 'select'; }
   
   /*post*/
   var post_id = parent.attr('data-post');
   /*owner*/
   var owner_id = parent.attr('data-owner');
   /*question*/
   var question_id = parent.attr('data-question');
   /*option*/
   var option_id = radioBtn.attr('data-option');			
   /* send ajax to db*/
   $.ajax({type: "get",url: site_ajax, data:{'f':'question_select', 'question_id':question_id, 'option_id':option_id, 'post_id':post_id, 'owner_id':owner_id, 'do':action}, cache:false, success: function(result) { 
					if(result.result!=1) { 
					/* alert of error */
					_modal('#_modal-message', {title: 'Error', message: 'There is some thing went worng!' });
					/*enable all inputs*/ 
                    parent.find('input.questionBtn').each(function(i) { $(this).removeAttr("disabled"); });
					}
					else {			
								
					/* select answer */
						if(selected.val() == '') {
							var selectedId, width;
							total.val(parseInt(total.val()) + 1);
							parent.find('tr').each(function(i) {															
								var votes = $(this).find('.optionVotes');
								var whoVoted = $(this).find('.whoVoted');
								var questionBtn = $(this).find('input.questionBtn');
								/*count votes*/
								if(questionBtn.attr('data-option') == radioBtn.attr('data-option')) {
									width = (parseInt(votes.text()) + 1) / (parseInt(total.val())) * 100;
									votes.text(parseInt(votes.text()) + 1);
									whoVoted.attr('hits', parseInt(whoVoted.attr('hits')) + 1);
									selectedId = questionBtn.attr('data-option');
								}else {
									width = (parseInt(votes.text())) / (parseInt(total.val())) * 100;
								}								
								$(this).find('.shaded').width(width+'%');
							});
							selected.val(selectedId);
						
						/* unselect answer */
						}else if (selected != '' && radioBtn.attr('data-option') == selected.val()) {
							var width;
							total.val(parseInt(total.val()) - 1);
							parent.find('tr').each(function(i) {
								var votes = $(this).find('.optionVotes');
								var whoVoted = $(this).find('.whoVoted');
								var questionBtn = $(this).find('input.questionBtn');
								/*count votes*/
								if(questionBtn.attr('data-option') == radioBtn.attr('data-option')) {
									width = (parseInt(votes.text()) - 1) / (parseInt(total.val())) * 100;
									votes.text(parseInt(votes.text()) - 1);
									whoVoted.attr('hits', parseInt(whoVoted.attr('hits')) - 1);
								}else {
									width = (parseInt(votes.text())) / (parseInt(total.val())) * 100;
								}
								width = (isNaN(width))? 0 : width;
								$(this).find('.shaded').width(width+'%');
							});
							selected.val('');
							
						/* change for other answer */
						}else {
							var selectedId, width;
							parent.find('tr').each(function(i) {
								var votes = $(this).find('.optionVotes');
								var whoVoted = $(this).find('.whoVoted');
								var questionBtn = $(this).find('input.questionBtn');
								/*count votes*/
								/* new option */
								if(questionBtn.attr('data-option') == radioBtn.attr('data-option')) {
									width = (parseInt(votes.text()) + 1) / (parseInt(total.val())) * 100;
									votes.text(parseInt(votes.text()) + 1);
									whoVoted.attr('hits', parseInt(whoVoted.attr('hits')) + 1);
									selectedId = questionBtn.attr('data-option');
								/* old option */
								}else if (questionBtn.attr('data-option') == selected.val()) {
									width = (parseInt(votes.text()) - 1) / (parseInt(total.val())) * 100;
									votes.text(parseInt(votes.text()) - 1);
									whoVoted.attr('hits', parseInt(whoVoted.attr('hits')) - 1);
								}else {
									width = (parseInt(votes.text())) / (parseInt(total.val())) * 100;
								}
								
								$(this).find('.shaded').width(width+'%');
							});
							selected.val(selectedId);
						}
						
   /* select radio check */				
   if(selected.val() != '' && radioBtn.attr('data-option') == selected.val()) { radioBtn.prop("checked", true); }else { radioBtn.prop("checked", false); }
						
   /*enable all inputs*/ 
   parent.find('input.questionBtn').each(function(i) { $(this).removeAttr("disabled"); });
						
					}
                }
            });
			
			e.stopPropagation();
			
			
        });

	});
	
	
    //poll invite
    function invite_friend_list(type, post_id){ 
       __modal(site_url+'/plugin_requests.php?f=invite_to_post&type='+type+'&post_id='+post_id);
	}	
	
	/*select invite*/
    $('body').on('click', '.friend_container ul li', function(){
		$(this).toggleClass("active");
		if($(this).find("input[type=checkbox]").attr("checked")==true){
			$(this).find("input[type=checkbox]").removeAttr("checked");
		}else{
			$(this).find("input[type=checkbox]").attr("checked","checked");
		}
	});
	
	/* invite search */
	$('body').on('keyup','#friend_name', function(){
		var typed = $(this).val();
		if(typed == ""){
			$('.friend_container ul li').show();
		}else{
			$('.friend_container ul li').hide();
			
			$(".friend_container ul li").each(function(){
				var name = $(this).find("div:last").text();
				if((name.substr(0,typed.length)).toLowerCase() == typed.toLowerCase()){
					$(this).show();
				}
			});
		}
	});
	
    /* invite friends */
	$('body').on('click', '.send_friend_invite', function(e) {
			e.preventDefault();
			var loading  = $('#formLoading');
			var button = $(this);
			// prepare request data
            loading.show();
			button.attr("disabled", 'disabled');
			$(".friend_invite").ajaxForm({ success:  processInvite }).submit(); 
			e.stopPropagation();
    });	
	
	/* invite friends complete */
	function processInvite(data) {
       var loading  = $('#formLoading');
       var button = $('.send_friend_invite');
	                if(data) {
						loading.hide();
						button.removeAttr("disabled");
						 //$('#_modal').modal('hide');
						Wo_CloseModels();
					} else {
						/* check if the _modal not visible, show it */
    					if(!$('#_modal').is(":visible")) $('#_modal').modal('show');
    					/* update the _modal-content with the rendered template */
    					//$('.modal-content:last').html('<div class="mb20 mt20 ml10"><h4>Error no can invite to friend. try more late!!!<h4></div>');
						$('#_modal_leader .modal-content').html('<div class="mb20 mt20 ml10"><h4>Error no can invite to friend. try more late!!!<h4></div>');
					}
   }	