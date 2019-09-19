<?php
/* request
 * @autor : Pp Galvan - LdrMx
 */
 
if($f == 'admin_colorbox'){
	$is_admin = Wo_IsAdmin();
	$is_moderoter = Wo_IsModerator();
	if ($is_admin == false && $is_moderoter == false) {
		  /* return ajax */		
		  return_json(array('status' => 200, 'message' => "You don't have the right permission to access this"));
	}

	include "assets/plugins/colorbox/class_colorbox.php";
	$adm_colorbox = new colorbox();
	
	$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : '' ) );
	$id = ( isset($_POST['id']) ? $_POST['id'] : ( isset($_GET['id']) ? $_GET['id'] : 0 ) );
	$type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : 0 ) );
	$color1 = ( isset($_POST['color1']) ? $_POST['color1'] : ( isset($_GET['color1']) ? $_GET['color1'] : '' ) );
	$color2 = ( isset($_POST['color2']) ? $_POST['color2'] : ( isset($_GET['color2']) ? $_GET['color2'] : '' ) );
	$status = ( isset($_POST['status']) ? $_POST['status'] : ( isset($_GET['status']) ? $_GET['status'] : 1 ) );
	$count_id = ( isset($_POST['count_id']) ? $_POST['count_id'] : ( isset($_GET['count_id']) ? $_GET['count_id'] : 0 ) );  
	
	//check security
	$task = Wo_Secure($task);
	$id = Wo_Secure($id);
	$type = Wo_Secure($type);
	$color1 = Wo_Secure($color1);
	$color2 = Wo_Secure($color2);
	$status = Wo_Secure($status);
	$count_id = Wo_Secure($count_id);

