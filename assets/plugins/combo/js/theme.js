var themeHtml = '<div id="style-selector"><div class="style-toggle"></div><div id="style-selector-container"><div class="style-selector-wrapper"><div class="ss-content ss-content-first clearfix"><a href="https://codecanyon.net/user/ldrmx" target="_blank" class="buy-button ss-button">Buy WoWonder Plugins!</a></div><div class="ss-content ss-no-styles"><span class="ss-title">WoWonder Plugins</span></div><span class="ss-title ss-gray">Plugin For WoWonder comes with two beautiful themes, you can view the demo of each theme below.</span><div class="ss-content demos-wrapper clearfix"><div class="demos"><div class="demo-sites clearfix">'+
            '<a href="'+site_url+'/?theme=wowonder" class="demo classic-demo">'+
              '<span class="demo-bg"><img alt="plugin" src="'+site_url+'/upload/admin/wowonder.png"></span>'+
              '<span class="demo-name">Default</span>'+
            '</a>'+
            '<a href="'+site_url+'/?theme=sunshine" class="demo launch-demo">'+
              /*'<div class="new-demo-badge">NEW</div>'+*/
              '<span class="demo-bg"><img alt="plugin" src="'+site_url+'/upload/admin/sunshine.png"></span>'+
              '<span class="demo-name">sunshine</span>'+
            '</a>'+
'</div></div></div></div></div></div>';
		  
$(function(){
	if($('#style-selector').length == 0){
		$('body').append(themeHtml);
	}
});
		  

// Handle the stye selector toggle button click
$('body').on('click', '#style-selector .style-toggle', function(e) { e.preventDefault();
  if ( $( 'body' ).hasClass( 'ss-close' ) ) {
    $( 'body' ).removeClass( 'ss-close' );
    $( 'body' ).addClass( 'ss-open' );
    $( '#style-selector' ).animate( {'left': '-' + $( '#style-selector' ).width() + 'px' }, 'slow' );
  } else {
    $( 'body' ).removeClass( 'ss-open' );
    $( 'body' ).addClass( 'ss-close' );
    $( '#style-selector' ).animate( {'left': '0px'}, 'slow' );
  }
});