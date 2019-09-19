/*tag*/
 $(function(){

	// run publisher
    /* toggle publisher_tag_friend */
    $('body').on('click', '.js_tag_friend', function() {
        $(this).toggleClass('active');
        $('.publisher_tag_friend').slideToggle('fast');
        $('.publisher_tag_friend').find('input').focus();
    });
	
	    /* toggle activated publisher-meta */
    $('body').on('keyup', '.publisher_tag_friend input', function() {
        if($(this).val() == '') {
            $('.js_tag_friend').removeClass('activated');
        } else {
            $('.js_tag_friend').addClass('activated');
        }
    });
	
});