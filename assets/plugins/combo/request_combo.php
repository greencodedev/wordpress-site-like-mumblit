<?php
/* request
 * @autor : Pp Galvan - LdrMx
 */
 
if($f == 'admin_combo'){
	$is_admin = Wo_IsAdmin();
	$is_moderoter = Wo_IsModerator();
	if ($is_admin === false && $is_moderoter === false) {
		  /* return ajax */		
		  return_json(array('status' => 200, 'message' => "You don't have the right permission to access this"));
	}

	include "assets/plugins/combo/class_combo.php";
	$adm_combo = new combo();
	$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : '' ) );
	$reaction_key = ( isset($_POST['reaction_key']) ? $_POST['reaction_key'] : ( isset($_GET['reaction_key']) ? $_GET['reaction_key'] : '' ) );
	$reaction_id = ( isset($_POST['reaction_id']) ? $_POST['reaction_id'] : ( isset($_GET['reaction_id']) ? $_GET['reaction_id'] : 0 ) );
	$reaction_status = ( isset($_POST['reaction_status']) ? $_POST['reaction_status'] : ( isset($_GET['reaction_status']) ? $_GET['reaction_status'] : 1 ) );
	$reaction_colour = ( isset($_POST['reaction_colour']) ? $_POST['reaction_colour'] : ( isset($_GET['reaction_colour']) ? $_GET['reaction_colour'] : 'yellow-opt' ) );
	$text = ( isset($_POST['text']) ? $_POST['text'] : ( isset($_GET['text']) ? $_GET['text'] : '') );
	$count_id = ( isset($_POST['count_id']) ? $_POST['count_id'] : ( isset($_GET['count_id']) ? $_GET['count_id'] : 0 ) ); 
	$frame = ( isset($_POST['frame']) ? $_POST['frame'] : ( isset($_GET['frame']) ? $_GET['frame'] : 0 ) );  

