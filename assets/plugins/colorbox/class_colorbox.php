<?php 
class colorbox{			
	
	//DELETE IMAGE
	function delete_image($id){
		global $sqlConnect;
		$resource = $sqlConnect->query('SELECT * FROM colorbox WHERE id = '.$id.' LIMIT 1');
		if($resource->num_rows > 0){
			$file = $resource->fetch_assoc();
			if($file['type'] == '3'){
				unlink("upload/colorbox/{$file['src']}.{$file['filetype']}");
			}
			$sqlConnect->query('DELETE FROM colorbox WHERE id='.$file['id'].'');
			$sqlConnect->query("UPDATE Wo_Posts SET color='' WHERE color='{$file['id']}'"); 
			return true;
		}
		return false;
	}

	//UPLOAD
	function upload($type = '3', $status = '1') {
		  global $sqlConnect, $wo;
		  
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
				return array('result' => 0, 'message' => 'Extension no allowed', 'error' => true);
			}		  
			$sqlConnect->query("INSERT INTO `colorbox` (`type`, `status`, `date`) VALUES ('{$type}', '{$status}', '".time()."')");
			$cid = $sqlConnect->insert_id;
			
			$dest = "upload/colorbox/";
		  
			$file_dest = $dest.$cid .'.'.$ext;
			
			if(move_uploaded_file($_FILES['file']['tmp_name'], $file_dest)){
			    if($wo['system']['colorbox_image_cut'] == 1){
					//375 x 275 $width, $height, quality 80
					Wo_Resize_Crop_Image($wo['system']['colorbox_image_cut_width'], $wo['system']['colorbox_image_cut_height'], $file_dest, $file_dest, $wo['system']['colorbox_image_quality']);
				} else {
					$last_file = $file_dest;
					$explode2  = @end(explode('.', $file_dest));
					$explode3  = @explode('.', $file_dest); 	
				}						  
			}
	        					
			// REMOVE IF ERROR
			if(!file_exists($file_dest)) {		
					$sqlConnect->query("DELETE FROM colorbox WHERE id='{$cid}' LIMIT 1");
					@unlink($file_dest);
					return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
			} else { 						
			// UPDATE IF NO ERROR
					$sqlConnect->query("UPDATE colorbox SET src='{$cid}', filetype='{$ext}' WHERE id='{$cid}' LIMIT 1"); 
					$db_update =  $sqlConnect->affected_rows;					
							
					if($db_update == 1){
					   return array('result' => 1, 'message' => "Successfully colorbox was created", 'results'=> $db_update, 'success' => true, 'id' => $cid, 'ext' => $ext);
					} else {
					   return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
					}				
			}	
		}		
	}		
?>