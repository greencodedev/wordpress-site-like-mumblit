<ul class="list-group">
	<li class="list-group ads-side post-media">
		
		<div class="head">
			<?php
				if($preview == 0){
					include './themes/' . $wo['config']['theme'] . '/layout/plugins/ads/ads_random_btn.phtml';
				}
			?>
			<span>
				<img class="fa fa-fw mr5" src="<?php echo $wo['config']['theme_url'];?>/img/promote.png" alt="ads" title="ads">
				<?php echo $wo['lang']['plugin_ads_suggested-page']; ?>
			</span>
		</div>
		
		<div class="second-head">
			<div class="media">
				
				<div class="media-object pull-left">
					<a class="ads-click-link" data-id="<?php echo $ad['id']; ?>" href="<?php echo $wo['config']['site_url'].'/'.$info_ads_page['page_name']; ?>" data-ajaxify="true">
						<img width="40" height="40" src="<?php echo $info_ads_page['photo']; ?>">
					</a>
				</div>
				
				<div class="media-body">
					<h5 class="media-heading">
						<a class="ads-click-link" data-id="<?php echo $ad['id']; ?>" href="<?php echo $wo['config']['site_url'].'/'.$info_ads_page['page_name']; ?>">
							<?php echo $info_ads_page['page_title']; ?>
						</a>
					</h5>
					<span>
					   <?php echo Wo_GetLikeButton($info_ads_page['page_id']); ?>
					</span>
					<span class="<?php echo Wo_RightToLeft('pull-right');?>">
					   <?php echo Wo_CountPageLikes($info_ads_page['page_id']); ?>
					   <i class="fa fa-fw fa-thumbs-up"></i>
					</span> 				
				</div>
				
			</div>
		</div>
		
		<p class="ad-description">
			<?php echo $ad['description']; ?>
		</p>
		
		<div class="ad-image">
			<a class="ads-click-link" data-id="<?php echo $ad['id']; ?>" href="<?php echo $wo['config']['site_url'].'/'.$info_ads_page['page_name']; ?>">
				<img src="<?php if(@preg_match('/http/i', $ad['images'])){ echo $ad['images']; } else { echo $wo['config']['site_url'].'/'.$ad['images']; } ?>">
			</a>
		</div>
		
	</li>
</ul>