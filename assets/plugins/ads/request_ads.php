<?php
/* request ads
 * @autor : Pp Galvan - LdrMx
 */

/* variable */ 
$ad_id = ( isset($_POST['ad_id']) ? $_POST['ad_id'] : ( isset($_GET['ad_id']) ? $_GET['ad_id'] : 0 ) );	
$ad_id = Wo_Secure($ad_id);

/*ADMIN ADS*/
if($f == 'admin_ads'){

	/* return ajax*/
	$is_admin = Wo_IsAdmin();
	$is_moderoter = Wo_IsModerator();
	if ($is_admin === false && $is_moderoter === false) { 
		return_json(array('result' => 0, 'error' => true, 'title' => 'Error', 'btn' => '', 'content' => "You don't have the right permission to access this"));
	}	

	switch ($task) {		
		case 'ad_delete':			
			
			// valid inputs
			if(!isset($ad_id) || !is_numeric($ad_id)){ 
				return_json(array('result'=>'0', 'error' => true, 'title'=>'Error', 'btn'=>'', 'content' => "This Ad id no is a number"));
			}					
			$sqlConnect->query("DELETE FROM ads_ldrsoft WHERE id='{$ad_id}' LIMIT 1");			            
			/* return */
			return_json(array('result' => 1, 'message' => "Done, Plugin info have been updated", 'success' => true));			
			break;
		 
		 case 'ad_activate':		
			
			// valid inputs
			if(!isset($ad_id) || !is_numeric($ad_id)){ 
				return_json(array('result' => '0', 'error' => true, 'title'=>'Error', 'btn'=>'', 'content' => "This Ad id no is a number"));
			}	        
			$plan_query = $sqlConnect->query("SELECT `A`.`plan_id`, `P`.`quantity`, `P`.`type`, `P`.`money_type`
									FROM `ads_plans` AS P LEFT JOIN `ads_ldrsoft` AS A ON `A`.`plan_id`=`P`.`id`
									WHERE `A`.`id`='{$ad_id}' LIMIT 1");			
			$plan_info = $plan_query->fetch_assoc();		  			
			  			
			if($plan_info['type'] !='period'){ 			
				$sqlConnect->query("UPDATE ads_ldrsoft SET paid='1', status='1', quantity = '{$plan_info['quantity']}' 
									WHERE id='{$ad_id}' LIMIT 1");			
			
			} else {
			
				$date_start = time();
				$date_end = time()+(60*60*24*$plan_info['quantity']);
				$sqlConnect->query("
							UPDATE `ads_ldrsoft` SET `paid`='1', `status`='1', `quantity`= '{$plan_info['quantity']}', `date_start`= '{$date_start}', `date_end`= '{$date_end}' 
							WHERE `id`='{$ad_id}' LIMIT 1");
			
			}			
			$db_update =  $sqlConnect->affected_rows;
			
			/* return */
			if($db_update == 1){
			   $data = array('result' => 1, 'message' => "Successfully info have been updated", 'results'=> $db_update, 'success' => true, 'quantity' => $plan_info['quantity']);
			} else {
			   $data = array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
			}	
			return_json($data);
			
			break;
		
		 case 'save_ad_edit':	
			
			$campaign_name = ( isset($_POST['campaign_name']) ? $_POST['campaign_name'] : ( isset($_GET['campaign_name']) ? $_GET['campaign_name'] : '' ) );
			$link = ( isset($_POST['link']) ? $_POST['link'] : ( isset($_GET['link']) ? $_GET['link'] : '' ) );
			$title = ( isset($_POST['title']) ? $_POST['title'] : ( isset($_GET['title']) ? $_GET['title'] : '' ) );
			$description = ( isset($_POST['description']) ? $_POST['description'] : ( isset($_GET['description']) ? $_GET['description'] : '' ) );
			$plan_type = ( isset($_POST['plan_type']) ? $_POST['plan_type'] : ( isset($_GET['plan_type']) ? $_GET['plan_type'] : '' ) );
			$plan_id = ( isset($_POST['plan_id']) ? $_POST['plan_id'] : ( isset($_GET['plan_id']) ? $_GET['plan_id'] : '' ) );
			$status = ( isset($_POST['status']) ? $_POST['status'] : ( isset($_GET['status']) ? $_GET['status'] : 0 ) );
			
			if(empty($campaign_name) or empty($title) or empty($description) or empty($plan_id) ) { 
			   return_json(array('result' => 0, 'error' => true, 'message' => "complete formulary"));
			}	
			
			$campaign_name = Wo_Secure($campaign_name);
			$link = Wo_Secure($link);
			$title = Wo_Secure($title);
			$description = Wo_Secure($description);
			$plan_type = Wo_Secure($plan_type);
			$plan_id = Wo_Secure($plan_id);
			$status = Wo_Secure($status);
			$ad_id = Wo_Secure($ad_id);
			
			$sqlConnect->query("
				UPDATE ads_ldrsoft SET campaign_name = '{$campaign_name}', link = '{$link}', title = '{$title}', 
				description = '{$description}', plan_type = '{$plan_type}', plan_id = '{$plan_id}', status = '{$status}' 
				WHERE id= '{$ad_id}' LIMIT 1
			");
			$db_update =  $sqlConnect->affected_rows;
			
			/* return */
			if($db_update == 1){
			   return_json(array('result' => 1, 'message' => "Successfully saved", 'results'=> $db_update, 'success' => true));
			} else {
			   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
			}	
			break;	
				 
		 case 'plan_save':		 
			$plan_id = ( isset($_POST['plan_id']) ? $_POST['plan_id'] : ( isset($_GET['plan_id']) ? $_GET['plan_id'] : '0' ) );
			$name = ( isset($_POST['name']) ? $_POST['name'] : ( isset($_GET['name']) ? $_GET['name'] : '' ) );
			$description = ( isset($_POST['description']) ? $_POST['description'] : ( isset($_GET['description']) ? $_GET['description'] : '' ) );
			$type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : '' ) );   	    
			$price = ( isset($_POST['price']) ? $_POST['price'] : ( isset($_GET['price']) ? $_GET['price'] : 0 ) );
			$quantity = ( isset($_POST['quantity']) ? $_POST['quantity'] : ( isset($_GET['quantity']) ? $_GET['quantity'] : 0 ) );
			$enable = ( isset($_POST['enable']) ? $_POST['enable'] : ( isset($_GET['enable']) ? $_GET['enable'] : '1' ) );
			$payment_currency = ( isset($_POST['payment_currency']) ? $_POST['payment_currency'] : ( isset($_GET['payment_currency']) ? $_GET['payment_currency'] : 'USD' ) );			
			
			$name = Wo_Secure($name);
			$description = Wo_Secure($description);
			$type = Wo_Secure($type);
			$price = Wo_Secure($price);
			$quantity = Wo_Secure($quantity);
			$plan_id = Wo_Secure($plan_id);
			$enable = Wo_Secure($enable);
			$payment_currency = Wo_Secure($payment_currency);
		   
			/* return */
			if(empty($name) or empty($description) or empty($price) or empty($quantity) ) { 
				return_json(array('result' => 0, 'error' => true, 'message' => "complete formulary"));
			}
			if(!is_numeric($price)) { 
				return_json(array('result' => 0, 'error' => true, 'message' => "The price is incorrect no is a number")); 
			}	 	
			if(!is_numeric($quantity)) { 
				return_json(array('result' => 0, 'error' => true, 'message' => "The quantity is incorrect no is a number")); 
			}	 		
		   
			/* save */
			$plan = Ads::plan_add($plan_id, $name, $description, $type, $price, $quantity, $enable, $payment_currency);	 			
			
			/* return */
			if(!empty($plan['id'])){
				return_json(array('result' => 1, 'sucess' => true, 'message' => "Successfully saved", 'success' => true));
			} else {
				return_json(array('result' => 0, 'error' => true, 'message' => "Try more late!!"));	
			}
			break;	
									
		 case 'plan_delete':
			 
			 // valid inputs
			 if(!isset($plan_id) || !is_numeric($plan_id)){ 
				 return_json(array('result' => 0, 'error' =>  true, 'message' => "This plan id no exist")); 
			 }			
			 $sqlConnect->query("DELETE FROM ads_plans WHERE id='{$plan_id}' LIMIT 1");
			 
			 /* return */
			 return_json(array('result' => 1, 'message' => "Done, Plan info have been deleted", 'success' => true));
			 break;
					
					
		 case 'payment_setting': 		 
			// valid inputs
			$ads_enable_paypal = ( isset($_POST['ads_enable_paypal']) ? $_POST['ads_enable_paypal'] : ( isset($_GET['ads_enable_paypal']) ? $_GET['ads_enable_paypal'] : '1' ) );
			$ads_enable_sandbox = ( isset($_POST['ads_enable_sandbox']) ? $_POST['ads_enable_sandbox'] : ( isset($_GET['ads_enable_sandbox']) ? $_GET['ads_enable_sandbox'] : '1' ) );
			$corporate_paypal_email = ( isset($_POST['corporate_paypal_email']) ? $_POST['corporate_paypal_email'] : ( isset($_GET['corporate_paypal_email']) ? $_GET['corporate_paypal_email'] : '' ) );
			$paypal_email_notification = ( isset($_POST['paypal_email_notification']) ? $_POST['paypal_email_notification'] : ( isset($_GET['paypal_email_notification']) ? $_GET['paypal_email_notification'] : '' ) );	
			$ads_enable_voguepay = ( isset($_POST['ads_enable_voguepay']) ? $_POST['ads_enable_voguepay'] : ( isset($_GET['ads_enable_voguepay']) ? $_GET['ads_enable_voguepay'] : '1' ) );
			$voguePayMerchantId = ( isset($_POST['voguePayMerchantId']) ? $_POST['voguePayMerchantId'] : ( isset($_GET['voguePayMerchantId']) ? $_GET['voguePayMerchantId'] : '' ) );	
			$ads_enable_stripe = ( isset($_POST['ads_enable_stripe']) ? $_POST['ads_enable_stripe'] : ( isset($_GET['ads_enable_stripe']) ? $_GET['ads_enable_stripe'] : '1' ) );
			$stripe_secret_key = ( isset($_POST['stripe_secret_key']) ? $_POST['stripe_secret_key'] : ( isset($_GET['stripe_secret_key']) ? $_GET['stripe_secret_key'] : '' ) );   	    
			$stripe_publishable_key = ( isset($_POST['stripe_publishable_key']) ? $_POST['stripe_publishable_key'] : ( isset($_GET['stripe_publishable_key']) ? $_GET['stripe_publishable_key'] : '' ) );
						
			$ads_enable_paypal = Wo_Secure($ads_enable_paypal);	 
			$ads_enable_sandbox = Wo_Secure($ads_enable_sandbox);
			$corporate_paypal_email = Wo_Secure($corporate_paypal_email);
			$paypal_email_notification = Wo_Secure($paypal_email_notification);			
			$ads_enable_voguepay = Wo_Secure($ads_enable_voguepay);
			$voguePayMerchantId = Wo_Secure($voguePayMerchantId);			
			$ads_enable_stripe = Wo_Secure($ads_enable_stripe);
			$stripe_secret_key = Wo_Secure($stripe_secret_key);
			$stripe_publishable_key = Wo_Secure($stripe_publishable_key);
								   
			$sqlConnect->query("
				UPDATE plugins_system SET 
				ads_enable_paypal = '{$ads_enable_paypal}',
				ads_enable_sandbox = '{$ads_enable_sandbox}',	
				corporate_paypal_email = '{$corporate_paypal_email}',		
				paypal_email_notification = '{$paypal_email_notification}',		
				ads_enable_voguepay = '{$ads_enable_voguepay}',
				voguePayMerchantId = '{$voguePayMerchantId}',
				ads_enable_stripe = '{$ads_enable_stripe}',
				stripe_secret_key = '{$stripe_secret_key}',
				stripe_publishable_key = '{$stripe_publishable_key}'		
				WHERE system_id = '1' LIMIT 1
			");						
			$db_update =  $sqlConnect->affected_rows;
			
			/* return */
			if($db_update == 1){
			   return_json(array('result' => 1, 'message' => "Done, Plugin info have been updated", 'results'=> $db_update, 'success' => true));
			} else {
			   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
			}
			break;	
			
		 case 'position_setting': 	 

			// valid inputs 
			$enable_ads_left_column = ( isset($_POST['enable_ads_left_column']) ? $_POST['enable_ads_left_column'] : ( isset($_GET['enable_ads_left_column']) ? $_GET['enable_ads_left_column'] : '1' ) );
			$ads_no_left_column = ( isset($_POST['ads_no_left_column']) ? $_POST['ads_no_left_column'] : ( isset($_GET['ads_no_left_column']) ? $_GET['ads_no_left_column'] : '1' ) );  
			$pagelet_ego = ( isset($_POST['pagelet_ego']) ? $_POST['pagelet_ego'] : ( isset($_GET['pagelet_ego']) ? $_GET['pagelet_ego'] : '1' ) );	
			$ads_no_right_column = ( isset($_POST['ads_no_right_column']) ? $_POST['ads_no_right_column'] : ( isset($_GET['ads_no_right_column']) ? $_GET['ads_no_right_column'] : '3' ) );
			$enable_ad_rigth_place = ( isset($_POST['enable_ad_rigth_place']) ? $_POST['enable_ad_rigth_place'] : ( isset($_GET['enable_ad_rigth_place']) ? $_GET['enable_ad_rigth_place'] : '' ) );
			$enable_ads_on_post = ( isset($_POST['enable_ads_on_post']) ? $_POST['enable_ads_on_post'] : ( isset($_GET['enable_ads_on_post']) ? $_GET['enable_ads_on_post'] : '1' ) );  
			$enable_website_ads_on_post = ( isset($_POST['enable_website_ads_on_post']) ? $_POST['enable_website_ads_on_post'] : ( isset($_GET['enable_website_ads_on_post']) ? $_GET['enable_website_ads_on_post'] : '1' ) );
			
			$enable_ads_left_column = Wo_Secure($enable_ads_left_column);
			$ads_no_left_column = Wo_Secure($ads_no_left_column);			
			$pagelet_ego = Wo_Secure($pagelet_ego);	
			$ads_no_right_column = Wo_Secure($ads_no_right_column);
			$enable_ad_rigth_place = Wo_Secure($enable_ad_rigth_place);
			$enable_ads_on_post = Wo_Secure($enable_ads_on_post);
			$enable_website_ads_on_post = Wo_Secure($enable_website_ads_on_post);
		
			$sqlConnect->query("
				UPDATE plugins_system SET 
				enable_ads_left_column = '{$enable_ads_left_column}',
				ads_no_left_column = '{$ads_no_left_column}',			
				pagelet_ego = '{$pagelet_ego}',
				ads_no_right_column = '{$ads_no_right_column}',
				enable_ad_rigth_place = '{$enable_ad_rigth_place}',			
				enable_ads_on_post = '{$enable_ads_on_post}',
				enable_website_ads_on_post = '{$enable_website_ads_on_post}'
				WHERE system_id = '1' LIMIT 1
			");
			$db_update =  $sqlConnect->affected_rows;
			
			/* return */
			if($db_update == 1){
			   return_json(array('result' => 1, 'message' => "Done, Plugin info have been updated", 'results'=> $db_update, 'success' => true));
			} else {
			   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
			}
			break;		
						
		default: 
		 /* return */
		 return_json(array('result' => 1, 'sucess' => true, 'message' => "This task no exist"));
		 break;
	}
}


/*USER ADS*/
if($f == 'ads_get_page'){	
	$page_id = ( isset($_POST['page_id']) ? $_POST['page_id'] : ( isset($_GET['page_id']) ? $_GET['page_id'] : 0 ) );
	// valid inputs
	if(!isset($page_id) ||  $page_id == 0){ return_json(array('result' => 1, 'message' => 'add a page')); }  	
	//get html	
	$html = Wo_LoadPage('plugins/ads/ajax_ads_get_page');
	$html = preg_replace('/\s+/',' ',$html);   		
	// return 
	return_json(array('result' => 1, 'sucess' => true, 'template' => $html));	
}


/* url details */
if($f == 'ads_get_website') {
	$link = ( isset($_POST['link']) ? $_POST['link'] : ( isset($_GET['link']) ? $_GET['link'] : '' ) );
	// valid inputs & return
	if(!isset($link) ||  $link == ''){ 
		return_json(array('result' => 1, 'message' => 'add a link'));
	}      
	
	/*clear url*/
	$link = preg_replace("#www\.#","http://www.",$link);
	$link = preg_replace("#http://http://www\.#","http://www.",$link);
	$link = preg_replace("#https://http://www\.#","https://www.",$link);
	
	// initialize the return array
	$return = array();   
	
	$url_info_json = @file_get_contents("http://api.embed.ly/1/oembed?key=026dd9f909d341e5a745c92865885239&url=".$link);
	$url_info = ( $url_info_json ) ? json_decode($url_info_json, true) : array();     
	$details['src'] = isset($url_info['thumbnail_url']) ? $url_info['thumbnail_url'] : '';
	
	if(!empty($url_info['title'])){
		$leave = 25 - strlen ($url_info['title']);
		$details['title'] = (strlen($url_info['title']) > 25 ) ?  substr_replace($url_info['title'],"",$leave) : '';
	} else {
		$details['title'] = '';
	}
	
	if(!empty($url_info['description'])){
		$leave = 99 - strlen ($url_info['description']);
		$details['desc'] = (strlen($url_info['description']) > 99) ?  substr_replace($url_info['description'],"",$leave) : '';
	} else {
	$details['desc'] = '';
	}
	//echo json_encode($details);
	
	//new src
	/*if($details['src'] != ''){
		$details['src'] = import_image_from_url($details['src'], $wo['user']['user_id'].'_'.rand(1,9999) , 'ads');
	}*/
	
	$details['url'] = $link;
	$details['host'] = parse_url($link, PHP_URL_HOST); 
	$details['count_title'] = (25) - strlen($details['title']);
	$details['count_desc'] = (99) - strlen($details['desc']);   

	$where = " type='clicks' ";    
	
	// get plans
	$wo['plans'] = Ads::get_plans("", FALSE, FALSE, "id DESC", $where);
	$wo['detail'] = $details;	
	
	$html = Wo_LoadPage('plugins/ads/ajax_ads_get_website');   
	/* clear html */
	$html = preg_replace('/\s+/',' ',$html);
	
	/* return */
	return_json(array('result' => '1', 'template' => $html));
}


/* click */
if($f == 'click'){	
	//inputs
	$ad_id = ( isset($_POST['ad_id']) ? $_POST['ad_id'] : ( isset($_GET['ad_id']) ? $_GET['ad_id'] : '0' ) );
	$myresult = 0;
	//check if ad_id exist
	if($ad_id != 0){
		$ad = Ads::get_ad($ad_id, "");
		//check if ad exist & update click
		if($ad){ 
			Ads::processClick($ad);
			$myresult = 1;	
		}
	}
	/* return */
	return_json(array('result' => $myresult));
}


/* plans */
if($f == 'load_plans'){  
	  // valid inputs
	  $type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : 'all' ) );
	  $valid['view'] = array('all', 'clicks', 'impression', 'period');
	 
	  /* return */
	  if(!in_array($type, $valid['view'])) { return_json(array('result' => 0, 'message' => 'this option no exist')); }
	 
	  // get plans
	  if($type == 'impression'){ $where = " type='impression' "; }
	  elseif($type == 'clicks'){ $where = " type='clicks' "; }
	  elseif($type == 'period'){ $where = " type='period' "; }
	  else { $where = ''; }	  
	  $plans = Ads::get_plans("", FALSE, FALSE, "id DESC", $where);
	  
	  $html = "";
	  foreach($plans as $plan){ $html .= '<option value="'.$plan['id'].'">'.$plan['name'].' ('.$plan['price'].' '.$plan['money_type'].')</option>'; }      
	 
	  /* return */
	  return_json(array('result' => 1, 'sucess' => true, 'html' => $html));
}


/*UPLOAD IMAGE*/
if($f == 'upload_image'){
	if($wo['loggedin'] === false) { return false; }
	
	if(isset($_FILES['image'])) {
		$name = $_FILES['image']['name'];                  
		$ext = pathinfo($name, PATHINFO_EXTENSION);

		//create directory 
		if(!file_exists('upload/ads')) { mkdir('upload/ads', 0777, true); }    	  
		$allowed           = 'jpg,png,jpeg,gif';
		$new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
		$extension_allowed = explode(',', $allowed);
		$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);

		//extension allowed
		if(!in_array($file_extension, $extension_allowed)) { return false; }	
		$ar = array('image/png','image/jpeg', 'image/gif');
		
		$prefix = 'ldr_ad'.hash('crc32', time()*rand(1, 9999));
		$image_new_name = $prefix.'_n.'.$ext;
		$image_url = 'upload/ads/'.$image_new_name;
	  
		if(move_uploaded_file($_FILES['image']['tmp_name'], $image_url)){
			//Wo_Resize_Crop_Image(400, 400, $image_url, $image_url, 20);
			$last_file = $image_url;
			$explode2  = @end(explode('.', $image_url));
			$explode3  = @explode('.', $image_url);
			/*$last_file = $explode3[0] . '_full.' . $explode2;
			@Wo_CompressImage($image_url, $last_file, 10);*/							  
		}
		//$image_url_new = $wo['config']['site_url'].'/upload/ads/'.$image_new_name;
		
		/* return */
		return_json(array('image' => $image_url));
	}	  
}



if($f == 'ads_post') {
	 // initialize the return array
	 $return = array();
	 $preview = ( isset($_POST['preview']) ? $_POST['preview'] : ( isset($_GET['preview']) ? $_GET['preview'] : '0' ) );
	 
	if($wo['system']['enable_ads_on_post'] == 1){
		$where = "";
		// NO SHOW WEBSITE LINK AND PAGES IN POST
		if($wo['system']['enable_website_ads_on_post'] == 0){ $where .= " A.type='post' AND "; }
		// MAKE SURE  AD IS NOT PAID
		$where .= " A.paid='1' ";
		// MAKE SURE AD IS NOT PAUSED  
		$where .= " AND A.status='1' ";  
		// MAKE SURE EXIST QUANTITY  
		$where .= " AND A.quantity>'0' ";  
		//IN NEXT VERSION CHECK COUNTRY
		//if($wo['loggedin']){ $where .= " AND ( target_location = '{$wo['user']['country_id']}' OR target_location = 'all')";  }
		
		$nowtime = time();
		$where .= " AND date_start<'{$nowtime}' AND (date_end>'{$nowtime}' OR date_end='0') ";
		
		// MAKE SURE IS GENDER
		if($wo['loggedin']){ $where .= " AND ( target_gender = '{$wo['user']['gender']}' OR target_gender = 'all')"; }
	   
		/*get new ads random*/ 
		$PostAds = Ads::get_ads_lists(1, FALSE, FALSE, " RAND() ", $where);
		
		if($PostAds['count'] == 0){
			return false;
		}
	
		/*get type of ads*/
		foreach($PostAds['ads'] as $ad){
			Ads::updateAd($ad);
			if($ad['type']=='website'){
		
				ob_start();
				include "assets/plugins/ads/template/ads_website.php";
				$html = ob_get_contents();
				ob_end_clean();
				ob_get_level();
				$html = str_replace("\t",'',$html);
				$html = str_replace("\r",'',$html);
				$html = str_replace("\n",'',$html);
				$return['data'] = $html;
		 
			} else if($ad['type']=='page'){
		 
				$info_ads_page = Ads::get_page($ad['page_id']);
				ob_start();
				include "assets/plugins/ads/template/ads_page.php";
				$html = ob_get_contents();
				ob_end_clean();
				ob_get_level();
				$html = str_replace("\t",'',$html);
				$html = str_replace("\r",'',$html);
				$html = str_replace("\n",'',$html);
				$return['data'] = $html;
		 
			}else {
		
				$wo['story'] = Wo_PostData($ad['post_id'], '', 'not_limited');
				/* return */
				if(empty($wo['story']) || Wo_PostExists($wo['story']['post_id']) === false) { 
					return_json(array('title'=> 'Preview Post', 'content' => 'This story no exist', 'btn' => ''));
				}
			
				$html =  Wo_LoadPage('plugins/ads/ajax_ads_get_post');	
				$html = str_replace("\t",'',$html);
				$html = str_replace("\r",'',$html); 
				$html = str_replace("\n",'',$html);
				
				$return['data'] .= '<div class="pagelets list-group" data-id="post-list-ads" data-content="LdrMx" style="display: block;"><div class="post-ads"><div class="head">';	
				
				if($preview == 0){
					ob_start();
					include './themes/' . $wo['config']['theme'] . '/layout/plugins/ads/ads_random_btn.phtml';
					$html2 = ob_get_contents();
					ob_end_clean();
					ob_get_level();
					$html2 = str_replace("\t",'',$html2);
					$html2 = str_replace("\r",'',$html2);
					$html2 = str_replace("\n",'',$html2);
					$return['data'] .= $html2;
				} 
				
				$return['data'] .= '<span><img class="fa fa-fw mr5" src="'.$wo['config']['theme_url'].'/img/promote.png" alt="ads" title="ads">'.$wo['ads']['sponsored'].'</span></div>'.$html.'</div></div></div>';
			}				
			/* return */
			return_json($return);
		}
	
	}
	/* return */
	return_json($return);
}


if($f == 'save') {
	$campaign_name = ( isset($_POST['campaign_name']) ? $_POST['campaign_name'] : ( isset($_GET['campaign_name']) ? $_GET['campaign_name'] : '' ) );
	$title = ( isset($_POST['title']) ? $_POST['title'] : ( isset($_GET['title']) ? $_GET['title'] : '' ) );
	$type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : '' ) );
	$description = ( isset($_POST['description']) ? $_POST['description'] : ( isset($_GET['description']) ? $_GET['description'] : '' ) );
	$imagen = ( isset($_POST['imagen']) ? $_POST['imagen'] : ( isset($_GET['imagen']) ? $_GET['imagen'] : '' ) );
	$plan_type = ( isset($_POST['plan_type']) ? $_POST['plan_type'] : ( isset($_GET['plan_type']) ? $_GET['plan_type'] : '' ) );
	$plan_id = ( isset($_POST['plan_id']) ? $_POST['plan_id'] : ( isset($_GET['plan_id']) ? $_GET['plan_id'] : 0 ) );
	$gender = ( isset($_POST['gender']) ? $_POST['gender'] : ( isset($_GET['gender']) ? $_GET['gender'] : '' ) );
	$id = ( isset($_POST['id']) ? $_POST['id'] : ( isset($_GET['id']) ? $_GET['id'] : '' ) );
	$page_id = ( isset($_POST['page_id']) ? $_POST['page_id'] : ( isset($_GET['page_id']) ? $_GET['page_id'] : 0 ) );
	$post_id = ( isset($_POST['post_id']) ? $_POST['post_id'] : ( isset($_GET['post_id']) ? $_GET['post_id'] : 0 ) );
	$link = ( isset($_POST['link']) ? $_POST['link'] : ( isset($_GET['link']) ? $_GET['link'] : '' ) );
	$status = ( isset($_POST['status']) ? $_POST['status'] : ( isset($_GET['status']) ? $_GET['status'] : 1 ) );
	$display_link = ( isset($_POST['display_link']) ? $_POST['display_link'] : ( isset($_GET['display_link']) ? $_GET['display_link'] : '' ) );
	$location = ( isset($_POST['location']) ? $_POST['location'] : ( isset($_GET['location']) ? $_GET['location'] : '' ) );
	$auto = ( isset($_POST['auto']) ? $_POST['auto'] : ( isset($_GET['auto']) ? $_GET['auto'] : false ) );   
	
	if(!$auto) {             			
		/* required & return */
		if($campaign_name == '') { 
		   return_json(array('result' => 0, 'title' => 'error', 'message' => 'Please add a name to this campaign'));
		}			
		if($plan_id == '') { 			   
		   return_json(array('result' => 0, 'title' => 'error', 'message' => 'Please select a plan if ads to this campaign'));
		}
				   
		if($type == 'website') {
			if(strlen($title)<2) { 
			   return_json(array('result' => 0, 'title' => 'error', 'message' => 'This title is very short'));
			}
			if(strlen($title)>25) { 
			   return_json(array('result' => 0, 'title' => 'error', 'message' => 'This title is very extensive'));
			}
			if(strlen($description)<5) { 
			   return_json(array('result' => 0, 'title' => 'error', 'message' => 'This description is very short'));
			}
			if(strlen($description)>99) { 
			   return_json(array('result' => 0, 'title' => 'error', 'message' => 'This description is very extensive'));
			}
			if($imagen == "") { 
			   return_json(array('result' => 0, 'title' => 'error', 'message' => 'Please add a image to this campaign'));
			}
			if($display_link == "") { 
			   return_json(array('result' => 0, 'title' => 'error', 'message' => 'Please add a link for show to this campaign'));
			}
		}			
	}
	
		/*add to db*/ 		
		if (empty($location)) { $location = 'all'; } else { $location = @json_encode($location); }
			
		$campaign_name = Wo_Secure($campaign_name);
		$title = Wo_Secure($title);
		$description = Wo_Secure($description);
		$plan_id = Wo_Secure($plan_id);
		$user_id = Wo_Secure($wo['user']['user_id']);
		$plan_type = Wo_Secure($plan_type);
		$type = Wo_Secure($type);
		$page_id = Wo_Secure($page_id);
		$post_id = Wo_Secure($post_id);
		$gender = Wo_Secure($gender);
		$imagen = Wo_Secure($imagen);
		$link = Wo_Secure($link);
		$display_link = Wo_Secure($display_link);
		$slug = Wo_Secure(hash('crc32', $campaign_name.time()));
		$status = Wo_Secure($status);
		$id = Wo_Secure($id);
				
		//UPDATE
		if($id != ""){		
			$sqlConnect->query("UPDATE `ads_ldrsoft` SET 
			`campaign_name` = '{$campaign_name}', 
			`title` = '{$title}', 
			`description` = '{$description}', 
			`plan_id` = '{$plan_id}', 
			`plan_type` = '{$plan_type}', 
			`type` = '{$type}', 
			`page_id` = '{$page_id}', 
			`post_id` = '{$post_id}', 
			`target_location` = '{$location}', 
			`target_gender` = '{$gender}', 
			`images` = '{$imagen}', 
			`link` = '{$link}', 
			`display_link` = '{$display_link}', 
			`status` = '{$status}', 
			`updated` = ".time()."
			 WHERE `id`= '{$id}' AND `user_id` = '{$wo['user']['user_id']}'");
			//update
			$get_query = $sqlConnect->query("SELECT * FROM `ads_ldrsoft` WHERE `id`='{$id}' AND `user_id` ='{$wo['user']['user_id']}' LIMIT 1");	
			$ad = $get_query->fetch_assoc();
		
		} else if($id == ""){//NEW ADS	
			
			$sqlConnect->query("INSERT INTO `ads_ldrsoft` (
			`campaign_name`, 
			`title`, 
			`description`, 
			`plan_id`, 
			`user_id`, 
			`plan_type`, 
			`type`, 
			`page_id`, 
			`post_id`, 
			`target_location`, 
			`target_gender`, 
			`images`, 
			`link`, 
			`display_link`, 
			`slug`, 
			`status`, 
			`created`, 
			`updated`
			)
			VALUES ( 
			'{$campaign_name}', 
			'{$title}', 
			'{$description}', 
			'{$plan_id}', 
			'{$wo['user']['user_id']}', 
			'{$plan_type}', 
			'{$type}', 
			'{$page_id}', 
			'{$post_id}', 
			'{$location}', 
			'{$gender}', 
			'{$imagen}', 
			'{$link}', 
			'{$display_link}', 
			'{$slug}', 
			'{$status}', 
			".time().", 
			".time()."
			 )");	
			$adId = $sqlConnect->insert_id;
	
			$get_query = $sqlConnect->query("SELECT * FROM `ads_ldrsoft` WHERE `id`='{$adId}' AND `user_id` ='{$wo['user']['user_id']}' LIMIT 1");	
			$ad = $get_query->fetch_assoc();
		}    
				//$ad = Ads::add($val);

		/* error return */
		if(!$ad) {
		   return_json(array('result' => 0, 'title' => 'Error', 'message' => 'Cant create ads in this moment'));
		}
								
			return_json(array('result' => 1, 'ad_id' =>  $ad['id'], 'slug' => $ad['slug'], 'title' => 'New ad create', 'message' => $ad['id']));
	/*	} else{ 
			$ad = Ads::add($val);
			return_json(array('result' => 1, 'ad_id' =>  $ad['id'], 'slug' => $ad['slug'], 'title' => 'Ads update', 'message' => $ad['id']));
		}
	}		
	 return */
	return_json($response);
}


