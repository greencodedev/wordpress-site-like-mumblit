<?php
   if($f == 'autocomplete'){
      // valid inputs
		 $limit = ( isset($_POST['limit']) ? $_POST['limit'] : ( isset($_GET['limit']) ? $_GET['limit'] : 10 ) );	
		 $query = ( isset($_POST['query']) ? $_POST['query'] : ( isset($_GET['query']) ? $_GET['query'] : '' ) );
		 $type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : 'share' ) );
		 $random = ( isset($_POST['random']) ? $_POST['random'] : ( isset($_GET['random']) ? $_GET['random'] : false ) );
		 $skipped_ids = ( isset($_POST['skipped_ids']) ? $_POST['skipped_ids'] : ( isset($_GET['skipped_ids']) ? $_GET['skipped_ids'] : '' ) );	
		 $skipped_array =  array();
		// valid inputs
		/* if both (query & skipped_ids) not set */
		if(!isset($query)) {
			return false;
		} else {
			if(!empty($skipped_ids)){
				/* if skipped_ids not array */
				$skipped_ids = json_decode($skipped_ids);
				if(!is_array($skipped_ids)) {
					return false;
				}
				/* skipped_ids must contain numeric values only */
				$skipped_array = array_filter($skipped_ids, 'is_numeric');
			}
		}
		 
      if($s == 'users'){	 
	     // autocomplete
		     // initialize the return array
		     $return = array();
		     // get users
			 $users = array();
			 
           	 /* get users */
			 /*get user input and limits*/
  		     $input = strtolower( $query );
  		     $len = strlen($input);
			 

			if(count($skipped_array) > 0) {
				/* make a skipped list (including the viewer) */
				$skipped_list = implode(',', $skipped_array);
				/* get users */			
				$sql = "SELECT `user_id`, `username`, `first_name`, `last_name`, `gender`, `avatar` FROM " . T_USERS . " WHERE user_id NOT IN ('{$skipped_list}') AND (SUBSTRING(username, 1, $len)='$input' OR SUBSTRING(first_name, 1, $len)='$input' OR SUBSTRING(last_name, 1, $len)='$input' )  LIMIT $limit";
			} else {
				$sql = "SELECT `user_id`, `username`, `first_name`, `last_name`, `gender`, `avatar` FROM " . T_USERS . " WHERE (SUBSTRING(username, 1, $len)='$input' OR SUBSTRING(first_name, 1, $len)='$input' OR SUBSTRING(last_name, 1, $len)='$input' )  LIMIT $limit";
			}
			$resource = $sqlConnect->query($sql);
	    	if($resource->num_rows > 0) {
            	 while($row = $resource->fetch_assoc()) {
				     $row['id'] = $row['user_id'];
                	 if($row['avatar']!= ''){ $row['photo'] =  $row['avatar']; } else { $row['photo'] = 'upload/photos/d-avatar.jpg'; }
			         if($row['first_name']!= ''){ $row['fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['fullname'] = $row['username']; }			
                	 $users[] = $row;
           	     }
             }
			 
			 			
		     if(count($users) > 0) {
		  	   /* return */
			  ob_start();
	  	      include './themes/'.$wo['config']['theme'].'/layout/plugins/admin/ajax_autocomplete.phtml';
	          $html = ob_get_contents();
              ob_end_clean(); 
			  $html = str_replace("\t",'',$html);
			  $html = str_replace("\r",'',$html);
			  $html = str_replace("\n",'',$html);
			  $return['suggest'] = $html;
		     }
		     // return & exit
		     return_json($return);
	  }
	  if($s == 'friend'){	     	      
	     // autocomplete
		     // initialize the return array
		     $return = array();
		     // get users
			 $users = array();
			 
           	 /* get users */
			 /*get user input and limits*/
  		     $input = strtolower( $query );
  		     $len = strlen($input);
	
  		     /*get info of database of friends*/
			if(count($skipped_array) > 0) {
				/* make a skipped list (including the viewer) */
				$skipped_list = implode(',', $skipped_array);				
				$sql = "SELECT Wo_Followers.*, user_id, username, first_name, last_name, avatar, gender FROM Wo_Followers LEFT JOIN Wo_Users ON (Wo_Followers.following_id = Wo_Users.user_id AND Wo_Followers.following_id != '{$wo['user']['user_id']}') OR (Wo_Followers.follower_id = Wo_Users.user_id AND Wo_Followers.follower_id != '{$wo['user']['user_id']}') WHERE user_id NOT IN ('{$skipped_list}') AND Wo_Followers.active = '1' AND 
  (following_id = '{$wo['user']['user_id']}' 
  /* OR follower_id = '{$wo['user']['user_id']}'*/
  )
  AND (SUBSTRING(username, 1, $len)='$input' OR SUBSTRING(first_name, 1, $len)='$input' OR SUBSTRING(last_name, 1, $len)='$input') LIMIT $limit";
			} else {
				$sql = "SELECT Wo_Followers.*, user_id, username, first_name, last_name, avatar, gender FROM Wo_Followers LEFT JOIN Wo_Users ON (Wo_Followers.following_id = Wo_Users.user_id AND Wo_Followers.following_id != '{$wo['user']['user_id']}') OR (Wo_Followers.follower_id = Wo_Users.user_id AND Wo_Followers.follower_id != '{$wo['user']['user_id']}') WHERE  Wo_Followers.active = '1' AND 
  (following_id = '{$wo['user']['user_id']}' 
  /* OR follower_id = '{$wo['user']['user_id']}'*/
  )
  AND (SUBSTRING(username, 1, $len)='$input' OR SUBSTRING(first_name, 1, $len)='$input' OR SUBSTRING(last_name, 1, $len)='$input') LIMIT $limit";
			}
  		     
  		     $resource = $sqlConnect->query($sql);

	    	 if($resource->num_rows > 0) {
            	 while($row = $resource->fetch_assoc()) {
				     $row['id'] = $row['user_id'];
                	 if($row['avatar']!= ''){ $row['photo'] =  $row['avatar']; } else { $row['photo'] = 'upload/photos/d-avatar.jpg'; }
			         if($row['first_name']!= ''){ $row['fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['fullname'] = $row['username']; }			
                	 $users[] = $row;
           	     }
             }
			 
			 
		     if(count($users) > 0) {
		  	   /* return */
			  ob_start();
	  	      include './themes/' . $wo['config']['theme'] . '/layout/plugins/admin/ajax_autocomplete.phtml';
	          $html = ob_get_contents();
              ob_end_clean(); 
			  $html = str_replace("\t",'',$html);
			  $html = str_replace("\r",'',$html);
			  $html = str_replace("\n",'',$html);
			  $return['suggest'] = $html;
		     }
		     // return & exit
		     return_json($return);
	  }
	  if($s == 'page'){ 
	     // autocomplete
		     // initialize the return array
		     $return = array();
		     // get users
			 $users = array();
			 
           	 /* get users */
			 /*get user input and limits*/
  		     $input = strtolower( $query );
  		     $len = strlen($input);
	
  		     /*get info of database of page*/
  		     $sql = "SELECT `page_id`, `user_id`, `page_name`, `page_title`, `avatar` FROM Wo_Pages WHERE user_id = '{$wo['user']['user_id']}' AND (SUBSTRING(page_name, 1, $len)='$input' OR SUBSTRING(page_title, 1, $len)='$input') LIMIT $limit";
  		     $resource = $sqlConnect->query($sql);

	    	 if($resource->num_rows > 0) {
            	 while($row = $resource->fetch_assoc()) {
				     $row['id'] = $row['page_id'];
                	 if($row['avatar']!= ''){ $row['photo'] =  $row['avatar']; } else { $row['photo'] = 'upload/photos/d-page.jpg'; }
			         if($row['page_title']!= ''){ $row['fullname'] =  $row['page_title']; } else { $row['fullname'] = $row['page_name']; }			
                	 $users[] = $row;
           	     }
             }
			 
			 
		     if(count($users) > 0) {
		  	   /* return */
			  ob_start();
	  	      include './themes/' . $wo['config']['theme'] . '/layout/plugins/admin/ajax_autocomplete.phtml';
	          $html = ob_get_contents();
              ob_end_clean(); 
			  $html = str_replace("\t",'',$html);
			  $html = str_replace("\r",'',$html);
			  $html = str_replace("\n",'',$html);
			  $return['suggest'] = $html;
		     }
		     // return & exit
		     return_json($return);
	  }
	  if($s == 'group'){	 
	     // autocomplete
		     // initialize the return array
		     $return = array();
		     // get users
			 $users = array();
			 
           	 /* get users */
			 /*get user input and limits*/
  		     $input = strtolower( $query );
  		     $len = strlen($input);
	
  		     /*get info of database of group*/
  		     $sql = "SELECT `id`, `user_id`, `group_name`, `group_title`, `avatar` FROM Wo_Groups WHERE user_id = '{$wo['user']['user_id']}' AND (SUBSTRING(group_name, 1, $len)='$input' OR SUBSTRING(group_title, 1, $len)='$input') LIMIT $limit";
  		     $resource = $sqlConnect->query($sql);

	    	 if($resource->num_rows > 0) {
            	 while($row = $resource->fetch_assoc()) {
				     $row['id'] = $row['id'];
                	 if($row['avatar']!= ''){ $row['photo'] =  $row['avatar']; } else { $row['photo'] = 'upload/photos/d-group.jpg'; }
			         if($row['group_title']!= ''){ $row['fullname'] =  $row['group_title']; } else { $row['fullname'] = $row['group_name']; }			
                	 $users[] = $row;
           	     }
             }
			 
			 
		     if(count($users) > 0) {
		  	   /* return */
			  ob_start();
	  	      include './themes/' . $wo['config']['theme'] . '/layout/plugins/admin/ajax_autocomplete.phtml';
	          $html = ob_get_contents();
              ob_end_clean(); 
			  $html = str_replace("\t",'',$html);
			  $html = str_replace("\r",'',$html);
			  $html = str_replace("\n",'',$html);
			  $return['suggest'] = $html;
		     }
		     // return & exit
		     return_json($return);
	  }
	  if($s == 'event'){
	  	         // valid inputs
		 $limit = ( isset($_POST['limit']) ? $_POST['limit'] : ( isset($_GET['limit']) ? $_GET['limit'] : 5 ) );	
		 $query = ( isset($_POST['query']) ? $_POST['query'] : ( isset($_GET['query']) ? $_GET['query'] : '' ) );	 
	     // autocomplete
		     // initialize the return array
		     $return = array();
		     // get users
			 $users = array();
			 
           	 /* get users */
			 /*get user input and limits*/
  		     $input = strtolower( $query );
  		     $len = strlen($input);
	
  		     /*get info of database of events*/
  		     $sql = "SELECT `id`, `poster_id`, `name`, `cover` FROM Wo_Events WHERE poster_id = '{$wo['user']['user_id']}' AND (SUBSTRING(name, 1, $len)='$input') LIMIT $limit";
  		     $resource = $sqlConnect->query($sql);

	    	 if($resource->num_rows > 0) {
            	 while($row = $resource->fetch_assoc()) {
				     $row['id'] = $row['id'];
                	 if($row['cover']!= ''){ $row['photo'] =  $row['cover']; } else { $row['photo'] = 'upload/photos/d-event.jpg'; }
			         if($row['name']!= ''){ $row['fullname'] =  $row['name']; } else { $row['fullname'] = $row['name']; }			
                	 $users[] = $row;
           	     }
             }
			 
			 
		     if(count($users) > 0) {
		  	   /* return */
			  ob_start();
	  	      include './themes/' . $wo['config']['theme'] . '/layout/plugins/admin/ajax_autocomplete.phtml';
	          $html = ob_get_contents();
              ob_end_clean(); 
			  $html = str_replace("\t",'',$html);
			  $html = str_replace("\r",'',$html);
			  $html = str_replace("\n",'',$html);
			  $return['suggest'] = $html;
		     }
		     // return & exit
		     return_json($return);
	  }
	  if($s == 'new_users'){
		// get users
		$users = array();		
		$suggest = Wo_UserSug(7);
		
		if(count($suggest) > 0) {
			foreach($suggest as $row) {
				$row['id'] = $row['user_id'];
				if($row['avatar']!= ''){ $row['photo'] =  $row['avatar']; } else { $row['photo'] = 'upload/photos/d-avatar.jpg'; }
				if(!empty($row['first_name'])){ $row['fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['fullname'] = $row['name']; }			
				$users[] = array(
				'user_id'=>$row['user_id'],
				'user_picture'=>$row['avatar'],
				'user_name'=>$row['username'],
				'full_name'=>$row['fullname'],
				'btn' => '<div class="user-follow-btn"><div class="user-follow-button">'.Wo_GetFollowButton($row['user_id']).'</div></div>'
				);
			}
		}
		// return & exit
		return_json(array('result' => 1,'count' => count($users), 'fof' => $users,'message' => 'Suggest Friend'));
	  }
	  
   }
   
   
/* PLUGINS GENERAL */
if($f == 'admin_plugins'){

	$is_admin = Wo_IsAdmin();
	$is_moderoter = Wo_IsModerator();
	if ($is_admin === false && $is_moderoter === false) {
		return_json( array('error' => true, 'message' => "You don't have the right permission to access this"));
	}

	switch ($task) {
		case 'plugin':
		
			/* prepare */
			$p_active = (isset($_POST['p_active']))? '1' : '0';
			$p_key = ( isset($_POST['p_key']) ? $_POST['p_key'] : ( isset($_GET['p_key']) ? $_GET['p_key'] : '' ) );
			$p_id = ( isset($_POST['p_id']) ? $_POST['p_id'] : ( isset($_GET['p_id']) ? $_GET['p_id'] : 0 ) );			
		
			// valid inputs
			if(!isset($p_id) || !is_numeric($p_id)) { return_json(array('error' => true, 'message' => "This no is a number")); }
						
			$p_active = Wo_Secure($p_active);	
			$p_id = Wo_Secure($p_id);
			$p_key = Wo_Secure($p_key);			
			
			$sqlConnect->query("UPDATE plugins_ldrsoft SET p_active='{$p_active}', p_key='{$p_key}' WHERE p_id='{$p_id}' LIMIT 1");
			$db_update =  $sqlConnect->affected_rows;					
			/* return */		
			if($db_update == 1){
			   return_json(array('result' => 1, 'message' => 'Done, Plugin info have been updated', 'results'=> $db_update, 'success' => true));
			} else {
			   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
			}
		break;
		
		default: 
			return_json(array('result' => 0, 'error' => true, 'message' => "This option no exist"));
		break;
	}
}


/*ADMIN BONUS SETTING*/
if($f == 'admin_bonus'){

	$is_admin = Wo_IsAdmin();
	$is_moderoter = Wo_IsModerator();
	if ($is_admin === false && $is_moderoter === false) {
		  return_json(array('error' => true, 'message' => "You don't have the right permission to access this"));
	}
	
	$task = ( isset($_POST['task']) ? $_POST['task'] : ( isset($_GET['task']) ? $_GET['task'] : '' ) );
	$val = ( isset($_POST['val']) ? $_POST['val'] : ( isset($_GET['val']) ? $_GET['val'] : array() ) );

// setting
	switch ($task) {
	   					
		 case 'setting': 
			$home_left_column = ( isset($_POST['home_left_column']) ? $_POST['home_left_column'] : ( isset($_GET['home_left_column']) ? $_GET['home_left_column'] : '1') );
			$plublisher_new = ( isset($_POST['plublisher_new']) ? $_POST['plublisher_new'] : ( isset($_GET['plublisher_new']) ? $_GET['plublisher_new'] : '1' ) );  
			
			$bonus_enable_home_left_column = Wo_Secure($home_left_column);
			$bonus_enable_plublisher_new = Wo_Secure($plublisher_new);			
			
			$sqlConnect->query("UPDATE plugins_system SET 
			bonus_enable_home_left_column = '{$bonus_enable_home_left_column}',
			bonus_enable_plublisher_new = '{$bonus_enable_plublisher_new}'
			WHERE system_id = '1' LIMIT 1");
			$db_update =  $sqlConnect->affected_rows;
			
			/* return */
			if($db_update == 1){
					   return_json(array('result' => 1, 'message' => "Done, Plugin info have been updated", 'results'=> $db_update, 'success' => true));
					} else {
					   return_json(array('result' => 0, 'message' => 'Cant update try more late!', 'results'=> $db_update, 'error' => true));
					}
			break;		
			
			default: 
			return_json(array('result' => 0, 'message' => 'This task no exist', 'error' => true));
			break;
	}

}
/////
if($f == 'delete_all'){
	// valid inputs
	if(!isset($_POST['id']) || !is_numeric($_POST['id'])) { 
	// return
	return_json(array('status' => 'error', 'message' => "This id no exist"));
	}
	$_id = Wo_Secure($_POST['id']);

	switch ($_POST['handle']) {
		case 'payment':		    
			$sqlConnect->query("DELETE FROM ads_purchased WHERE id = '{$_id}' AND user_id = '{$wo['user']['user_id']}' LIMIT 1");
			break;

		case 'report':
			$sqlConnect->query("DELETE FROM reports WHERE report_id = '{$_id}' AND user_id = '{$wo['user']['user_id']}' LIMIT 1");
			break;
	}

	// return
	return_json();
}

require_once('request_cloned.php');	
?>