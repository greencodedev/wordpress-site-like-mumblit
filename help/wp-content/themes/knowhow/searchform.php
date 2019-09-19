<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
        <input type="text" value="<?php _e("Search...", "framework") ?>" name="s" id="s" onblur="if (this.value == '')  {this.value = '<?php _e("Search...", "framework") ?>';}" onfocus="if (this.value == '<?php _e("Search...", "framework") ?>')  
{this.value = '';}" />
</form>