switch ($task) {						
    case 'new_src':
			if(!empty($type) && $type == 3 && !isset($_FILES)){				
      		    return_json(array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Select a image"));	
			}     
			$upload_result = array();
			$upload_result = $adm_colorbox->upload($type, $status);

			if($upload_result['result'] == 1){ 
				$img = '<div class="col-sm-3 colorbox" style="padding-right:2px;" data-token="0" data-id="'.$upload_result['id'].'"><div class="panel panel-default clearfix" style="padding:0;"><div class="panel-body" style="padding:0;"><div class="pull-right" style="margin:5px; position:absolute;"><button type="button" class="close js_colorbox_remover" title="Remove"><span>×</span></button></div><div class="pull-left image" style="color:rgba(255,255,255,1.00);font-size:30px;font-weight:700;line-height:1.2000em;padding:50px 30px;text-align:center; background-repeat: no-repeat; background-size: 100% 100%; height: 120px; width:100%; background-image : url(&quot;'.$wo['config']['site_url'].'/upload/colorbox/'.$upload_result['id'].'.'.$upload_result['ext'].'&quot;);"><div><label for="new_src_'.$upload_result['id'].'" class="btn label_new_src"><i class="fa fa-camera"></i></label><input class="new_src" style="display:none;" name="file" type="file" accept="image/*" id="new_src_'.$upload_result['id'].'"></div></div></div></div></div>';
				/* return */
				$data = array('result' => 1, 'html' => $img, 'id'=> $count_id, 'status' => '200', 'success' => true, 'message' => "Done, was added");
			} else { 
				/* return */
				$data = array( 'result' => 0, 'id'=> $count_id, 'status' => '200', 'success' => true, 'message' => "Error no was added");
			}
		/* return ajax */		
		return_json($data);	
		break;
		
	case 'new_color':
			if(empty($color1) && empty($color2)){				
      		    return_json(array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Please add a color"));	
			}     
			$sqlConnect->query("INSERT INTO `colorbox` (`c1`, `c2`, `type`, `status`, `date`) VALUES ('{$color1}', '{$color2}', '{$type}', '{$status}', '".time()."')");
			$cid = $sqlConnect->insert_id;


			if($cid){ 
				if($type == 1){
					$new_color = 'background-color : '.$color1.'';
				} else if($type == 2){
					$new_color = 'background-image : linear-gradient(45deg, '.$color1.' 0%, '.$color2.' 100%)';
				}
				$color_return = '<div class="col-sm-3 colorbox" style="padding-right:2px;" data-token="0" data-id="'.$cid.'"><div class="panel panel-default clearfix" style="padding:0;"><div class="panel-body" style="padding:0;"><div class="pull-right" style="margin:5px; position:absolute;"><button type="button" class="close js_colorbox_remover" title="Remove"><span>×</span></button></div><div class="pull-left image" style="color:rgba(255,255,255,1.00);font-size:30px;font-weight:700;line-height:1.2000em;padding:50px 30px;text-align:center; background-repeat: no-repeat; background-size: 100% 100%; height: 120px; width:100%; '.$new_color.';"></div></div></div></div>';
				/* return */
				$data = array('result' => 1, 'html' => $color_return, 'id'=> $cid, 'status' => '200', 'success' => true, 'message' => "Done, was added");
			} else { 
				/* return */
				$data = array( 'result' => 0, 'id'=> $cid, 'status' => '200', 'success' => true, 'message' => "Error no was added");
			}
		/* return ajax */		
		return_json($data);	
		break;							
		
	
	case 'status':
		// UPDATE IF NO ERROR
		$sqlConnect->query("UPDATE colorbox SET status='{$status}' WHERE id='{$id}' LIMIT 1"); 
		$db_update =  $sqlConnect->affected_rows;					
				
		if($db_update == 1){
		   return_json(array('result' => 1, 'message' => "Successfully colorbox was created", 'results'=> $db_update, 'success' => true, 'id' => $id));
		} else {
		   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
		}
		break;
		
		case 'color_edit':        	        
			$resource = $sqlConnect->query('SELECT * FROM colorbox WHERE id='.$id.' LIMIT 1');
			$file = $resource->fetch_assoc();
			if($resource->num_rows > 0){	
				// UPDATE IF NO ERROR
				$sqlConnect->query("UPDATE colorbox SET c1='{$color1}', c2='{$color2}' WHERE id='{$id}' LIMIT 1"); 
				$db_update =  $sqlConnect->affected_rows;	
				if($type == 1){
					$new_color = 'background-color : '.$color1.'';
				} else if($type == 2){
					$new_color = 'background-image : linear-gradient(45deg, '.$color1.' 0%, '.$color2.' 100%)';
				}
				$color_return = '<div class="pull-right" style="margin:5px; position:absolute;"><button type="button" class="close js_colorbox_remover" title="Remove"><span>×</span></button></div><div class="pull-left image" style="color:rgba(255,255,255,1.00);font-size:30px;font-weight:700;line-height:1.2000em;padding:50px 30px;text-align:center; background-repeat: no-repeat; background-size: 100% 100%; height: 120px; width:100%; '.$new_color.';"><div onclick="get_info_color(\''.$type.'\', \''.$id.'\', \''.$color1.'\', \''.$color2.'\');" class="btn"><i class="fa fa-pencil"></i></div></div>';									
				if($db_update == 1){
				   return_json(array('result' => 1, 'message' => "Successfully colorbox was created", 'results'=> $db_update, 'success' => true, 'id' => $id, 'html' => $color_return));
				} else {
				   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
				}
			}
			return_json(array('result' => 0, 'message' => 'This color no exist', 'results'=> false, 'error' => true));
		break;
		
		//colorbox src
		case 'src':	
			/* return ajax*/			
			if(!isset($_FILES)){					
      		    return_json(array( 'result' => 0, 'status' => '200', 'error' => true, 'message' => "Select a image"));	
			}     
			
			$name = $_FILES['file']['name'];  
			$ext = pathinfo($name, PATHINFO_EXTENSION);
			
			//create directory 
			if(!file_exists('upload/colorbox')) { mkdir('upload/colorbox', 0777, true); } 

			$allowed           = 'jpg,png,jpeg,gif';
			$new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
			$extension_allowed = explode(',', $allowed);
			$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
			
			//extension allowed
			if(!in_array($file_extension, $extension_allowed)) { 
				return return_json(array('result' => 0, 'message' => 'Extension no allowed', 'error' => true));
			}
			//get info    
			$resource = $sqlConnect->query('SELECT * FROM colorbox WHERE id = '.$id.' LIMIT 1');
			$file = $resource->fetch_assoc();
			//delete old	
			unlink("upload/colorbox/{$file['src']}.{$file['filetype']}");
	
			$file_dest = 'upload/colorbox/'.$file['src'].'.'.$ext;		
			
			//upload
			if(move_uploaded_file($_FILES['file']['tmp_name'], $file_dest)){
				 $last_file = $file_dest;
				  $explode2  = @end(explode('.', $file_dest));
				  $explode3  = @explode('.', $file_dest);							  
			} else {
				return return_json(array('result' => 0, 'message' => 'Can not upload', 'error' => true));			
			}		
							
			if(file_exists($file_dest)){		
				//update colorbox 
				$sqlConnect->query("UPDATE colorbox SET filetype='{$ext}' WHERE id='{$id}' LIMIT 1"); 
				$db_update =  $sqlConnect->affected_rows;											
			    $rand_src = rand(1000,9999);
			   return return_json(array('result' => 1, 'message' => "Successfully", 'results'=> $db_update, 'success' => true, 'html' => $wo['config']['site_url'].'/'.$file_dest.'?rand='.$rand_src));
			} else {
			   return return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
			}
		break;
						
	case 'delete':
			// valid inputs & return ajax 
			if(!isset($id) || !is_numeric($id)) {  		
      		   return_json(array('status' => 200, 'message' => "This no is a number"));
			}
			$result = $adm_colorbox->delete_image($id);		

	  		/* return ajax */		
      		return_json(array('status' => '200', 'success' => $result, 'message' => "Done, was deleted"));	
			break;			


	case 'setting':
 	  		$colorbox_enable = ( isset($_POST['colorbox_enable']) ? $_POST['colorbox_enable'] : ( isset($_GET['colorbox_enable']) ? $_GET['colorbox_enable'] : '1' ) );
			$colorbox_limit = ( isset($_POST['colorbox_limit']) ? $_POST['colorbox_limit'] : ( isset($_GET['colorbox_limit']) ? $_GET['colorbox_limit'] : '1' ) );
			$colorbox_image_cut = ( isset($_POST['colorbox_image_cut']) ? $_POST['colorbox_image_cut'] : ( isset($_GET['colorbox_image_cut']) ? $_GET['colorbox_image_cut'] : '1' ) );
			$colorbox_image_cut_width = ( isset($_POST['colorbox_image_cut_width']) ? $_POST['colorbox_image_cut_width'] : ( isset($_GET['colorbox_image_cut_width']) ? $_GET['colorbox_image_cut_width'] : '1' ) );
			$colorbox_image_cut_height = ( isset($_POST['colorbox_image_cut_height']) ? $_POST['colorbox_image_cut_height'] : ( isset($_GET['colorbox_image_cut_height']) ? $_GET['colorbox_image_cut_height'] : '1' ) );
			$colorbox_image_quality = ( isset($_POST['colorbox_image_quality']) ? $_POST['colorbox_image_quality'] : ( isset($_GET['colorbox_image_quality']) ? $_GET['colorbox_image_quality'] : '1' ) );
			
			//check security
			$colorbox_enable = Wo_Secure($colorbox_enable);
			$colorbox_limit = Wo_Secure($colorbox_limit);
			$colorbox_image_cut = Wo_Secure($colorbox_image_cut);
			$colorbox_image_cut_width = Wo_Secure($colorbox_image_cut_width);
			$colorbox_image_cut_height = Wo_Secure($colorbox_image_cut_height);
			$colorbox_image_quality = Wo_Secure($colorbox_image_quality);
			
			$sqlConnect->query("UPDATE plugins_system SET colorbox_enable = '{$colorbox_enable}', colorbox_limit = '{$colorbox_limit}', colorbox_image_cut = '{$colorbox_image_cut}', colorbox_image_cut_width = '{$colorbox_image_cut_width}', colorbox_image_cut_height = '{$colorbox_image_cut_height}', colorbox_image_quality = '{$colorbox_image_quality}' WHERE system_id = '1' LIMIT 1");						
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
?>