/* AD DELETE */
if($f == 'delete'){
	$ad_id = ( isset($_POST['ad_id']) ? $_POST['ad_id'] : ( isset($_GET['ad_id']) ? $_GET['ad_id'] : '0' ) );
	$myresult = 0;
	if($ad_id != 0){		
		if ($is_admin === true && $is_moderoter === true) { 
			$sqlConnect->query("DELETE FROM ads_ldrsoft WHERE id='{$ad_id}' LIMIT 1");
		} else {
			$sqlConnect->query("DELETE FROM ads_ldrsoft WHERE user_id='{$wo['user']['user_id']}' AND id='{$ad_id}' LIMIT 1");
		} 		
		$myresult = 1;	
	}	 
	/* return */
	return_json(array('result' => $myresult));
}


/* AD PREVIEW */
if($f=='ads_preview'){	
	$preview = 1;
	$ad_id = ( isset($_POST['ad_id']) ? $_POST['ad_id'] : ( isset($_GET['ad_id']) ? $_GET['ad_id'] : 'all' ) );
	$MyAds = Ads::get_ads_lists(1, FALSE, FALSE, " A.id ", " A.id = '{$ad_id}' ");
		
	foreach($MyAds['ads'] as $ad){

		if($ad['type']=='website'){
			ob_start();
			include "assets/plugins/ads/template/ads_website.php";
			$html = ob_get_contents();
			ob_end_clean();
			ob_get_level();
			$html = str_replace("\t",'',$html);
			$html = str_replace("\r",'',$html);
			$html = str_replace("\n",'',$html);	
			/* return */
			return_json(array('result' => 1, 'title'=> 'Preview Website', 'content' => $html, 'btn' => ''));	
		} else if($ad['type']=='page'){
			$get_page = $sqlConnect->query("SELECT * FROM Wo_Pages WHERE page_id = '{$ad['page_id']}' ORDER BY page_id DESC LIMIT 1");
			$info_ads_page = $get_page->fetch_assoc();	
			$info_ads_page['photo'] =  $wo['config']['site_url'].'/'.$info_ads_page['avatar'];
			$get_likes = $sqlConnect->query("SELECT * FROM Wo_Pages_Likes WHERE page_id = '{$ad['page_id']}' ");
			$info_ads_page['page_likes'] = $get_likes->num_rows;
			ob_start();
			include "assets/plugins/ads/template/ads_page.php";
			$html = ob_get_contents();
			ob_end_clean();
			ob_get_level();
			$html = str_replace("\t",'',$html);
			$html = str_replace("\r",'',$html);
			$html = str_replace("\n",'',$html);
			/* return */
			return_json(array('result' => 1, 'title'=> 'Preview Page', 'content' => $html, 'btn' => ''));	
		} else {
			$wo['story'] = Wo_PostData($MyAds['ads'][0]['post_id'], '', 'not_limited');
			 if(empty($wo['story']) || Wo_PostExists($wo['story']['post_id']) === false) { 
				 /* return */
				 return_json(array('result' => 0, 'title'=> 'Preview Post', 'content' => 'This story no exist', 'btn' => ''));
			 }	 
			$html =  Wo_LoadPage('plugins/ads/ajax_ads_get_post');
			$html = str_replace("\t",'',$html);
			$html = str_replace("\r",'',$html); 
			$html = str_replace("\n",'',$html);				
			/* return */
			return_json(array('result' => 1, 'title'=> 'Preview Post', 'content' => $html, 'btn' => ''));	
		}
	}
}


