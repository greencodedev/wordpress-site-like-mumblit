<?php

/* Functions specific to the included option settings */

/*-----------------------------------------------------------------------------------*/
/* Add Favicon
/*-----------------------------------------------------------------------------------*/

function st_favicon() {
	if (of_get_option('st_custom_favicon')) {
	echo '<link rel="shortcut icon" href="'. of_get_option('st_custom_favicon') .'"/>'."\n";
	}
}

add_action('wp_head', 'st_favicon');