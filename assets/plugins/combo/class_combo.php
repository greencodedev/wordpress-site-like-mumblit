<?php 
class combo{			
		
	//DELETE IMAGE
	function delete_image($reaction_id){
		global $sqlConnect;
		$resource = $sqlConnect->query('SELECT * FROM reactions WHERE reaction_id = '.$reaction_id.'');
		$file = $resource->fetch_assoc();
		
		if($file['reaction_key'] == 'like'){ return_json(array('result' => '0', 'error' => true, 'message' => "this reaction no can delete")); }	
			
		unlink("upload/reaction/reaction_{$file['reaction_key']}.{$file['reaction_filetype']}");
		unlink("upload/reaction/reaction-{$file['reaction_key']}-button.php");
		$sqlConnect->query("DELETE FROM Wo_Langs WHERE id = '{$file['reaction_lang']}' LIMIT 1");
		$sqlConnect->query('DELETE FROM reactions WHERE reaction_id = '.$file['reaction_id'].'');
		//default like
		$sqlConnect->query("UPDATE Wo_Likes SET reaction='like' WHERE reaction='{$file['reaction_key']}'"); 
		$sqlConnect->query("UPDATE Wo_Notifications SET type='liked_post', app_src='' WHERE type='reaction_post' AND text='reaction_{$file['reaction_key']}.{$file['reaction_filetype']}'"); 
	}

	//UPLOAD
	function upload($reaction_key = '', $text = '', $reaction_status = '1', $reaction_colour = 'yellow-opt') {
		  global $sqlConnect, $url;
		  
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
				return array('result' => 0, 'message' => 'Extension no allowed', 'error' => true);
			}
		  
			$sqlConnect->query("INSERT INTO `reactions` (`reaction_key`, `reaction_colour`,`reaction_status`, `reaction_date`) VALUES ('{$reaction_key}', '{$reaction_colour}', '{$reaction_status}', '".time()."')");
			$reaction_id = $sqlConnect->insert_id;
			
			$dest = "upload/reaction/";
		  
			$file_dest = $dest.'reaction_'.$reaction_key.'.'.$ext;
			
			if(move_uploaded_file($_FILES['file']['tmp_name'], $file_dest)){
			     //Wo_Resize_Crop_Image($width, $height, $file_dest, $file_dest, 20);
				 $last_file = $file_dest;
				  $explode2  = @end(explode('.', $file_dest));
				  $explode3  = @explode('.', $file_dest);
				  //$last_file = $explode3[0] . '_full.' . $explode2;
				  //@Wo_CompressImage($file_dest, $last_file, 20);	 							  
			}
	        
			// create php file
			$php_string = '<span class="text-link story-like-btn yellow-opt opt" onclick="set_reaction($(this), \''.$reaction_key.'\', <?php echo $post_id; ?>);" title="<?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>">
<img src="<?php echo $wo[\'config\'][\'site_url\']; ?>/upload/reaction/reaction_'.$reaction_key.'.'.$ext.'" class="progress-img" width="14px"> <?php echo $wo[\'lang\'][\'reaction_'.$reaction_id.'\']; ?>
</span>';
			
			$config_file = $dest.'reaction-'.$reaction_key.'-button.php';
			$handle = fopen($config_file, 'w');
			
			fwrite($handle, $php_string);
			fclose($handle);
					
			// UPLOAD AND RESIZE PHOTO IF NO ERROR
			if(!file_exists($file_dest)) {		
					$sqlConnect->query("DELETE FROM reactions WHERE reaction_id='{$reaction_id}' LIMIT 1");
					@unlink($file_dest);
					return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
			} else { 				
					$text = substr($text,0,255);
					$text = htmlspecialchars(stripslashes($text));
						
					//get all langs
					$resource = $sqlConnect->query("SHOW COLUMNS FROM Wo_Langs");
					while ($fetched_data = $resource->fetch_assoc()) {
						$data[] = $fetched_data['Field'];
					}
					unset($data[0]);
					unset($data[1]);

					$resource = $sqlConnect->query("SELECT * FROM Wo_Langs WHERE lang_key = 'reaction_".$reaction_id."' LIMIT 1");
					if($resource->num_rows > 0){					
						$rowz = $resource->fetch_assoc();
						$langz = "";	
						$javascript_lang_import_first = TRUE; 	
						foreach($data as $key => $val){
							if( !$javascript_lang_import_first ) $langz .= ", ";
							$langz .= "`{$val}`='{$text}'";
							$javascript_lang_import_first = FALSE;
						}			
						$sql_new = "UPDATE `Wo_Langs` SET {$langz} WHERE id='{$rowz['id']}' LIMIT 1";			
						$sqlConnect->query($sql_new);			
						$new_lang = $rowz['id'];
					}else{
						//insert lang
						$langz = "";
						$sqlConnect->query("INSERT INTO `Wo_Langs` (`lang_key`) VALUES ('reaction_".$reaction_id."')");
						$new_lang = $sqlConnect->insert_id;
						$javascript_lang_import_first = TRUE; 	
						foreach($data as $key => $val){
							if( !$javascript_lang_import_first ) $langz .= ", ";
							$langz .= "`{$val}`='{$text}'";
							$javascript_lang_import_first = FALSE;
						}			
						$sql_new = "UPDATE `Wo_Langs` SET {$langz} WHERE id='{$new_lang}' LIMIT 1";			
						$sqlConnect->query($sql_new);
					}
		
					// UPDATE IF NO ERROR
					$sqlConnect->query("UPDATE reactions SET reaction_filetype='{$ext}', reaction_lang ='{$new_lang}', reaction_order ='{$reaction_id}'  WHERE reaction_id='{$reaction_id}' LIMIT 1"); 
					$db_update =  $sqlConnect->affected_rows;					
							
					if($db_update == 1){
					   return array('result' => 1, 'message' => "Successfully reaction was created", 'results'=> $db_update, 'success' => true, 'reaction_id' => $reaction_id, 'ext' => $ext);
					} else {
					   return array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true);
					}				
			}	
		}		
	}	
?>