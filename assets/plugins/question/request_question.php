<?php
/* request
 * @autor : Pp Galvan - LdrMx
 */
if($f == 'admin_question'){

	$is_admin = Wo_IsAdmin();
	$is_moderoter = Wo_IsModerator();
	if ($is_admin === false && $is_moderoter === false) {
		  /* return ajax */		
		  return_json(array('status' => 200, 'message' => "You don't have the right permission to access this"));
	}

	$question_id = ( isset($_POST['question_id']) ? $_POST['question_id'] : ( isset($_GET['question_id']) ? $_GET['question_id'] : 0 ) );
	$option_id = ( isset($_POST['option_id']) ? $_POST['option_id'] : ( isset($_GET['option_id']) ? $_GET['option_id'] : 0 ) );
	$val = ( isset($_POST['val']) ? $_POST['val'] : ( isset($_GET['val']) ? $_GET['val'] : array() ) );
	
	switch ($task) {
		case 'question_delete':
				
				// valid inputs & return ajax 
				if(!isset($question_id) || !is_numeric($question_id)) {		
				   return_json(array('status' => 200, 'message' => "This no is a number"));
				}	
				
				Question::admin_question_delete($question_id);	
					
				/* return ajax */		
				return_json(array('status' => '200', 'success' => true, 'message' => "Done, Plugin info have been updated"));	
				break;
				
				/*setting*/
		case 'setting': 
				
				// valid inputs & return ajax
				if(count($val) == 0) { 	
				   return_json(array('status' => 200, 'message' => "No exist values"));
				}	
				
				$expected = array(
					'question_limit_answers' => '',
					'question_enable_add_answers' => '1',
					'question_enable_multi_votes' => '1',
					'question_enable_invite_to_friends' => '1',
					'question_enable_email_invite_to_friends' => '1',
					'question_enable_show_in_profile' => '1'
				);
				 
				extract(array_merge($expected, $val));
				
				$question_limit_answers = Wo_Secure($question_limit_answers);
				$question_enable_add_answers = Wo_Secure($question_enable_add_answers);
				$question_enable_multi_votes = Wo_Secure($question_enable_multi_votes);
				$question_enable_invite_to_friends = Wo_Secure($question_enable_invite_to_friends);
				$question_enable_email_invite_to_friends = Wo_Secure($question_enable_email_invite_to_friends);
				$question_enable_show_in_profile = Wo_Secure($question_enable_show_in_profile);			
									   
				$sqlConnect->query("
					UPDATE plugins_system SET 
					question_limit_answers = '{$question_limit_answers}',
					question_enable_add_answers = '{$question_enable_add_answers}',
					question_enable_multi_votes = '{$question_enable_multi_votes}',
					question_enable_invite_to_friends = '{$question_enable_invite_to_friends}',
					question_enable_email_invite_to_friends = '{$question_enable_email_invite_to_friends}',
					question_enable_show_in_profile = '{$question_enable_show_in_profile}'
					WHERE system_id = '1' LIMIT 1
				");
				
				$test =  $sqlConnect->affected_rows;
				
				/* return */
				$data = array('status' => 200, 'message' => "Done, Plugin info have been updated", 'result'=> $test, 'success' => true);
				
				/* return ajax */		
				return_json($data);
				
				break;			
				/*setting*/
				
			default: 
				/* return ajax */		
				return_json(array('status' => 200, 'message' => "This option no exist"));
			 break;
		}

}

	
if ($f == 'question_select'){
	
	// check page parameters
	if(!isset($_GET['question_id']) || !isset($_GET['option_id']) ||!isset($_GET['do'])) { 
		/* return ajax */	
		return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'message' => 'form incomplete'));
	}
	
	// valid inputs
	$valid['do'] = array('select', 'unselect');
	if(!in_array($_GET['do'], $valid['do'])) { 
		  /* return ajax */		
		  return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'message' => 'option no exist'));
	}
	
	$html = "";
	
	$post_type = 'question';
	$post_id = Wo_Secure($_GET['post_id']);
	$question_id = Wo_Secure($_GET['question_id']);
	$author_id = Wo_Secure($_GET['owner_id']);
	$option_id = Wo_Secure($_GET['option_id']);
	
	if(!preg_match('/^([0-9]+)$/', $option_id)) { 
		  /* return ajax */		
		  return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'message' => 'option error'));
	 }
	
	// check if valid post
	$checkPost = $sqlConnect->query("SELECT * FROM Wo_Posts WHERE post_id = '{$post_id}' AND user_id = '{$author_id}'");
	
	/* return ajax */	
	if($checkPost->num_rows == 0){ 	 	
		  return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'message' => 'This post content is unavailable, It may be deleted by the author'));
	}
	
	// check if valid option
	$checkOption = $sqlConnect->query("SELECT * FROM posts_questions_options WHERE id = '{$option_id}' AND question_id = '{$question_id}'");
	
	/* return ajax */	
	if($checkOption->num_rows == 0) { 	  	
		  return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'message' => 'This question content is unavailable, It may be deleted by the author'));
	}
	
	// select option
	if($_GET['do'] == "select") {
		// check selection
		$getSelection = $sqlConnect->query("SELECT option_id FROM questions_options_votes WHERE user_id = '{$wo['user']['user_id']}' AND question_id = '{$question_id}'");
	   
		if($getSelection->num_rows == 0) {
			
			// insert selection
			$sqlConnect->query("INSERT INTO questions_options_votes (user_id, question_id, option_id) VALUES ('{$wo['user']['user_id']}', '{$question_id}', '{$option_id}')");
			
			// update option
			$sqlConnect->query("UPDATE posts_questions_options SET votes = votes + 1 WHERE id = '{$option_id}'");
			
			// update question
			$sqlConnect->query("UPDATE posts_questions SET question_votes = question_votes + 1 WHERE question_id = '{$question_id}'");        
			
			/* insert notification */
			if($wo['user']['user_id'] != $author_id){		
				   $url_post = 'index.php?link1=post&id='.$post_id;
				   $sqlConnect->query("INSERT INTO 
				   Wo_Notifications(notifier_id, recipient_id, post_id, type, url,time) 
				   VALUES ('{$wo['user']['user_id']}', '{$author_id}', '{$post_id}', 'question', '{$url_post}', ".time()." )");
			}
			
		}else {		
			// get old option
			$oldOption = $getSelection->fetch_array(MYSQLI_ASSOC);
			if($oldOption['option_id'] == $option_id) { exit; }
			
			// update selection
			$sqlConnect->query("UPDATE questions_options_votes SET option_id = '{$option_id}' WHERE user_id = '{$wo['user']['user_id']}' AND question_id = '{$question_id}'");
			
			// update option
			$sqlConnect->query("UPDATE posts_questions_options SET votes = votes + 1 WHERE id = '{$option_id}'");
			$sqlConnect->query("UPDATE posts_questions_options SET votes = IF(votes=0,0,votes-1) WHERE id = '{$oldOption['option_id']}'");
		}
	
	// unselect option
	}elseif ($_GET['do'] == "unselect") {
		
		// delete selection
		$sqlConnect->query("DELETE FROM questions_options_votes WHERE user_id = '{$wo['user']['user_id']}' AND question_id = '{$question_id}' AND option_id = '{$option_id}'") || exit;
		
		// check affected rows
		if($sqlConnect->affected_rows > 0) {
		
			// update option
			$sqlConnect->query("UPDATE posts_questions_options SET votes = IF(votes=0,0,votes-1) WHERE id = '{$option_id}'");
		
			// update question
			$sqlConnect->query("UPDATE posts_questions SET question_votes = IF(question_votes=0,0,question_votes-1) WHERE question_id = '{$question_id}'");
		
			// delete notification
			if($wo['user']['user_id'] != $author_id) {		
				$sqlConnect->query("DELETE FROM Wo_Notifications WHERE notifier_id = '{$wo['user']['user_id']}' AND type = 'question' AND recipient_id = '{$author_id}'");
			}
		}
	}

	/* return ajax */		
	return_json(array('result' => 1, 'html' => $html));
	}

	
	   if($f == 'question_who_votes'){
		  // check page parameters
		  if(!isset($_GET['question_id'])) { 
			 /* return ajax */		
			 return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'content' => 'This Question no exist'));
		  }
		  if(!isset($_GET['option_id'])) { 
			 /* return ajax */		
			 return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'content' => 'This Option no exist'));
		  }
		  $question_id = Wo_Secure($_GET['question_id']);
		  $option_id = Wo_Secure($_GET['option_id']);
		  
		  // check if valid option
		  $checkOption = $sqlConnect->query("SELECT * FROM posts_questions_options WHERE id = '{$option_id}' AND question_id = '{$question_id}'");
	
		  if($checkOption->num_rows == 0) { 
			 /* return ajax */		
			 return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'content' => 'This Question content is unavailable, It may be deleted by the author'));
		  }
		   $getUsers = $sqlConnect->query("SELECT U.* FROM questions_options_votes AS P INNER JOIN Wo_Users AS U ON P.user_id = U.user_id WHERE P.question_id = '{$question_id}' AND P.option_id = '{$option_id}'");
	
		  if($getUsers->num_rows == 0){ 
			 /* return ajax */		
			 return_json(array('status' => 200, 'title' => 'Error', 'btn' =>'', 'content' => 'This questions no contain votes in this moment'));
		  }
		  while($row = $getUsers->fetch_assoc()) {
			 if($row['avatar']!= ''){ $row['user_picture'] =  $row['avatar']; } else { $row['user_picture'] = 'upload/photos/d-avatar.jpg'; }
			 if($row['first_name']!= ''){ $row['user_fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['user_fullname'] = $row['username']; }
			 $usersWho[] = $row;
		  }
	
			/* variables */
			$users = $usersWho;
			$usersWho = $usersWho;
			$usersWhoNums = count($usersWho);
			$view = 'select';
			$get = 'question_votes';
			$id = $option_id;
			$html = '';

		   if(count($users) > 0 ){ 	
			  ob_start();
			  foreach($users as $_user){ 
				 include './themes/' . $wo['config']['theme'] . '/layout/plugins/__feed_user.phtml';
			  } 
			  $html = ob_get_contents();
			  ob_end_clean();
		   }
		 /* return ajax */		
		 return_json(array('result' => 1, 'html' => '', 'title' => $wo['lang']['plugin_question_people_who_vote_this'], 'content' => $html, 'btn' =>''));
	   }
	
	
	if( $f== "invite_to_post" ){
	   $html = Wo_LoadPage('plugins/question/friend_invite');
	   /* return ajax */		
	   return_json(array('result' => 1, 'html' => '', 'title' => 'Friend Select', 'content' => $html, 'btn' =>''));
	}
	
	
	if($f == 'new_friend_invite'){
		$post_id = ( isset($_POST['post_id']) ? $_POST['post_id'] : ( isset($_GET['post_id']) ? $_GET['post_id'] : '0' ) );
		$friends = ( isset($_POST['friend']) ? $_POST['friend'] : ( isset($_GET['friend']) ? $_GET['friend'] : array() ) );
		$post_id = Wo_Secure($post_id);
		$friend_list = implode(',', $friends);
		if(!empty($friend_list) && count($friend_list)>0){ 	 
		   //$friend_list = explode(", ", $friends);
		   $resource = $sqlConnect->query(" SELECT `user_id`, `username`, `first_name`, `last_name`, `email` FROM `Wo_Users` WHERE user_id IN ($friend_list) ");	
		   
		   if($resource->num_rows > 0){        
			 
			  /* prepare email */
			  $subject = "New invitation to vote a question in ".$wo['config']['siteTitle'];
			  $body  = ucwords($wo['user']['username']).", need of you vote in this post";
			  $body .= "\r\n\r\nPlease follow this link:";
			  $body .= "\r\n\r\n".$wo['config']['site_url']."/post/".$post_id."/";
			
			  while($rows = $resource->fetch_assoc()){
				 /* insert notification */
				 if($wo['user']['user_id'] != $rows['user_id']) {	
					$url_post = 'index.php?link1=post&id='.$post_id;
					$sqlConnect->query("INSERT INTO Wo_Notifications(notifier_id, recipient_id, post_id, type, url,time) 
					VALUES ('{$wo['user']['user_id']}', '{$rows['user_id']}', '{$post_id}', 'question_invite', '{$url_post}', ".time()." )");
				 }		  
				 if($wo['system']['question_enable_email_invite_to_friends'] == 1){
					$email =  $rows['email'];		
					 /* send using mail() */
					if(!send_email($email, $subject, $body)){ 
					   return_json( array('result' => 0, 'title' => 'Error', 'btn' =>'', 'content' => 'No can invite friends in this moment')  );
					}	   
				 } 		 
			  }
			  
			  return_json( array('result' => 1, 'title' => 'Ready', 'btn' =>'', 'message' => "Done, invitation was sending.") );
		   }
		   
		} else { return_json( array('result' => 0, 'title' => 'Error', 'btn' =>'', 'content' => 'Select minime to a friend')  ); }
}
?>