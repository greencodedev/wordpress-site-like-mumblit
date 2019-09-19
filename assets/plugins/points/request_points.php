<?php
if($f == 'admin_points'){
	$item_id = ( isset($_POST['item_id']) ? $_POST['item_id'] : ( isset($_GET['item_id']) ? $_GET['item_id'] : 0 ) );	

	switch ($task) {
		case 'setting':
			   
			$setting_userpoints_enable_offers = ( isset($_POST['setting_userpoints_enable_offers']) ? $_POST['setting_userpoints_enable_offers'] : ( isset($_GET['setting_userpoints_enable_offers']) ? $_GET['setting_userpoints_enable_offers'] : 0 ) );
			$setting_userpoints_enable_shop = ( isset($_POST['setting_userpoints_enable_shop']) ? $_POST['setting_userpoints_enable_shop'] : ( isset($_GET['setting_userpoints_enable_shop']) ? $_GET['setting_userpoints_enable_shop'] : 0 ) );
			$setting_userpoints_enable_topusers = ( isset($_POST['setting_userpoints_enable_topusers']) ? $_POST['setting_userpoints_enable_topusers'] : ( isset($_GET['setting_userpoints_enable_topusers']) ? $_GET['setting_userpoints_enable_topusers'] : 0 ) );
			$setting_userpoints_enable_statistics = ( isset($_POST['setting_userpoints_enable_statistics']) ? $_POST['setting_userpoints_enable_statistics'] : ( isset($_GET['setting_userpoints_enable_statistics']) ? $_GET['setting_userpoints_enable_statistics'] : 0 ) );
			$userpoints_invitation = ( isset($_POST['userpoints_invitation']) ? $_POST['userpoints_invitation'] : ( isset($_GET['userpoints_invitation']) ? $_GET['userpoints_invitation'] : 0 ) );
			$userpoints_refer = ( isset($_POST['userpoints_refer']) ? $_POST['userpoints_refer'] : ( isset($_GET['userpoints_refer']) ? $_GET['userpoints_refer'] : 0 ) );
			$level_allow_transfer = ( isset($_POST['level_allow_transfer']) ? $_POST['level_allow_transfer'] : ( isset($_GET['level_allow_transfer']) ? $_GET['level_allow_transfer'] : 0 ) );
			$level_max_transfer = ( isset($_POST['level_max_transfer']) ? $_POST['level_max_transfer'] : ( isset($_GET['level_max_transfer']) ? $_GET['level_max_transfer'] : 0 ) );
			
			$sqlConnect->query("UPDATE plugins_system SET 
					setting_userpoints_enable_offers = '{$setting_userpoints_enable_offers}',
					setting_userpoints_enable_shop = '{$setting_userpoints_enable_shop}',
					setting_userpoints_enable_topusers = '{$setting_userpoints_enable_topusers}',
					setting_userpoints_enable_statistics = '{$setting_userpoints_enable_statistics}',
					userpoints_invitation = '{$userpoints_invitation}',
					userpoints_refer = '{$userpoints_refer}',
					level_allow_transfer = '{$level_allow_transfer}',
					level_max_transfer = '{$level_max_transfer}'
			");			
			
			/*return ajax*/
			return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_success_saved']) );	
		break;
		
		case 'setting_ranks':	
			$setting_userpoints_enable_pointrank = ( isset($_POST['enable_pointrank']) ? $_POST['enable_pointrank'] : ( isset($_GET['enable_pointrank']) ? $_GET['enable_pointrank'] : 0 ) );		
			$sqlConnect->query("UPDATE plugins_system SET setting_userpoints_enable_pointrank =  '{$setting_userpoints_enable_pointrank}'");
			$point_rank_points = $_POST['point_rank_points'];
			$point_rank_text = $_POST['point_rank_text'];			

			/* get new id */  
			$sqlConnect->query("TRUNCATE userpointranks");			
			foreach($point_rank_points as $key => $value ) {
			    if($value !== '' & is_numeric($value)) {
					$sqlConnect->query("INSERT INTO userpointranks( amount, text ) VALUES ( '{$value}', '{$point_rank_text[$key]}' )");
				} 
			}

			/*return ajax*/
			return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_success_saved']) );
			break;
		
		case 'admin_userpoints_assign':
			
			   $actions = ( isset($_POST['actions']) ? $_POST['actions'] : ( isset($_GET['actions']) ? $_GET['actions'] : array() ) );
			   $actionsmax = ( isset($_POST['actionsmax']) ? $_POST['actionsmax'] : ( isset($_GET['actionsmax']) ? $_GET['actionsmax'] : array() ) );
			   $actionsrollover = ( isset($_POST['actionsrollover']) ? $_POST['actionsrollover'] : ( isset($_GET['actionsrollover']) ? $_GET['actionsrollover'] : array() ) );
			   $actionsname = ( isset($_POST['actionsname']) ? $_POST['actionsname'] : ( isset($_GET['actionsname']) ? $_GET['actionsname'] : array() ) );

				foreach($actions as $k => $v){
					// days -> seconds
					$rollover_period = intval($actionsrollover[$k]) * 86400;   
					// new, previously unknown actiontype
					if( intval($k) == 0 ) {
					   $sqlConnect->query( "INSERT INTO actionpoints (
												action_type,
												action_name,
												action_points,
												action_pointsmax,
												action_rolloverperiod)
												VALUES (
												'{$k}',
												'{$actionsname[$k]}',
												".intval($v).",
												".intval($actionsmax[$k]).",
												'{$rollover_period}'
												)" );
					} else {
	  					   // unknown item, changing name
					   if(isset($actionsname[$k])) {
						 $sqlConnect->query( "UPDATE actionpoints SET
									  action_name = '{$actionsname[$k]}',
									  action_points = " . intval($v) . ",
									  action_pointsmax = " . intval($actionsmax[$k]) . ",
									  action_rolloverperiod = $rollover_period
									WHERE action_id = " . intval($k) );
					   } else {
					   $sqlConnect->query( "UPDATE actionpoints SET
									action_points = " . intval($v) . ",
									action_pointsmax = " . intval($actionsmax[$k]) . ",
									action_rolloverperiod = $rollover_period
								  WHERE action_id = " . intval($k) );
					   }
				   }
			  }
		
				/*return ajax*/
				return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_success_saved']) );    
			break;
		
		/*offers and shop*/		
		case 'offer_add_new':
				   $item_type = ( isset($_POST['item_type']) ? $_POST['item_type'] : ( isset($_GET['item_type']) ? $_GET['item_type'] : 0 ) );
				   $offer_enabled = ( isset($_POST['offer_enabled']) ? $_POST['offer_enabled'] : ( isset($_GET['offer_enabled']) ? $_GET['offer_enabled'] : 0 ) );
				   $offer_title = ( isset($_POST['offer_title']) ? $_POST['offer_title'] : ( isset($_GET['offer_title']) ? $_GET['offer_title'] : '' ) );
				   $offer_cost = ( isset($_POST['offer_cost']) ? $_POST['offer_cost'] : ( isset($_GET['offer_cost']) ? $_GET['offer_cost'] : 0 ) );
				   $offer_cant = ( isset($_POST['offer_cant']) ? $_POST['offer_cant'] : ( isset($_GET['offer_cant']) ? $_GET['offer_cant'] : 0 ) );
				   $offer_transact_state = ( isset($_POST['offer_transact_state']) ? $_POST['offer_transact_state'] : ( isset($_GET['offer_transact_state']) ? $_GET['offer_transact_state'] : 0 ) );
				   $appear_in_transactions = ( isset($_POST['appear_in_transactions']) ? $_POST['appear_in_transactions'] : ( isset($_GET['appear_in_transactions']) ? $_GET['appear_in_transactions'] : 0 ) );
				   $redirect_url = ( isset($_POST['redirect_url']) ? $_POST['redirect_url'] : ( isset($_GET['redirect_url']) ? $_GET['redirect_url'] : '' ) );
				   $offer_desc = $_POST['offer_desc'];
				   $offer_tags = $_POST['offer_tags'];
				   $offer_desc_encoded = $offer_desc;
				   $is_error = 0;
				   
				   if(empty($offer_title)){ 
					   $is_error = 1;
					   return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_add_a_title']) );
				   }
				   if(empty($offer_desc)) { 
					   $is_error = 1; 
					   return_json( array('result'=> 0, 'error' => true, 'message' => 'Add a description') );
				   }
				   if(empty($redirect_url)){
					   $is_error = 1;
					   return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_please_enter_url']) );
				   }
	
				 if($is_error == 0) {
				
				   $metadata = array( 'url'  => $redirect_url, 't'  => intval($appear_in_transactions) );  
				   $metadata = serialize( $metadata );
	
				   if($item_id == 0) {
				   $sqlConnect->query("INSERT INTO userpointearner(
											   type,
											   name,
											   title,
											   body,
											   date,
											   cost,
											   cant,
											   metadata,
											   enabled,
											   tags,
											   redirect_on_buy,
											   transact_state,
											   appear_in_transactions
											   )
											   VALUES(
											   '400',
											   'generic',
											   '{$offer_title}',
											   '{$offer_desc_encoded}',
											   '".time()."',
											   '{$offer_cost}',
											   '{$offer_cant}',
											   '{$metadata}',
											   '{$offer_enabled}',
											   '{$offer_tags}',
											   '{$redirect_url}',
											   '{$offer_transact_state}',
											   '{$appear_in_transactions}'
											)");
					   $item_id = $sqlConnect->insert_id;
				   } else {
						$sqlConnect->query("UPDATE userpointearner SET
								title = '$offer_title',
								body = '$offer_desc_encoded',
								cost = $offer_cost,
								cant = $offer_cant,
								metadata = '$metadata',
								enabled = $offer_enabled,
								tags = '$offer_tags',
								redirect_on_buy =  '$redirect_url',
								transact_state = $offer_transact_state,
								appear_in_transactions = $appear_in_transactions 
								WHERE id = $item_id");
				   }
				   
				   if(!empty($_FILES['image']['name'])){				   		
						// get allowed file size
						$max_allowed_size = $wo['system']['max_photo_size'] * 1024;  
						 
						// valid inputs
						if(!isset($_FILES["image"])) {
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_upload_max_filesize']) ); 
						}
						
						/* if file size is bigger than allowed size */
						if($_FILES["image"]["size"] > $max_allowed_size) { 
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_upload_error_file_is_big']) );
						}
							
						$name = $_FILES['image']['name'];  
						$ext = pathinfo($name, PATHINFO_EXTENSION);
						
						//create directory 
						if(!file_exists('upload/points')) { mkdir('upload/points', 0777, true); } 
						if(!file_exists('upload/points/earn')) { mkdir('upload/points/earn', 0777, true); } 
						
						$allowed           = 'jpg,png,jpeg,gif';
						$new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
						$extension_allowed = explode(',', $allowed);
						$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
						
						//extension allowed
						if(!in_array($file_extension, $extension_allowed)) { return false; }				
					   
						$path_tmp = 'upload/points/earn/'.$item_id.'.'.$ext;
					
						/* check if the file uploaded successfully */
						if(@move_uploaded_file($_FILES['image']['tmp_name'], $path_tmp)) {  
							$last_file = $path_tmp;
							$explode2  = @end(explode('.', $path_tmp));
							$explode3  = @explode('.', $path_tmp);
							/* save the new image */
							@Wo_CompressImage($path_tmp, $last_file, 20);
						} else {
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_upload_error_can_not']) );
						}
					   
					   /*upload image*/
					   $sqlConnect->query("UPDATE userpointearner SET photo='{$item_id}', ext='{$ext}' WHERE id='".$item_id."'");
				   }
				 }  
				 
				   /*return ajax*/
				   return_json( array('callback' => 'window.location = "'.$wo['config']['site_url'].'/index.php?link1=admin-plugins&view=points_offerts"') );
			 break;
			 
		case 'offer_edit':
			 break;
		
		case 'offer_delete':
			// valid inputs
			if(!isset($item_id) || !is_numeric($item_id)) {
				return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This item_id no is a number"));
			}			
			$upearner = new upearner( $item_id, false );
			$upearner->delete();
		
			/* return */
			return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_info_been_updated']) );
			break;
		
		case 'offer_enable':
			  // valid inputs
			  if(!isset($item_id) || !is_numeric($item_id)) {
				  return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This item_id no is a number"));
			  }	
			  $enable = soft::getpost('enable');
			  $upearner = new upearner( $item_id, false );
			  $upearner->enable($enable);
			
			  /* return */
			  return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_info_been_updated']) ); 
			break;
		
		/*shop*/
		case 'shop_add_new':
				   $item_type = ( isset($_POST['item_type']) ? $_POST['item_type'] : ( isset($_GET['item_type']) ? $_GET['item_type'] : 0 ) );
				   $offer_enabled = ( isset($_POST['offer_enabled']) ? $_POST['offer_enabled'] : ( isset($_GET['offer_enabled']) ? $_GET['offer_enabled'] : 0 ) );
				   $offer_title = ( isset($_POST['offer_title']) ? $_POST['offer_title'] : ( isset($_GET['offer_title']) ? $_GET['offer_title'] : '' ) );
				   $offer_cost = ( isset($_POST['offer_cost']) ? $_POST['offer_cost'] : ( isset($_GET['offer_cost']) ? $_GET['offer_cost'] : 0 ) );
				   $offer_cant = ( isset($_POST['offer_cant']) ? $_POST['offer_cant'] : ( isset($_GET['offer_cant']) ? $_GET['offer_cant'] : 0 ) );
				   $offer_transact_state = ( isset($_POST['offer_transact_state']) ? $_POST['offer_transact_state'] : ( isset($_GET['offer_transact_state']) ? $_GET['offer_transact_state'] : 0 ) );
				   $appear_in_transactions = ( isset($_POST['appear_in_transactions']) ? $_POST['appear_in_transactions'] : ( isset($_GET['appear_in_transactions']) ? $_GET['appear_in_transactions'] : 0 ) );
				   $redirect_url = ( isset($_POST['redirect_url']) ? $_POST['redirect_url'] : ( isset($_GET['redirect_url']) ? $_GET['redirect_url'] : '' ) );
				   $offer_desc = $_POST['offer_desc'];
				   $offer_tags = $_POST['offer_tags'];
				   $offer_desc_encoded = $offer_desc;
				   $is_error = 0;
				   
				   if(empty($offer_title)) { 
					   $is_error = 1; 
					   return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_please_enter_title']) );
				   }
				   if(empty($offer_desc)) { 
					   $is_error = 1; 
					   return_json( array('result'=> 0, 'error' => true, 'message' => 'Add a description') );
				   }
	
				 if($is_error == 0) {
											
				   $metadata = array( 'url'  => $redirect_url, 't'  => intval($appear_in_transactions) );  
				   $metadata = serialize( $metadata );
	
				   if($item_id == 0) {
					   $sqlConnect->query("INSERT INTO userpointspender(
											   type,
											   name,
											   title,
											   body,
											   date,
											   cost,
											   cant,
											   metadata,
											   enabled,
											   tags,
											   redirect_on_buy,
											   transact_state,
											   appear_in_transactions
											   )
											   VALUES(
											   '400',
											   'generic',
											   '{$offer_title}',
											   '{$offer_desc_encoded}',
											   '".time()."',
											   '{$offer_cost}',
											   '{$offer_cant}',
											   '{$metadata}',
											   '{$offer_enabled}',
											   '{$offer_tags}',
											   '{$redirect_url}',
											   '{$offer_transact_state}',
											   '{$appear_in_transactions}'
											)");
					   $item_id = $sqlConnect->insert_id;
				   } else {
					$sqlConnect->query("UPDATE userpointspender SET
								title = '$offer_title',
								body = '$offer_desc_encoded',
								cost = $offer_cost,
								cant = $offer_cant,
								metadata = '$metadata',
								enabled = $offer_enabled,
								tags = '$offer_tags',
								redirect_on_buy =  '$redirect_url',
								transact_state = $offer_transact_state,
								appear_in_transactions = $appear_in_transactions
								WHERE id = $item_id");
				   }

				   if(!empty($_FILES['image']['name'])){				   				   
						// get allowed file size
						$max_allowed_size = $wo['system']['max_photo_size'] * 1024;  
						 
						// valid inputs
						if(!isset($_FILES["image"])) {
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_upload_max_filesize']) ); 
						}
						
						/* if file size is bigger than allowed size */
						if($_FILES["image"]["size"] > $max_allowed_size) { 
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_upload_error_file_is_big']) );
						}
							
						$name = $_FILES['image']['name'];  
						$ext = pathinfo($name, PATHINFO_EXTENSION);
						
						//create directory 
						if(!file_exists('upload/points')) { mkdir('upload/points', 0777, true); } 
						if(!file_exists('upload/points/spend')) { mkdir('upload/points/spend', 0777, true); } 
						
						$allowed           = 'jpg,png,jpeg,gif';
						$new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
						$extension_allowed = explode(',', $allowed);
						$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
						
						//extension allowed
						if(!in_array($file_extension, $extension_allowed)) { return false; }				
					   
						$path_tmp = 'upload/points/spend/'.$item_id.'.'.$ext;
					
						/* check if the file uploaded successfully */
						if(@move_uploaded_file($_FILES['image']['tmp_name'], $path_tmp)) {  
							$last_file = $path_tmp;
							$explode2  = @end(explode('.', $path_tmp));
							$explode3  = @explode('.', $path_tmp);
							/* save the new image */
							@Wo_CompressImage($path_tmp, $last_file, 20);
						} else {
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_upload_error_can_not']) );
						}
						
						/*upload image*/
						$sqlConnect->query("UPDATE userpointspender SET photo='{$item_id}', ext='{$ext}' WHERE id='".$item_id."'");
				   }
				 }  
				 
				   /*return ajax*/
				   return_json( array('callback' => 'window.location = "'.$wo['config']['site_url'].'/index.php?link1=admin-plugins&view=points_shop"') );
			 break;
			 
		case 'shop_edit':
			 break;
			 
		case 'shop_delete':
			// valid inputs
			if(!isset($item_id) || !is_numeric($item_id)) {
				return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This item_id no is a number"));
			}			
			$upspender = new upspender( $item_id, false );
			$upspender->delete();
			/* return */
			return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_item_deleted']) );
			break;
			
		case 'shop_delete_user':
			// valid inputs
			if(!isset($item_id) || !is_numeric($item_id)) { 
				return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This item_id no is a number"));
			}			
			$sqlConnect->query(sprintf("DELETE FROM userpointspender_users WHERE id='{$item_id}' LIMIT 1"));
			/* return */
			return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_item_deleted']) );
			break;
					
		case 'shop_enable':
			  // valid inputs
			  if(!isset($item_id) || !is_numeric($item_id)) {
				  return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This item_id no is a number"));
			  }	
			  $enable = soft::getpost('enable');
			  $upspender = new upspender( $item_id, false );
			  $upspender->enable($enable);
			  /* return */
			  return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_info_been_updated']) ); 
			break;
			
		 case 'dogivepoints':
			  $sendtotype = ( isset($_POST['sendtotype']) ? $_POST['sendtotype'] : ( isset($_GET['sendtotype']) ? $_GET['sendtotype'] : 0 ) );
			  $username = ( isset($_POST['username']) ? $_POST['username'] : ( isset($_GET['username']) ? $_GET['username'] : 0 ) );
			  $user_id = ( isset($_POST['user_id']) ? $_POST['user_id'] : ( isset($_GET['user_id']) ? $_GET['username'] : 0 ) );
			  $send_message = ( isset($_POST['send_message']) ? $_POST['send_message'] : ( isset($_GET['send_message']) ? $_GET['send_message'] : 0 ) );
			  $subject = ( isset($_POST['subject']) ? $_POST['subject'] : ( isset($_GET['subject']) ? $_GET['subject'] : 0 ) );
			  $message = ( isset($_POST['message']) ? $_POST['message'] : ( isset($_GET['message']) ? $_GET['message'] : 0 ) );
			  $set_points = ( isset($_POST['set_points']) ? $_POST['set_points'] : ( isset($_GET['set_points']) ? $_GET['set_points'] : 0 ) );
			  $points = ( isset($_POST['points']) ? $_POST['points'] : ( isset($_GET['points']) ? $_GET['points'] : 0 ) );
			  $from_user_id = ( isset($_POST['from_user_id']) ? $_POST['from_user_id'] : ( isset($_GET['from_user_id']) ? $_GET['from_user_id'] : 0 ) );
			  $from_username = ( isset($_POST['from_username']) ? $_POST['from_username'] : ( isset($_GET['from_username']) ? $_GET['from_username'] : 0 ) );
			  $now =  time();
	
				if(!is_numeric($points) || $points == 0) {
					return_json( array('error' => true, 'message' => 'Please add a mount') );
				}
			
				/* info of admin send */
				if(is_numeric($from_user_id) && $from_user_id!= 0 && $from_username!= 0 &&  $from_user_id != $wo['user']['user_id']) {			  
					$getUser = $sqlConnect->query("SELECT user_id, username, first_name, last_name, avatar, email, gender FROM Wo_Users
					 WHERE user_id='{$from_user_id}' AND username='{$from_username}' LIMIT 1");
					$admin_user = $getUser->fetch_assoc();   
				} else {
					$getUser = $sqlConnect->query("SELECT user_id, username, first_name, last_name, avatar, email, gender FROM Wo_Users 
					WHERE user_id='{$wo['user']['user_id']}' LIMIT 1");
					$admin_user = $getUser->fetch_assoc();  
				}
			
				switch($sendtotype) {
	
					// All users ..
					// would be great to just inc all pointscoint, but there are some that have no rows
					case 0:
					ignore_user_abort( true );
					set_time_limit( 0 );
		
					$rows = $sqlConnect->query(" SELECT user_id, username, first_name, last_name, avatar, email, gender FROM Wo_Users ");
					while($row = $rows->fetch_assoc()) {
		
					/* default static point */
					if($set_points){
						userpoints_set( $row['user_id'], $points );
						
						if($send_message && ($row['user_id'] != $admin_user['user_id'])){
							send_email($row['email'], $subject, $message );  
						}												
					} else {
						userpoints_add( $row['user_id'], $points );
			
						$sqlConnect->query("INSERT INTO uptransactions (
						user_id,
						type,
						cat, 
						state,
						text,
						date,
						amount )
						VALUES (
						'{$row['user_id']}',
						'givepoints',
						'1',
						'0',
						'Admin points',
						'{$now}',
						'{$points}' )
						");								
						if($send_message && ($row['user_id'] != $admin_user['user_id'])){
							send_email($row['email'], $subject, $message );  
						}						
						Register_Notification(array(
						'recipient_id' => $row['user_id'],
						'type' => 'givepoints',
						'url' => 'index.php?link1=user_points',
						'email_subject' => $subject,
						'email_text' => $message
						));
					}
				}
				break;
	 
				// Specific user
				case 1:
				
				if($user_id == 0){
					return_json( array('error' => true, 'message' => $username) );
				}
				/*get info of user */  
				$getUser = $sqlConnect->query("SELECT user_id, username, first_name, last_name, avatar, email, gender 
				FROM Wo_Users WHERE user_id='{$user_id}' LIMIT 1");
				if( $getUser->num_rows == 0) {
					return_json( array('error' => true, 'message' => $wo['lang']['plugin_point_that_user_doesnt_exist']) );
				} else {
					$happy_user = $getUser->fetch_assoc();
					if($set_points){
						userpoints_set( $happy_user['user_id'], $points );
					} else{
						userpoints_add( $happy_user['user_id'], $points );
			
						$sqlConnect->query("INSERT INTO uptransactions (
						user_id,
						type,
						cat, 
						state,
						text,
						date,
						amount )
						VALUES (
						'{$happy_user['user_id']}',
						'givepoints',
						'1',
						'0',
						'Admin points',
						'{$now}',
						'{$points}' )");
						
						if($send_message && ($happy_user['user_id'] != $admin_user['user_id'])) {
							send_email( $happy_user['email'], $subject, $message );
						}
						Register_Notification(array(
						'recipient_id' => $happy_user['user_id'],
						'type' => 'givepoints',
						'url' => 'index.php?link1=user_points',
						'email_subject' => $subject,
						'email_text' => $message
						));
					}
				}
				break;		
			}	
			return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_point_was_sending']) ); 
	
			 break;
			 
		case 'shop_user_edit':
		
			$user_id = ( isset($_POST['user_id']) ? $_POST['user_id'] : ( isset($_GET['user_id']) ? $_GET['username'] : 0 ) );
			$item_id = ( isset($_POST['item_id']) ? $_POST['item_id'] : ( isset($_GET['item_id']) ? $_GET['item_id'] : 0 ) );
			$news_invites = ( isset($_POST['news_invites']) ? $_POST['news_invites'] : ( isset($_GET['news_invites']) ? $_GET['news_invites'] : 0 ) );
			$transaction_id = ( isset($_POST['transaction_id']) ? $_POST['transaction_id'] : ( isset($_GET['transaction_id']) ? $_GET['transaction_id'] : 0 ) );
			$status = ( isset($_POST['status']) ? $_POST['status'] : ( isset($_GET['status']) ? $_GET['status'] : 0 ) );	
			
			//completed
			if($status == 0){
			
			//add invitations
			if($news_invites != 0){
			   $sqlConnect->query("UPDATE Wo_Users SET user_invitesleft = user_invitesleft + ".$news_invites." WHERE user_id='{$user_id}' LIMIT 1");
			}
			
			//completed
			   $sqlConnect->query("UPDATE userpointspender_users SET status = ".$status." WHERE user_id='{$user_id}' AND transaction_id='{$transaction_id}'  LIMIT 1");
			   $sqlConnect->query("UPDATE uptransactions SET state = ".$status." WHERE user_id='{$user_id}' AND id='{$transaction_id}' LIMIT 1");
			
			//send notify
			$get_point_user = $sqlConnect->query("SELECT user_id, username, first_name, last_name, email FROM Wo_Users WHERE user_id='{$user_id}' LIMIT 1");
			$rows = $get_point_user->fetch_assoc();
			
			/* prepare email */
			$subject = $wo['lang']['plugin_point_you_iten_shop_was_completed']." ".$wo['config']['siteTitle'];
			$body  = $rows['first_name'].", congratulations";
			$body .= "\r\n\r\n".$wo['lang']['plugin_point_the_process_was_completed'];
			$body .= "\r\n\r\n".$wo['config']['site_url']."/index.php?link1=user_points";
			$email =  $rows['email'];
			  
			/* send using mail() */
			send_email($email, $subject, $body);
			
			  return_json( array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_item_was_updated']) );
			} else {
			  return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_status_continue_pending']) );
			}
			break;			
								
		default: 
			return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This task no exist"));
	    break;
	}
}



if($f == 'points'){

    $task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : '' ) );
	$val = ( isset($_POST['val']) ? $_POST['val'] : ( isset($_GET['val']) ? $_GET['val'] : array() ) );
	$item_id = ( isset($_POST['item_id']) ? $_POST['item_id'] : ( isset($_GET['item_id']) ? $_GET['item_id'] : '0' ) );

	if($s == 'invite'){
			$invite_id = ( isset($_POST['invite_id']) ? $_POST['invite_id'] : ( isset($_GET['invite_id']) ? $_GET['invite_id'] : '0' ) );
					
			switch ($task) {
				case 'doinvite': 				
					$invite_emails = Wo_Secure($_POST['invite_emails']);
					$invite_message = Wo_Secure($_POST['invite_message']);
					$invites_left = $wo['user']['user_invitesleft'];
					
					if($invite_emails == "") { 
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_please_enter_email_for_invitation']) );
					}
					
					// valid inputs
					if($invites_left <= 0){ 
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_you_have'].' <strong>'.$invites_left.'</strong> '.$wo['lang']['plugin_point_invitations_available']) );
					}
					if(trim($invite_emails) == "") { 
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_please_enter_email_for_invitation']) );
					}
					
					$invite_emails = implode(",", array_slice(explode(",", $invite_emails), 0, 10));			
					
					$invite_emails_array = explode(",", $invite_emails);
				
					for($e=0; $e<count($invite_emails_array); $e++)
					{
						$email = trim($invite_emails_array[$e]);
					
						//self
						if($email == $wo['user']['email']){ return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_no_autoinvitation']) ); }
						$resource = $sqlConnect->query("SELECT email FROM Wo_Users WHERE email ='{$email}' LIMIT 1");
						//exist invitation	  
						$resource = $sqlConnect->query("SELECT invite_email FROM invites WHERE invite_email ='{$email}'");
						if($resource->num_rows > 0){ 
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_people_invited_before']) );
						}					
						//exist in network
						if($resource->num_rows > 0){ 
							return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_email_exists']) );
						}
						
						if($email != "" && $invites_left > 0){
							// CREATE CODE, INSERT INTO DATABASE, AND SEND EMAIL
							$invite_code = rand(111111,999999);
							
							$sqlConnect->query("INSERT INTO invites (invite_user_id, invite_date, invite_email, invite_code) VALUES ('{$wo['user']['user_id']}', '".time()."', '{$email}', '{$invite_code}') ON DUPLICATE KEY UPDATE invite_date = '".time()."', invite_code = '{$invite_code}' ");
							$new_invitation = $sqlConnect->insert_id;
							
							/*send email*/
							$subject = $wo['lang']['plugin_point_you_have_an_invitation'];
							
							$body  = "Hello, You have been invited by ".$wo['user']['first_name']." to join our social network. To join, please follow the link below:";
							$body .= $invite_message;
							$body .= "\r\n\r\n".$wo['config']['site_url']."/register?signup_invite=".$invite_code;
							$body .= "\r\n\r\n".$wo['lang']['plugin_point_best_regards_admin'];
							
							$invites_left--;
							$sqlConnect->query("UPDATE Wo_Users SET user_invitesleft='{$invites_left}' WHERE user_id='{$wo['user']['user_id']}' LIMIT 1");
							$wo['user']['user_invitesleft'] = $invites_left;
							
							/* send using mail() */
							if(!send_email($email, $subject, $body)) { 
								return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_cant_send_email']) );
							}	
							
							/* return */
							return_json( array('result'=> 1, 'success' => true, 'total_invites' => $invites_left, 'message' => $wo['lang']['plugin_point_done_invitation_was_send']) );
						}
					}
								
				break;	
				
				case 're_send_invite':
					$invite_id = Wo_Secure($invite_id);
					// valid inputs
					if(trim($invite_id) == 0) {
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_error_send_invitation']) );
					}
					//get info
					$resource = $sqlConnect->query("SELECT * FROM invites WHERE invite_id ='{$invite_id}' LIMIT 1");					
					if($resource->num_rows == 0){ 
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_invitation_no_exists']) );
					}	       
					$inv_info = $resource->fetch_assoc();
					
					$email = $inv_info['invite_email'];
					$invite_code = $inv_info['invite_code'];
					
					//exist user
					$resource = $sqlConnect->query("SELECT email FROM Wo_Users WHERE email ='{$email}' LIMIT 1");
					if($resource->num_rows > 0){ 
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_no_use_baucher']) );
					}
					
					/*send email*/
					$subject = 'You have received an invitation to join our social network.';	        
					$body  = "Hello, You have been invited by ".$wo['user']['first_name']." to join our social network. To join, please follow the link below:";
					$body .= "\r\n\r\n".$wo['config']['site_url']."/register?signup_invite=".$invite_code;
					$body .= "\r\n\r\n".$wo['lang']['plugin_point_best_regards_admin'];
					
					/* send using mail() */
					if(!send_email($email, $subject, $body)) { 
						return_json(array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_cant_send_email']));
					}	
					
					/* return */
					return_json(array('result'=> 1, 'success' => true, 'message' => $wo['lang']['plugin_point_done_invitation_was_send']));        
				break;
				
				case 'delete_invite':
					
					$invite_id = Wo_Secure($invite_id);
					// valid inputs
					if(trim($invite_id) == 0) {
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_invite_no_valid']) );
					}
					//get info
					$resource = $sqlConnect->query("SELECT * FROM invites WHERE invite_id ='{$invite_id}' AND invite_user_id = '{$wo['user']['user_id']}' LIMIT 1");
					if($resource->num_rows == 0){ 
						return_json( array('result'=> 0, 'error' => true, 'message' => $wo['lang']['plugin_point_invitation_no_exists']) );
					}	
					//delelete      			
					$sqlConnect->query("DELETE FROM invites WHERE invite_id ='{$invite_id}' LIMIT 1");
					//update
					$sqlConnect->query("UPDATE Wo_Users SET user_invitesleft=user_invitesleft+1 WHERE user_id='{$wo['user']['user_id']}' LIMIT 1");
					$invites_left = ($wo['user']['user_invitesleft']+1);
					$wo['user']['user_invitesleft'] = $invites_left;							
					/* return */
					return_json(array('result'=> 1, 'success' => true, 'total_invites' => $invites_left, 'message' => 'this invitations was deleted'));        
				break;
						
				default: 
				return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This task no exist"));
				break;
			}
	}
	
	
	if($s == 'offers'){
		switch ($task) {
			case 'dobuy':
					 $get_user_offer =  $sqlConnect->query("SELECT * FROM userpointearner_users WHERE user_id = '{$wo['user']['user_id']}' AND offer = '{$item_id}' LIMIT 1");
					 if($get_user_offer->num_rows > 0){
						/* return */
						return_json( array('error' => true, 'message' => $wo['lang']['plugin_point_participate_before']) );
					 }					 
					 $upearner = new upearner( $item_id );			     
					 $transaction = $upearner->transact();
					 
					 if($transaction['result'] == 0) {
						/* return */
						return_json( array('error' => true, 'message' => $wo['lang']['plugin_point_try_more_late']) );
					 } else {
						/* return */
						return_json( array('callback' => 'window.location = "'.$upearner->upearner_info['redirect_on_buy'].'"') );
					 }					 
				break;
				
			default: 
			return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This task no exist"));
			break;
		}
	}
	
	
	
	if($s == 'shop'){
		switch ($task) {
			case 'dobuy':	 
					 $upspender = new upspender( $item_id );
									 
					 $transaction = $upspender->transact($wo);
				  
					 if($transaction['result'] == 0) {
					   /* return */
					   return_json( array('error' => true, 'message' => $transaction['message']) );
					 } else {
					   /* return */
					   if( $upspender->upspender_info['transact_state'] == 0) {
						   return_json( array('success' => true, 'message' => $wo['lang']['plugin_point_you_purchase']) );
					   }else{
						   return_json( array('success' => true, 'message' => $wo['lang']['plugin_point_notification_to_admin']) );
					   }
					 }				 
				break;
				
			default: 
				return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This task no exist"));
			break;
		}
	}
	
	if($s === 'transfer'){
		switch ($task) {
			case 'sendpoints':
				$points_recipient_id = ( isset($_POST['recipient_id']) ? $_POST['recipient_id'] : ( isset($_GET['recipient_id']) ? $_GET['recipient_id'] : 0 ) );
				$points_amount = ( isset($_POST['points_amount']) ? $_POST['points_amount'] : ( isset($_GET['points_amount']) ? $_GET['points_amount'] : 0 ) );
				$points_amount = intval($points_amount);
				
				$result = userpoints_transfer_points( $wo['user']['user_id'], $points_recipient_id, $points_amount );
				
				$response = array();
				
				if($result['is_error'] == 1) {
					$response['error'] = true;
					$response['message'] = $result['message'];
				}
				else {
					$response['success'] = true;
					$response['message'] = $result['message'];
					$response['balance'] = get_points( $wo['user']['user_id'] );
				}
				
				//$response['msg'] = soft::get_language_text( $result['message'] );
				//$response['message'] = $result['message'];

				return_json($response);
				
			break;
				
			default: 
				return_json(array('result'=>'0', 'error'=>true, 'title'=>'Error', 'btn'=>'', 'message' => "This task no exist"));
			break;
		}
	}
}
?>