if($f=='ads_status'){
	if($wo['loggedin'] === false) { return false; }
	$ad_id = ( isset($_POST['ad_id']) ? $_POST['ad_id'] : ( isset($_GET['ad_id']) ? $_GET['ad_id'] : '0' ) );
	$status = ( isset($_POST['status']) ? $_POST['status'] : ( isset($_GET['status']) ? $_GET['status'] : '' ) );
	$myresult = '0';

	// valid inputs & return
	$valid = array('0', '1');
	if(!in_array($status, $valid)){ 
		return_json(array('result' => '0', 'title'=> 'Preview Post', 'content' => 'this value no is valid', 'btn' => ''));
	}
  
	if($ad_id != 0){
		$ad_id = Wo_Secure($ad_id);
		$status = Wo_Secure($status); 
		$user_id = Wo_Secure($wo['user']['user_id']);
		if ($is_admin === true && $is_moderoter === true) { 
			$sqlConnect->query("UPDATE ads_ldrsoft SET status='{$status}' WHERE id='{$ad_id}' LIMIT 1");
		} else {
			$sqlConnect->query("UPDATE ads_ldrsoft SET status='{$status}' WHERE user_id='{$user_id}' AND id='{$ad_id}' LIMIT 1");
		} 
		 $myresult = '1';	
	}	 
	/* return */
	return_json(array('result' => $myresult));     
}
?>