switch ($task) {						
    case 'add_file':
	        if (empty($reaction_key)){ 
				/* return ajax*/
				$data = array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Add a key");					
      		    return_json($data);
			}   
            if (empty($text)){ 
				/* return ajax*/
				$data = array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Add a text");					
      		    return_json($data);
			}  
			if(!isset($_FILES)){
			    /* return ajax*/
				$data = array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Select a image");					
      		    return_json($data);	
			}     
			$upload_result = array();
			$upload_result = $adm_combo->upload($reaction_key, $text, $reaction_status, $reaction_colour);

			//return_json( array('callback' => 'window.location.reload();') );
			if($upload_result['result'] == 1){ 
				if($reaction_status == 1){ $reac_sel_1 = 'selected=""'; }else{ $reac_sel_1 = ''; }
				if($reaction_status == 0){ $reac_sel_0 = 'selected=""'; }else{ $reac_sel_0 = ''; }
				if($reaction_colour == 'opt-active'){ $reac_selc_1 = 'selected=""'; }else{ $reac_selc_1 = ''; }
				if($reaction_colour == 'red-opt'){ $reac_selc_2 = 'selected=""'; }else{ $reac_selc_2 = ''; }
				if($reaction_colour == 'orange-opt'){ $reac_selc_3 = 'selected=""'; }else{ $reac_selc_3 = ''; }
				if($reaction_colour == 'yellow-opt'){ $reac_selc_4 = 'selected=""'; }else{ $reac_selc_4 = ''; }
				if($reaction_colour == 'green-opt'){ $reac_selc_5 = 'selected=""'; }else{ $reac_selc_5 = ''; }
				$img = '<div class="col-sm-6 reaction" style="padding:2px;" data-id="'.$upload_result['reaction_id'].'"><div class="panel panel-default clearfix"><div class="panel-body"><div class="pull-right"><button type="button" class="close js_reaction_remover" title="Remove"><span>×</span></button></div><div class="pull-left image"><img src="'.$wo['config']['site_url'].'/upload/reaction/reaction_'.$reaction_key.'.'.$upload_result['ext'].'" title="'.$text.'" class="img-rounded avatar"><div><label for="new_src_'.$upload_result['reaction_id'].'" class="btn label_new_src"><i class="fa fa-camera"></i></label><input class="new_src" style="display:none;" name="file" type="file" accept="image/*" id="new_src_'.$upload_result['reaction_id'].'"></div></div><div class="pull-left form-group ml10"><div><b>reaction_key</b>: <span class="edit_reaction_key">'.$reaction_key.'</span> <div class="edit_key_input"></div></div><div class="mt5"><b>Text</b>: '.$text.' <a href="'.$wo['config']['site_url'].'/admincp/edit_key&id=reaction_'.$upload_result['reaction_id'].'"><i class="fa fa-pencil"></i></a></div><div class="mt10"><select class="form-control" name="reaction_status"><option '.$reac_sel_1.' value="1">'.$wo['lang']['plugin_enabled'].'</option><option '.$reac_sel_0.' value="0">'.$wo['lang']['plugin_disabled'].'</option></select></div><div class="mt10"><select class="form-control" name="reaction_colour"><option '.$reac_selc_1.' value="opt-active">blue</option><option '.$reac_selc_2.' value="red-opt">red</option><option '.$reac_selc_3.' value="orange-opt">orange</option><option '.$reac_selc_4.' value="yellow-opt">yellow</option><option '.$reac_selc_5.' value="green-opt">green</option>
</select></div></div></div></div></div>';
				/* return */
				$data = array('result' => 1, 'html' => $img, 'id'=> $count_id, 'status' => '200', 'success' => true, 'message' => "Done, was added");
			} else { 
				/* return */
				$data = array( 'result' => 0, 'id'=> $count_id, 'status' => '200', 'success' => true, 'message' => "Error no was added");
			}
		/* return ajax */		
		return_json($data);	
		break;						
		
	case 'reaction_status':
					// UPDATE IF NO ERROR
					$sqlConnect->query("UPDATE reactions SET reaction_status='{$reaction_status}' WHERE reaction_id='{$reaction_id}' LIMIT 1"); 
					$db_update =  $sqlConnect->affected_rows;					
							
					if($db_update == 1){
					   return array('result' => 1, 'message' => "Successfully reaction was created", 'results'=> $db_update, 'success' => true, 'reaction_id' => $reaction_id);
					} else {
					   return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
					}
		break;
		
	case 'reaction_colour':	        
			$resource = $sqlConnect->query('SELECT * FROM reactions WHERE reaction_id = '.$reaction_id.' LIMIT 1');
			$file = $resource->fetch_assoc();	
		
			// create php file
			$php_string = '<span class="text-link story-like-btn '.$reaction_colour.' opt" onclick="set_reaction($(this), \''.$file['reaction_key'].'\', <?php echo $post_id; ?>);" title="<?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>">
<img src="<?php echo $wo[\'config\'][\'site_url\']; ?>/upload/reaction/reaction_'.$file['reaction_key'].'.'.$file['reaction_filetype'].'" class="progress-img" width="14px"> <?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>
</span>';
			
			$config_file = 'upload/reaction/reaction-'.$file['reaction_key'].'-button.php';
			$handle = fopen($config_file, 'w');
			
			fwrite($handle, $php_string);
			fclose($handle);
			
					// UPDATE IF NO ERROR
					$sqlConnect->query("UPDATE reactions SET reaction_colour='{$reaction_colour}' WHERE reaction_id='{$reaction_id}' LIMIT 1"); 
					$db_update =  $sqlConnect->affected_rows;					
							
					if($db_update == 1){
					   return array('result' => 1, 'message' => "Successfully reaction was created", 'results'=> $db_update, 'success' => true, 'reaction_id' => $reaction_id);
					} else {
					   return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
					}
		break;
		
		//reaction_key
		case 'reaction_key':	
		    /* return ajax*/	
		    if (empty($reaction_key)){ 				
      		    return_json(array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Add a key"));
			}        
			$resource = $sqlConnect->query('SELECT * FROM reactions WHERE reaction_id = '.$reaction_id.' LIMIT 1');
			$file = $resource->fetch_assoc();
			
			//rename image	
 			rename('upload/reaction/reaction_'.$file['reaction_key'].'.'.$file['reaction_filetype'], 'upload/reaction/reaction_'.$reaction_key.'.'.$file['reaction_filetype']);	
			//delete old
			unlink("upload/reaction/reaction-{$file['reaction_key']}-button.php");
						
			// create php file
			$php_string = '<span class="text-link story-like-btn '.$file['reaction_colour'].' opt" onclick="set_reaction($(this), \''.$reaction_key.'\', <?php echo $post_id; ?>);" title="<?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>">
<img src="<?php echo $wo[\'config\'][\'site_url\']; ?>/upload/reaction/reaction_'.$reaction_key.'.'.$file['reaction_filetype'].'" class="progress-img" width="14px"> <?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>
</span>';
			
			$config_file = 'upload/reaction/reaction-'.$reaction_key.'-button.php';
			$handle = fopen($config_file, 'w');
			
			fwrite($handle, $php_string);
			fclose($handle);
			
			//update likes
			$sqlConnect->query("UPDATE Wo_Likes SET reaction='{$reaction_key}' WHERE reaction='{$file['reaction_key']}'"); 

			//update notifications
		    $sqlConnect->query("UPDATE Wo_Notifications SET app_src='reaction_{$reaction_key}.{$file['reaction_filetype']}' WHERE type='reaction_post' AND app_src='reaction_{$file['reaction_key']}.{$file['reaction_filetype']}'"); 	
			
			//update reaction_key
			$sqlConnect->query("UPDATE reactions SET reaction_key='{$reaction_key}' WHERE reaction_id='{$reaction_id}' LIMIT 1"); 
			$db_update =  $sqlConnect->affected_rows;					
					
			if($db_update == 1){
			   return_json( array('callback' => 'window.location.reload();') );
			   //return array('result' => 1, 'message' => "Successfully reaction was created", 'results'=> $db_update, 'success' => true, 'reaction_id' => $reaction_id);
			} else {
			   return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
			}
				/*	*/
		break;
		//reaction src
		case 'reaction_src':	
			/* return ajax*/			
			if(!isset($_FILES)){					
      		    return_json(array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Select a image"));	
			}     
			
			$name = $_FILES['file']['name'];  
			$ext = pathinfo($name, PATHINFO_EXTENSION);
			
			//create directory 
			if(!file_exists('upload/reaction')) { mkdir('upload/reaction', 0777, true); } 

			$allowed           = 'jpg,png,jpeg,gif';
			$new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
			$extension_allowed = explode(',', $allowed);
			$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
			
			//extension allowed
			if(!in_array($file_extension, $extension_allowed)) { 
				return return_json(array('result' => 0, 'message' => 'Extension no allowed', 'error' => true));
			}
			//get info    
			$resource = $sqlConnect->query('SELECT * FROM reactions WHERE reaction_id = '.$reaction_id.' LIMIT 1');
			$file = $resource->fetch_assoc();
			//delete old	
			unlink("upload/reaction/reaction_{$file['reaction_key']}.{$file['reaction_filetype']}");
	
			$file_dest = 'upload/reaction/reaction_'.$file['reaction_key'].'.'.$ext;		
			//upload
			if(move_uploaded_file($_FILES['file']['tmp_name'], $file_dest)){
				 $last_file = $file_dest;
				  $explode2  = @end(explode('.', $file_dest));
				  $explode3  = @explode('.', $file_dest);							  
			} else {
				return return_json(array('result' => 0, 'message' => 'Can not upload', 'error' => true));			
			}		
			
				// create php file
				$php_string = '<span class="text-link story-like-btn '.$file['reaction_colour'].' opt" onclick="set_reaction($(this), \''.$file['reaction_key'].'\', <?php echo $post_id; ?>);" title="<?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>">
	<img src="<?php echo $wo[\'config\'][\'site_url\']; ?>/upload/reaction/reaction_'.$file['reaction_key'].'.'.$ext.'" class="progress-img" width="14px"> <?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>
	</span>';
				
				$config_file = 'upload/reaction/reaction-'.$file['reaction_key'].'-button.php';
				$handle = fopen($config_file, 'w');
				
				fwrite($handle, $php_string);
				fclose($handle);
				
			if(file_exists($file_dest)){		
				//update notifications
				$sqlConnect->query("UPDATE Wo_Notifications SET app_src='reaction_{$file['reaction_key']}.{$ext}' WHERE type='reaction_post' AND app_src='reaction_{$file['reaction_key']}.{$file['reaction_filetype']}'");
				//update reaction 
				$sqlConnect->query("UPDATE reactions SET reaction_filetype='{$ext}' WHERE reaction_id='{$reaction_id}' LIMIT 1"); 
				$db_update =  $sqlConnect->affected_rows;											
			    $rand_src = rand(1000,9999);
			   return return_json(array('result' => 1, 'message' => "Successfully", 'results'=> $db_update, 'success' => true, 'html' => $wo['config']['site_url'].'/'.$file_dest.'?rand='.$rand_src));
			} else {
			   return return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
			}
		break;
						
	case 'reaction_delete':
			// valid inputs & return ajax 
			if(!isset($reaction_id) || !is_numeric($reaction_id)) {  		
      		   return_json(array('status' => 200, 'message' => "This no is a number"));
			}
			$adm_combo->delete_image($reaction_id);		
	  		/* return ajax */		
      		return_json(array('status' => '200', 'success' => true, 'message' => "Done, was deleted"));	
			break;			
			/*setting*/
	case 'setting':
 	  		//$enable_reaction = ( isset($_POST['enable_reaction']) ? $_POST['enable_reaction'] : ( isset($_GET['enable_reaction']) ? $_GET['enable_reaction'] : '1' ) );
			$enable_tag_action = ( isset($_POST['enable_tag_action']) ? $_POST['enable_tag_action'] : ( isset($_GET['enable_tag_action']) ? $_GET['enable_tag_action'] : '1' ) );
			$enable_welcome = ( isset($_POST['enable_welcome']) ? $_POST['enable_welcome'] : ( isset($_GET['enable_welcome']) ? $_GET['enable_welcome'] : '1' ) );
			$enable_dragdrop = ( isset($_POST['enable_dragdrop']) ? $_POST['enable_dragdrop'] : ( isset($_GET['enable_dragdrop']) ? $_GET['enable_dragdrop'] : '1' ) );
			//$enable_reaction = Wo_Secure($enable_reaction);
			$enable_tag_action = Wo_Secure($enable_tag_action);
			$enable_welcome = Wo_Secure($enable_welcome);
			$enable_dragdrop = Wo_Secure($enable_dragdrop);
			$frame = Wo_Secure($frame);
			
			//combo_enable_reaction = '{$enable_reaction}',
			$sqlConnect->query("UPDATE plugins_system SET 
			combo_enable_frame = '{$frame}',
			combo_enable_tag_action = '{$enable_tag_action}',	
			combo_enable_welcome = '{$enable_welcome}',		
			combo_enable_dragdrop = '{$enable_dragdrop}'		
			WHERE system_id = '1' LIMIT 1");						
			$db_update =  $sqlConnect->affected_rows;
			
			/* return */
			if($db_update == 1){
			   $data = array('result' => 1, 'message' => "Done, Plugin info have been updated", 'results'=> $db_update, 'success' => true);
			} else {
			   $data = array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
			}

			/* return ajax */		
      		return_json($data);	
			break;	  

		default: 
      	    /* return ajax */		
      	    return_json(array('status' => 200, 'message' => "This option no exist"));
		 break;
	}

}	


if($f == 'combo'){
    //value
	$post_id = ( isset($_POST['post_id']) ? $_POST['post_id'] : ( isset($_GET['post_id']) ? $_GET['post_id'] : 0 ) );

	if($s == 'like'){
		$post_id = Wo_Secure($post_id);
		$reaction = ( isset($_POST['reaction']) ? $_POST['reaction'] : ( isset($_GET['reaction']) ? $_GET['reaction'] : 'like' ) );
		
		
		$resource = $sqlConnect->query("SELECT reaction_key FROM reactions WHERE reaction_status='1'");
		$valid['reaction'] = array();
		$valid['reaction'][] = ''; 
		if($resource->num_rows >0){
			while($row = $resource->fetch_assoc()){ $valid['reaction'][] = $row['reaction_key']; }
		}
		//echo json_encode($valid['reaction']);
		// valid inputs
		//$valid['reaction'] = array('', 'like', 'love', 'haha', 'wow', 'sad', 'angry', 'dislike');
		
		/* return */ 
		if(!in_array($reaction, $valid['reaction'])) { return_json(array('result' => 0, 'error' => true, 'message' => "Error this reaction no exist")); } 
		
		/* check post id *//* return */
		if(!isset($post_id) || !is_numeric($post_id) || $post_id == 0) { return_json(array('result' => 0, 'error' => true, 'message' => "This post no exist")); }
			
		// initialize the return array
		$return = array();
		putReaction($reaction, $wo['user']['user_id'], $post_id);
		
		$return = array(
		'status' => 200, 'button_html' => getReactButtonTemplate($reaction, $wo['user']['user_id'], $post_id), 'activity_html' => getReactionActivityTemplate($reaction, $post_id)
		);
		
		$check_like = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE user_id = '{$wo['user']['user_id']}' AND post_id = '{$post_id}'");
		
		$return['liked'] = ($check_like->num_rows > 0)? true: false;
		
		/* plugin points */
		//if(!empty($wo['plugin_list']['plugin_actived']) && in_array('Points', $wo['plugin_list']['plugin_actived']) && !empty($wo['system']['userpoints_enable']) && $wo['system']['userpoints_enable'] == 1 && $return['liked'] == true){ update_points( $wo['user']['user_id'], 'like' ); }	
		
		// return & exit
		return_json($return);	 
	}
	
	//who like
	if($s == 'who_like'){
			$post_id = Wo_Secure($post_id);
			// get shares
			$users = who_likes($post_id);
			$result = array('result' => 1, 'html' => '', 'title' => 'Error', 'content' => 'This post no was liked', 'btn' =>'');	
			if(count($users) > 0 ){ 
				ob_start();
				foreach($users as $_user){ 
					include './themes/'.$wo['config']['theme'].'/layout/plugins/combo/__feed_user_likes.phtml';
				}
				$html = ob_get_contents();
				ob_end_clean(); 
				$result = array('result' => 1, 'html' => '', 'title' => 'Who Likes', 'content' => $html, 'btn' =>'');
			}	
			return_json($result);
	}
	
		//who like
	if($s == 'who_tag'){
			$post_id = Wo_Secure($post_id);
			// get tags
			$resource = $sqlConnect->query("SELECT T.*, U.* FROM posts_tags AS T LEFT JOIN Wo_Users AS U ON T.user_id = U.user_id WHERE T.post_id='{$post_id}'");
		    
			$users = array();
			while($row = $resource->fetch_assoc()){
				if($row['avatar']!= ''){ $row['user_picture'] =  $row['avatar']; } else { $row['user_picture'] = 'upload/photos/d-avatar.jpg'; }
				if($row['first_name']!= ''){ $row['user_fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['user_fullname'] = $row['username']; }
				$users[] = $row;
			}
			$result = array('result' => 1, 'html' => '', 'title' => 'Error', 'content' => 'This post was tagged', 'btn' =>'');	
			if(count($users) > 0 ){ 
				ob_start();
				foreach($users as $_user){ 
					include './themes/'.$wo['config']['theme'].'/layout/plugins/combo/__feed_user.phtml';
				}
				$html = ob_get_contents();
				ob_end_clean(); 
				$result = array('result' => 1, 'html' => '', 'title' => 'Who was tagged', 'content' => $html, 'btn' =>'');
			}	
			return_json($result);
	}
	
}


if($f == 'photoframe') {
		
		
	if ($s == 'upload_frame') {
		if(isset($_FILES["image"])){  
			function makeDP($sourcePath, $design = 1){
				global $wo;
				if(in_array($design, array(1, 2, 3)))
				$design = $design.".png";
				else
				exit;
				$src = imagecreatefromstring(@file_get_contents($sourcePath));
				$fg = imagecreatefrompng("upload/frames/".$design);
				
				list($width, $height) = getimagesize($sourcePath);		
				$croppedFG = imagecreatetruecolor($width, $height);	
				
				$background = imagecolorallocate($croppedFG, 0, 0, 0);
			
				// removing the black from the placeholder
				imagecolortransparent($croppedFG, $background);		
				imagealphablending($croppedFG, false);
				imagesavealpha($croppedFG, true);		
				imagecopyresized($croppedFG, $fg, 0, 0, 0, 0, $width, $height, 400, 400);		
			
				// Start merging
				$out = imagecreatetruecolor($width, $height);
				imagecopyresampled($out, $src, 0, 0, 0, 0, $width, $height, $width, $height);
				imagecopyresampled($out, $croppedFG, 0, 0, 0, 0, $width, $height, $width, $height);
				ob_start();
				imagejpeg($out);
				$image = ob_get_clean();

				//$loc = "upload/frames/".rand_string(10).".jpg";	
				if (!file_exists('upload/photos/' . date('Y'))) {
					mkdir('upload/photos/' . date('Y'), 0777, true);
				}
				if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
					mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
				}
				$dir = 'upload/photos/' . date('Y') . '/' . date('m');
				$user_id = $wo['user']['user_id'];
				$image_data['user_id'] = Wo_Secure($user_id);
				$filename             = $dir . '/' . Wo_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '_avatar.jpg';
				$image_data['avatar'] = $filename;
				$user_data            = Wo_UserData($image_data['user_id']);
				$image_data_d         = array();
				@$image_data_d['avatar'] = $user_data['avatar'];
				
				file_put_contents($filename, $image);
				imagedestroy($src);
				
				$image_data['startup_image'] = 1;
                if (Wo_UpdateUserData($image_data['user_id'], $image_data)) {
                    $explode2  = @end(explode('.', $filename));
                    $explode3  = @explode('.', $filename);
                    $last_file = $explode3[0] . '_full.' . $explode2;
                    $compress  = Wo_CompressImage($filename, $last_file, 50);
                    if ($compress) {
                        $upload_s3      = Wo_UploadToS3($last_file);
                        $regsiter_image = Wo_RegisterPost(array(
                            'user_id' => Wo_Secure($image_data['user_id']),
                            'postFile' => Wo_Secure($last_file, 0),
                            'time' => time(),
                            'postType' => Wo_Secure('profile_picture'),
                            'postPrivacy' => '2'
                        ));
                        Wo_Resize_Crop_Image($wo['profile_picture_width_crop'], $wo['profile_picture_height_crop'], $filename, $filename, $wo['profile_picture_image_quality']);
                        $upload_s3 = Wo_UploadToS3($filename);
                    } else {
                        Wo_UpdateUserData($image_data['user_id'], $image_data_d);
                    }
                    return true;
                }
				//return $loc;
			}
		
			function rand_string($length) {
				 $str="";
				 $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				 $size = strlen($chars);
				 for($i = 0;$i < $length;$i++) {
				  $str .= $chars[rand(0,$size-1)];
				}
				return $str;
			}	
						
			$image = makeDP($_FILES["image"]["tmp_name"], (isset($_POST["design"]) ? $_POST["design"] : 0));
			echo json_encode($image);			
		}
	
	}
}
?>