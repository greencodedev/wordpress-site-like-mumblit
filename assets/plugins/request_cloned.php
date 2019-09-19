<?php
/* autor Pp Galvan - LdrMx
$s == 'delete_post'
$s == 'insert_new_post'
*/
    if ($f == 'post_delete') {
        $post_id = ( isset($_POST['post_id']) ? $_POST['post_id'] : ( isset($_GET['post_id']) ? $_GET['post_id'] : '0' ) );
		$data = array('status' => 300);
		if (!empty($post_id)) {
            if (Delete_Post($post_id)) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
	
	
	
	
	if ($f == 'insert_new_post') {
        $media         = '';
        $mediaFilename = '';
        $post_photo    = '';
        $mediaName     = '';
        $video_thumb   = '';
        $html          = '';
        $recipient_id  = 0;
        $page_id       = 0;
        $group_id      = 0;
        $event_id      = 0;
        $invalid_file  = false;
        $errors        = false;
        $image_array   = array();
        if (Wo_CheckSession($hash_id) === false) {
            return false;
            die();
        }
        if (isset($_POST['recipient_id']) && !empty($_POST['recipient_id'])) {
            $recipient_id = Wo_Secure($_POST['recipient_id']);
        } else if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
            $event_id = Wo_Secure($_POST['event_id']);
        } else if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
            $page_id = Wo_Secure($_POST['page_id']);
        } else if (isset($_POST['group_id']) && !empty($_POST['group_id'])) {
            $group_id = Wo_Secure($_POST['group_id']);
            $group    = Wo_GroupData($group_id);
            if (!empty($group['id'])) {
                if ($group['privacy'] == 1) {
                    $_POST['postPrivacy'] = 0;
                } else if ($group['privacy'] == 2) {
                    $_POST['postPrivacy'] = 2;
                }
            }
        }
        if (isset($_FILES['postFile']['name'])) {
            if ($_FILES['postFile']['size'] > $wo['config']['maxUpload']) {
                $invalid_file = 1;
            } else if (Wo_IsFileAllowed($_FILES['postFile']['name']) == false) {
                $invalid_file = 2;
            } else {
                $fileInfo = array(
                    'file' => $_FILES["postFile"]["tmp_name"],
                    'name' => $_FILES['postFile']['name'],
                    'size' => $_FILES["postFile"]["size"],
                    'type' => $_FILES["postFile"]["type"]
                );
                $media    = Wo_ShareFile($fileInfo);
                if (!empty($media)) {
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                }
            }
        }
        if (isset($_FILES['postVideo']['name']) && empty($mediaFilename)) {
            if ($_FILES['postVideo']['size'] > $wo['config']['maxUpload']) {
                $invalid_file = 1;
            } else if (Wo_IsFileAllowed($_FILES['postVideo']['name']) == false) {
                $invalid_file = 2;
            } else {
                $fileInfo = array(
                    'file' => $_FILES["postVideo"]["tmp_name"],
                    'name' => $_FILES['postVideo']['name'],
                    'size' => $_FILES["postVideo"]["size"],
                    'type' => $_FILES["postVideo"]["type"],
                    'types' => 'mp4,m4v,webm,flv,mov,mpeg'
                );
                $media    = Wo_ShareFile($fileInfo);
                if (!empty($media)) {
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                    $img_types     = array(
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/gif'
                    );
                    if (!empty($_FILES['video_thumb']) && in_array($_FILES["video_thumb"]["type"], $img_types)) {
                        $fileInfo = array(
                            'file' => $_FILES["video_thumb"]["tmp_name"],
                            'name' => $_FILES['video_thumb']['name'],
                            'size' => $_FILES["video_thumb"]["size"],
                            'type' => $_FILES["video_thumb"]["type"],
                            'types' => 'jpeg,png,jpg,gif',
                            'crop' => array(
                                'width' => 525,
                                'height' => 295
                            )
                        );
                        $media    = Wo_ShareFile($fileInfo);
                        if (!empty($media)) {
                            $video_thumb = $media['filename'];
                        }
                    }
                }
            }
        }
        if (isset($_FILES['postMusic']['name']) && empty($mediaFilename)) {
            if ($_FILES['postMusic']['size'] > $wo['config']['maxUpload']) {
                $invalid_file = 1;
            } else if (Wo_IsFileAllowed($_FILES['postMusic']['name']) == false) {
                $invalid_file = 2;
            } else {
                $fileInfo = array(
                    'file' => $_FILES["postMusic"]["tmp_name"],
                    'name' => $_FILES['postMusic']['name'],
                    'size' => $_FILES["postMusic"]["size"],
                    'type' => $_FILES["postMusic"]["type"],
                    'types' => 'mp3,wav'
                );
                $media    = Wo_ShareFile($fileInfo);
                if (!empty($media)) {
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                }
            }
        }
        $multi = 0;
        if (isset($_FILES['postPhotos']['name']) && empty($mediaFilename) && empty($_POST['album_name'])) {
            if (count($_FILES['postPhotos']['name']) == 1) {
                if ($_FILES['postPhotos']['size'][0] > $wo['config']['maxUpload']) {
                    $invalid_file = 1;
                } else if (Wo_IsFileAllowed($_FILES['postPhotos']['name'][0]) == false) {
                    $invalid_file = 2;
                } else {
                    $fileInfo = array(
                        'file' => $_FILES["postPhotos"]["tmp_name"][0],
                        'name' => $_FILES['postPhotos']['name'][0],
                        'size' => $_FILES["postPhotos"]["size"][0],
                        'type' => $_FILES["postPhotos"]["type"][0]
                    );
                    $media    = Wo_ShareFile($fileInfo);
                    if (!empty($media)) {
                        $mediaFilename = $media['filename'];
                        $mediaName     = $media['name'];
                    }
                }
            } else {
                $multi = 1;
            }
        }
        if (empty($_POST['postPrivacy'])) {
            $_POST['postPrivacy'] = 0;
        }
        $post_privacy  = 0;
        $privacy_array = array(
            '0',
            '1',
            '2',
            '3'
        );
        if (isset($_POST['postPrivacy'])) {
            if (in_array($_POST['postPrivacy'], $privacy_array)) {
                $post_privacy = $_POST['postPrivacy'];
            }
        }
        setcookie("post_privacy", $post_privacy, time() + (10 * 365 * 24 * 60 * 60));
        $import_url_image = '';
        $url_link         = '';
        $url_content      = '';
        $url_title        = '';
        if (!empty($_POST['url_link']) && !empty($_POST['url_title'])) {
            $url_link  = $_POST['url_link'];
            $url_title = $_POST['url_title'];
            if (!empty($_POST['url_content'])) {
                $url_content = $_POST['url_content'];
            }
            if (!empty($_POST['url_image'])) {
                $import_url_image = @Wo_ImportImageFromUrl($_POST['url_image']);
            }
        }
        $post_text = '';
        $post_map  = '';
        if (!empty($_POST['postText']) && !ctype_space($_POST['postText'])) {
            $post_text = $_POST['postText'];
        }
        if (!empty($_POST['postMap'])) {
            $post_map = $_POST['postMap'];
        }
        $album_name = '';
        if (!empty($_POST['album_name'])) {
            $album_name = $_POST['album_name'];
        }
        if (!isset($_FILES['postPhotos']['name'])) {
            $album_name = '';
        }
		
		/*plugin question value*/
		$_tmp_options = ( isset($_POST['options']) ? $_POST['options'] : ( isset($_GET['options']) ? $_GET['options'] : array() ) );
		if(count($_tmp_options) > 0){
		   $_tmp_opts = array();
		   foreach ($_tmp_options as $_opt){ if ($_opt){ $_tmp_opts[] = $_opt; }}
		   if(count($_tmp_opts) > 0){
			  if(count($_tmp_opts) > 0 && empty($post_text)){
				  $data = array('status' => '400', 'errors' => 'Please add a question');
				  header("Content-type: application/json");
				  echo json_encode($data);
				  exit();
			  }
			  if(count($_tmp_opts) == 1 && !empty($post_text)){
				  $data = array('status' => '400', 'errors' => 'Please add more of a answer');
				  header("Content-type: application/json");
				  echo json_encode($data);
				  exit();
			  }
			  if(count($_tmp_opts) >= $wo['system']['question_limit_answers'] && !empty($post_text)){
				  $data = array('status' => '400', 'errors' => $wo['system']['question_limit_answers'].' is number limit of answers');
				  header("Content-type: application/json");
				  echo json_encode($data);
				  exit();
			  }
		   }
		}
		/*plugin question value*/
		
        $traveling = '';
        $watching  = '';
        $playing   = '';
        $listening = '';
        $feeling   = '';
        if (!empty($_POST['feeling_type'])) {
            $array_types = array(
                'feelings',
                'traveling',
                'watching',
                'playing',
                'listening'
            );
            if (in_array($_POST['feeling_type'], $array_types)) {
                if ($_POST['feeling_type'] == 'feelings') {
                    if (!empty($_POST['feeling'])) {
                        if (array_key_exists($_POST['feeling'], $wo['feelingIcons'])) {
                            $feeling = $_POST['feeling'];
                        }
                    }
                } else if ($_POST['feeling_type'] == 'traveling') {
                    if (!empty($_POST['feeling'])) {
                        $traveling = $_POST['feeling'];
                    }
                } else if ($_POST['feeling_type'] == 'watching') {
                    if (!empty($_POST['feeling'])) {
                        $watching = $_POST['feeling'];
                    }
                } else if ($_POST['feeling_type'] == 'playing') {
                    if (!empty($_POST['feeling'])) {
                        $playing = $_POST['feeling'];
                    }
                } else if ($_POST['feeling_type'] == 'listening') {
                    if (!empty($_POST['feeling'])) {
                        $listening = $_POST['feeling'];
                    }
                }
            }
        }
        if (isset($_FILES['postPhotos']['name'])) {
            $allowed = array(
                'gif',
                'png',
                'jpg',
                'jpeg'
            );
            for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
                $new_string = pathinfo($_FILES['postPhotos']['name'][$i]);
                if (!in_array(strtolower($new_string['extension']), $allowed)) {
                    $errors[] = $error_icon . $wo['lang']['please_check_details'];
                }
            }
        }
        if (!empty($_POST['answer']) && array_filter($_POST['answer'])) {
            if (!empty($_POST['postText'])) {
                foreach ($_POST['answer'] as $key => $value) {
                    if (empty($value) || ctype_space($value)) {
                        $errors = 'Answer #' . ($key + 1) . ' is empty.';
                    }
                }
            } else {
                $errors = 'Please write the question.';
            }
        }
        if (empty($errors)) {
            $is_option = false;
            if (!empty($_POST['answer']) && array_filter($_POST['answer'])) {
                $is_option = true;
            }
			/*plugin color*/    
			$color = 0;
			if(!empty($_POST['color'])){
				$color = $_POST['color'];
            $post_data = array(
                'user_id' => Wo_Secure($wo['user']['user_id']),
                'page_id' => Wo_Secure($page_id),
                'group_id' => Wo_Secure($group_id),
                'event_id' => Wo_Secure($event_id),
                'postText' => Wo_Secure($post_text),
                'recipient_id' => Wo_Secure($recipient_id),
                'postRecord' => Wo_Secure($_POST['postRecord']),
                'postFile' => Wo_Secure($mediaFilename, 0),
                'postFileName' => Wo_Secure($mediaName),
                'postMap' => Wo_Secure($post_map),
                'postPrivacy' => Wo_Secure($post_privacy),
                'postLinkTitle' => Wo_Secure($url_title),
                'postLinkContent' => Wo_Secure($url_content),
                'postLink' => Wo_Secure($url_link),
                'postLinkImage' => Wo_Secure($import_url_image, 0),
                'album_name' => Wo_Secure($album_name),
                'multi_image' => Wo_Secure($multi),
                'postFeeling' => Wo_Secure($feeling),
                'postListening' => Wo_Secure($listening),
                'postPlaying' => Wo_Secure($post_photo),
                'postWatching' => Wo_Secure($watching),
                'postTraveling' => Wo_Secure($traveling),
                'postFileThumb' => Wo_Secure($video_thumb),
                'time' => time(),
				'color' => Wo_Secure($color)
            );
			} else {
            $post_data = array(
                'user_id' => Wo_Secure($wo['user']['user_id']),
                'page_id' => Wo_Secure($page_id),
                'group_id' => Wo_Secure($group_id),
                'event_id' => Wo_Secure($event_id),
                'postText' => Wo_Secure($post_text),
                'recipient_id' => Wo_Secure($recipient_id),
                'postRecord' => Wo_Secure($_POST['postRecord']),
                'postFile' => Wo_Secure($mediaFilename, 0),
                'postFileName' => Wo_Secure($mediaName),
                'postMap' => Wo_Secure($post_map),
                'postPrivacy' => Wo_Secure($post_privacy),
                'postLinkTitle' => Wo_Secure($url_title),
                'postLinkContent' => Wo_Secure($url_content),
                'postLink' => Wo_Secure($url_link),
                'postLinkImage' => Wo_Secure($import_url_image, 0),
                'album_name' => Wo_Secure($album_name),
                'multi_image' => Wo_Secure($multi),
                'postFeeling' => Wo_Secure($feeling),
                'postListening' => Wo_Secure($listening),
                'postPlaying' => Wo_Secure($playing),
                'postWatching' => Wo_Secure($watching),
                'postTraveling' => Wo_Secure($traveling),
                'postFileThumb' => Wo_Secure($video_thumb),
                'time' => time()
            );
			}
			
            if (isset($_POST['postSticker']) && Wo_IsUrl($_POST['postSticker']) && empty($_FILES) && empty($_POST['postRecord'])) {
                $post_data['postSticker'] = $_POST['postSticker'];
            } else if (empty($_FILES['postPhotos']) && preg_match_all('/https?:\/\/(?:[^\s]+)\.(?:png|jpg|gif|jpeg)/', $post_data['postText'], $matches)) {
                if (!empty($matches[0][0]) && Wo_IsUrl($matches[0][0])) {
                    $post_data['postPhoto'] = @Wo_ImportImageFromUrl($matches[0][0]);
                }
            }
            if (!empty($is_option)) {
                $post_data['poll_id'] = 1;
            }
            //plugin
			//$id = Wo_RegisterPost($post_data);
			$id = Register_Post($post_data);
			$question_id = 0;
			//plugin
            if ($id) {
                Wo_CleanCache();
                Wo_UpdateUserDetails($wo['user'], true, false, false, true);
				//question				
				$question = $post_text;
				$tmp_options = ( isset($_POST['options']) ? $_POST['options'] : ( isset($_GET['options']) ? $_GET['options'] : array() ) );
	
				$options = array();
			   if(count($tmp_options) > 0 && $question != ''){			  
				  $question = Wo_Secure($question);
			  
				  foreach ($tmp_options as $option){ 
					 if($option){ $options[] = $option; }
				  }
				  if(count($options) > 0){ 
				    $post_data['question_id'] = 1;					
					
					$sqlConnect->query("INSERT INTO posts_questions (question_user_id, question_question, post_id) VALUES ('{$wo['user']['user_id']}', '{$question}', '{$id}')");
					$question_id = $sqlConnect->insert_id;					  
					  foreach ($options as $answer){
							$answer = Wo_Secure($answer);
							$sqlConnect->query("INSERT INTO posts_questions_options (title, question_id) VALUES ('{$answer}', '{$question_id}')");
					  }
					$sqlConnect->query("UPDATE Wo_Posts SET postType='question', question_id='{$question_id}' WHERE post_id = '{$id}' LIMIT 1");
				  }
				}		   			 
				//question
				//tag_friend
				$tag_friends = ( isset($_POST['tag_friends']) ? $_POST['tag_friends'] : ( isset($_GET['tag_friends']) ? $_GET['tag_friends'] : array() ) );
				$tag_friend = array();
			   if(count($tag_friends) > 0){			  
				  foreach ($tag_friends as $tf){ 
					 if($tf && is_numeric($tf)){ $tag_friend[] = $tf; }
				  }
				  if(count($tag_friend) > 0){ 				  
					  foreach ($tag_friend as $tagfriend){
							$tagfriend = Wo_Secure($tagfriend);
							if($tagfriend != $wo['user']['user_id']){
								$sqlConnect->query("INSERT INTO posts_tags (user_id, post_id) VALUES ('{$tagfriend}', '{$id}')");
								/*$notification here*/
							}
					  }
				  }
				}		   			 
				//tag_friend
                if ($is_option == true) {
                    foreach ($_POST['answer'] as $key => $value) {
                        $add_opition = Wo_AddOption($id, $value);
                    }
                }
                if (isset($_FILES['postPhotos']['name'])) {
                    if (count($_FILES['postPhotos']['name']) > 0) {
                        for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
                            $fileInfo = array(
                                'file' => $_FILES["postPhotos"]["tmp_name"][$i],
                                'name' => $_FILES['postPhotos']['name'][$i],
                                'size' => $_FILES["postPhotos"]["size"][$i],
                                'type' => $_FILES["postPhotos"]["type"][$i],
                                'types' => 'jpg,png,jpeg,gif'
                            );
                            $file     = Wo_ShareFile($fileInfo, 1);
                            if (!empty($file)) {
                                $media_album = Wo_RegisterAlbumMedia($id, $file['filename']);
                            }
                        }
                    }
                }
				/* plugin points */
				if(!empty($wo['plugin_list']['plugin_actived']) && in_array('Points', $wo['plugin_list']['plugin_actived']) && !empty($wo['system']['userpoints_enable']) && $wo['system']['userpoints_enable']){ update_points( $wo['user']['user_id'], 'post' ); }	
				/* plugin points */
                $wo['story'] = Wo_PostData($id);
                $html .= Wo_LoadPage('story/content');
                $data = array(
                    'status' => 200,
                    'html' => $html,
                    'invalid_file' => $invalid_file,
                    'post_count' => $wo['story']['publisher']['details']['post_count'],
					'is_question' => $question_id
                );
            } else {
                $data = array(
                    'status' => 400,
                    'invalid_file' => $invalid_file
                );
            }
        } else {
            header("Content-type: application/json");
            echo json_encode(array(
                'status' => 400,
                'errors' => $errors,
                'invalid_file' => $invalid_file
            ));
            exit();
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }	
?>