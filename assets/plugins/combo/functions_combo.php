<?php
/* functions reaction
 * @Autor : LdrMx
 */
	
/*reaction funtion*/
function isReacted($r = '', $user_id, $post_id) {
	global $sqlConnect;
	
	if(!$user_id ){ return false; }		
	$query = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}' AND user_id = '{$user_id}' LIMIT 1");
	if (empty($r)){ $query = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}' AND user_id = '{$user_id}' LIMIT 1"); }
	else { $query = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}' AND user_id = '{$user_id}' AND reaction='{$r}' LIMIT 1"); }
	if ($query->num_rows == 1){	$fetch = $query->fetch_assoc(); return $fetch['reaction']; }
}


function putReaction($r, $user_id, $post_id){
	global $sqlConnect, $wo;
	if (!$wo['loggedin'] == true && (empty($post_id) || !is_numeric($post_id) || $post_id < 1)) {
		return false;
	}
	$logged_user_id = Wo_Secure($wo['user']['user_id']);
	$post = Wo_PostData($post_id);
	$type2 = '';
	if (isset($post['postText']) && !empty($post['postText'])) {
		$text = substr($post['postText'], 0, 10) . '..';
	}
	if (isset($post['postYoutube']) && !empty($post['postYoutube'])) {
		$type2 = 'post_youtube';
	} elseif (isset($post['postSoundCloud']) && !empty($post['postSoundCloud'])) {
		$type2 = 'post_soundcloud';
	} elseif (isset($post['postVine']) && !empty($post['postVine'])) {
		$type2 = 'post_vine';
	} elseif (isset($post['postFile']) && !empty($post['postFile'])) {
		if (strpos($post['postFile'], '_image') !== false) {
			$type2 = 'post_image';
		} else if (strpos($post['postFile'], '_video') !== false) {
			$type2 = 'post_video';
		} else if (strpos($post['postFile'], '_avatar') !== false) {
			$type2 = 'post_avatar';
		} else if (strpos($post['postFile'], '_sound') !== false) {
			$type2 = 'post_soundFile';
		} else if (strpos($post['postFile'], '_cover') !== false) {
			$type2 = 'post_cover';
		} else if (strpos($post['postFile'], '_cover') !== false) {
			$type2 = 'post_cover';
		} else {
			$type2 = 'post_file';
		}
	}
	
	if (empty($r)){ $r = "like"; }	  	
	if (isReacted($r, $user_id, $post_id)){
		/* delete of post like */
		$sqlConnect->query("DELETE FROM Wo_Likes WHERE post_id='{$post_id}' AND user_id = '{$user_id}' LIMIT 1");		
		/* update post likes counter */
		//$sqlConnect->query("UPDATE Wo_Posts SET likes = IF(likes=0,0,likes-1) WHERE post_id = '{$post_id}' LIMIT 1");
		/* delete notification */
		$sqlConnect->query("DELETE FROM Wo_Notifications WHERE notifier_id = '{$user_id}' AND post_id = '{$post_id}'");	
		
		/* plugin points */
		if(!empty($wo['plugin_list']['plugin_actived']) && in_array('Points', $wo['plugin_list']['plugin_actived']) && !empty($wo['system']['userpoints_enable']) && $wo['system']['userpoints_enable'] == 1){
				update_points( $wo['user']['user_id'], 'dislike' );
		}
			
	} else {		
		$get_exist = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}' AND user_id='{$user_id}' LIMIT 1");
		$count_r = $get_exist->num_rows;
		/* delete of post like */
		$sqlConnect->query("DELETE FROM Wo_Likes WHERE post_id='{$post_id}' AND user_id = '{$user_id}' AND reaction<> '{$r}'");			
		/* update post likes counter */
		//$sqlConnect->query("UPDATE Wo_Posts SET likes = IF(likes=0,0,likes-1) WHERE post_id = '{$post_id}' LIMIT 1");
		/* delete notification */
		$sqlConnect->query("DELETE FROM Wo_Notifications WHERE notifier_id = '{$user_id}' AND post_id = '{$post_id}'");			
		/* insert like */
		$sql_query_two = $sqlConnect->query("INSERT INTO Wo_Likes (user_id,post_id,reaction) VALUES ('{$user_id}','{$post_id}','{$r}')");			
		/* update post likes counter */
		//$sqlConnect->query("UPDATE Wo_Posts SET likes = likes+1 WHERE post_id = '{$post_id}' LIMIT 1");

		if($count_r < 1){
			if(!empty($wo['plugin_list']['plugin_actived']) && in_array('Points', $wo['plugin_list']['plugin_actived']) && !empty($wo['system']['userpoints_enable']) && $wo['system']['userpoints_enable'] == 1){
				update_points( $wo['user']['user_id'], 'like' );
			}
		}
		
		$resource = $sqlConnect->query("SELECT reaction_filetype FROM reactions WHERE reaction_key = '{$r}' LIMIT 1");
		$file = $resource->fetch_assoc();	

			if ($type2 != 'post_avatar') {
				$activity_data = array(
					'post_id' => $post_id,
					'user_id' => $logged_user_id,
					'post_user_id' => $post['user_id'],
					'activity_type' => 'reaction_post',
					'app_src' => 'reaction_'.$r.'.'.$file['reaction_filetype']
				);
				
				$add_activity  = Register_Activity($activity_data);
				
			}
			if($post['user_id'] != $wo['user']['user_id']){
				$notification_data_array = array(
					'recipient_id' => $post['user_id'],
					'post_id' => $post_id,
					'type' => 'reaction_post',
					'text' => '',
					'type2' => $type2,
					'url' => 'index.php?link1=post&id=' . $post_id,
					'app_src' => 'reaction_'.$r.'.'.$file['reaction_filetype']
				);
				Register_Notification($notification_data_array);
			}
	}
	return true;
}


function numReactions($r = "", $post_id){ 
	global $sqlConnect;
	$allowed_reactions = array('like', 'love', 'haha', 'wow', 'sad', 'angry');
	if (in_array($r, $allowed_reactions))
	   { $query = $sqlConnect->query("SELECT COUNT(post_id) AS count FROM Wo_Likes WHERE post_id='{$post_id}' AND reaction='$r'"); }
		 else
	   { $query = $sqlConnect->query("SELECT COUNT(post_id) AS count FROM Wo_Likes WHERE post_id='{$post_id}'"); }		   
		$fetch = $query->fetch_assoc();	  
  return $fetch['count'];
}


function getReactions($r="", $post_id){ 
	global $sqlConnect;
			
	$allowed_reactions = array('like', 'love', 'haha', 'wow', 'sad', 'angry');
	$get = array();
	$query = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}'");
	
	if (in_array($r, $allowed_reactions)){ 
	   $query = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}' AND reaction='{$r}'");
	} else { 
	   $query = $sqlConnect->query("SELECT * FROM Wo_Likes WHERE post_id='{$post_id}'");
	}
	
	if($query->num_rows > 0){ 
		while($fetch = $query->fetch_assoc()){ 
			$get[$fetch['user_id']] = $fetch['reaction'];
		}
	}   
	return $get;
}


function getTopReactions($post_id, $l=5){	
	global $sqlConnect;
	$l = (int) $l;
	$g = array();
	$query = $sqlConnect->query("SELECT DISTINCT reaction FROM Wo_Likes WHERE post_id='{$post_id}' LIMIT $l");
	while($fetch = $query->fetch_assoc()){
		$g[] = $fetch['reaction'];
	}
	return $g;
}


function getReactButtonTemplate($r = '', $user_id, $post_id){ 
	global $wo;
	if ($reaction = isReacted($r, $user_id, $post_id)){ 
		 ob_start();
		 include "upload/reaction/reaction-".$reaction."-button.php";
		 $html = ob_get_contents();
		 ob_end_clean(); ob_get_level();
		 $html = preg_replace('/\s+/',' ',$html);
		 return $html;
	} 
	else {
		return '<span class="text-link story-like-btn opt active-likes" onclick="set_reaction($(this), \'like\', '.$post_id.');" title="'.$wo['lang']['like'].'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg> '.$wo['lang']['like'].'</span>';
	}
}


function format_like( $post_id ){
	global $wo, $sqlConnect;
	$value = '<span class="u_i_1"></span>';
	$sql = "
		SELECT L.*, U.user_id, U.first_name, U.last_name, U.username , U.avatar 
		FROM Wo_Likes AS L LEFT JOIN Wo_Users AS U 
		ON L.user_id = U.user_id 
		WHERE L.post_id='{$post_id}'
	";
	$query = $sqlConnect->query($sql);	
	$resource = $sqlConnect->query($sql);	
	   	
	$user_ids = array();
	while($row = $query->fetch_assoc()){
		$user_ids[] = $row['user_id'];
	}
	
	$user_arr = array();
	while($row = $resource->fetch_assoc()){
			//$user_arr[$row['user_id']] = $row;
		if($row['avatar']!= ''){ $row['user_picture'] =  $row['avatar']; } else { $row['user_picture'] = 'upload/photos/d-avatar.jpg'; }
		if($row['first_name']!= ''){ $row['user_fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['user_fullname'] = $row['username']; }
		if($row['user_id'] != $wo['user']['user_id']){
			$user_arr[$row['user_id']] = $row;
		}
	}
	//echo json_encode($user_arr);
	$count = count($user_ids);
	$cur_user_likes = ( $wo['loggedin'] ) ? in_array($wo['user']['user_id'], $user_ids) : false;

	if ( $count == 1 )
	{
		if ( $cur_user_likes )
		{
			$owner_str = '<span class="u_i_1">'.$wo['lang']['plugin_combo_you'];
			$value = $owner_str.' '.$wo['lang']['plugin_combo_like_this'].'.</span>';
		}
		else
		{
			$owner_id = $user_ids[0];
			$owner = $user_arr[$owner_id];
			$owner_url = $wo['config']['site_url'].'/'.$owner['username'];
			$owner_str = '<a href="' . $owner_url . '">' . $owner['user_fullname'] . '</a>';

			$value = '<span class="u_i_1"></span>'.$owner_str.' '.$wo['lang']['plugin_combo_like_this'].'.';
		}
	}
	elseif ( $count == 2 )
	{
		if ( $user_ids[0] == $wo['user']['user_id'] )
		{
			$other_user_id = $user_ids[1];
		}
		elseif ( $user_ids[1] == $wo['user']['user_id'] )
		{
			$other_user_id = $user_ids[0];
		}

		if ( $cur_user_likes )
		{
			$first_str = '<span class="u_i_1">'.$wo['lang']['plugin_combo_you'];
			$second_user = $user_arr[$other_user_id];
			$second_user_url = $wo['config']['site_url'].'/'.$second_user['username'];
			$second_str = '<a href="' . $second_user_url . '">' . $second_user['user_fullname']. '</a>';
			$owners = $first_str.' '.$wo['lang']['plugin_combo_and'].' </span>'.$second_str;
		}
		else
		{
			$first_id = $user_ids[0];
			$first_user = $user_arr[$first_id];
			$first_user_url = $wo['config']['site_url'].'/'.$first_user['username'];
			$first_str = '<a href="' . $first_user_url . '">' . $first_user['user_fullname'] . '</a>';

			$second_id = $user_ids[1];
			$second_user = $user_arr[$second_id];
			$second_user_url = $wo['config']['site_url'].'/'.$second_user['username'];
			$second_str = '<a href="' . $second_user_url . '">' . $second_user['user_fullname'] . '</a>';
			$owners = $first_str.' '.$wo['lang']['plugin_combo_and'].' '.$second_str;
		}

		//$owners = $first_str.' '.$wo['lang']['plugin_combo_and'].' '.$second_str;
		$value = $owners.' '.$wo['lang']['plugin_combo_like_this'].'.';
	}
	elseif ( $count > 2 )
	{
		$owners = '<span class="u_i_2">'.$count.'</span> '.$wo['lang']['plugin_combo_peoples'];
		$value = '<span class="u_i_1"></span>'.$owners.' '.$wo['lang']['plugin_combo_like_this'].'.';
	}

	return array( 'like' => $cur_user_likes, 'value' => $value, 'count' => $count );
}

	
function getReactionActivityTemplate($r = '', $post_id){	
  global $wo, $sqlConnect;
  
	  //$story_likes_num = numReactions($r, $post_id);
	  //$story_likes_num = numReactions('', $post_id);
	  $story_likes_num = format_like( $post_id );
	  //echo json_encode($story_likes_num); 
	  //$story_likes_num['value']
	  $list_reactions = ''; 				
	
	  foreach(getTopReactions($post_id) as $k => $v){
		 $resource = $sqlConnect->query("SELECT reaction_filetype FROM reactions WHERE reaction_key = '{$v}' LIMIT 1");
		 $file = $resource->fetch_assoc();	 
		 $list_reactions .= '<img src="'.$wo['config']['site_url'].'/upload/reaction/reaction_'.$v.'.'.$file['reaction_filetype'].'" style="margin-left:-6px; width:18px;" data-r="'.$v.'">';
	  }
			
	  $Html_react = '<span class="like-activity activity-btn"><span class="r_img_old">'.$list_reactions.'</span><span class="r_img"></span> <span class="stat-item post-like-status" id="likes" data-like="'.$story_likes_num['like'].'" data-count="'.$story_likes_num['count'].'">'.$story_likes_num['value'].'</span></span>';		
	
	return $Html_react;		
}


/*list reaction - no use*/ 
function getReactionsTemplate($r="", $post_id){  
	global $wo;		
	 ob_start();
	 include "upload/reaction/pr_view-reactions.php";
	 $html = ob_get_contents();
	 ob_end_clean(); ob_get_level();
	 $html = str_replace("\t",'',$html);
	 $html = str_replace("\r",'',$html);
	 $html = str_replace("\n",'',$html);
	 return $html; 
}
/*reaction function*/ 



/* welcome */
function combo_welcome($user_time = 0){
	global $wo;   
	$url_info_json = @file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=09b69e5b9f5daa0badb11b65e066ad133f0824683d2e4f99db1d24631edddc6d&ip=".$_SERVER['REMOTE_ADDR']."&format=json");
	$url_info = ( $url_info_json ) ? json_decode($url_info_json, true) : array();     
	$timeZone = isset($url_info['timeZone']) ? $url_info['timeZone'] : $user_time;
	$timeZone = str_replace(":",'.',$timeZone);
	if(is_numeric($timeZone)) {
		$user_time = $timeZone;
	}
	$hour_normal = date("H") + $user_time;
	//echo json_encode($user_time); 
	if($hour_normal < 0){
		 return $wo['lang']['plugin_combo_good_evening'];
	}elseif($hour_normal == 0){
		 return $wo['lang']['plugin_combo_good_nigth'];
	}elseif($hour_normal >= 1 && $hour_normal < 12){
		 return $wo['lang']['plugin_combo_good_morning'];
	}elseif($hour_normal >= 12 && $hour_normal < 20){
		 return $wo['lang']['plugin_combo_good_afternoon'];
	}elseif($hour_normal >= 20){
		 return $wo['lang']['plugin_combo_good_evening'];
	}
}


/*list tag friend */
function tags_in_post($post_id){
	global $sqlConnect;
	if(!$post_id){ return false; } 
	$resource = $sqlConnect->query("
		SELECT T.*, U.* 
		FROM posts_tags AS T LEFT JOIN Wo_Users AS U 
		ON T.user_id = U.user_id 
		WHERE T.post_id='{$post_id}'
	");
	$CountPeoples = $resource->num_rows;

	if($CountPeoples > 0){
		$counts_P = $CountPeoples-1;
		while ($row = $resource->fetch_assoc()){ 
				if($row['avatar']!= ''){ $row['user_picture'] =  $row['avatar']; } else { $row['user_picture'] = 'upload/photos/d-avatar.jpg'; }
				if($row['first_name']!= ''){ $row['user_fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['user_fullname'] = $row['username']; }
			$peoples[] = $row;
		}
		if($CountPeoples == 1){ 
			foreach ( $peoples as $key => $people){ 
				$text_return = " with ".'<span class="user-popover" data-type="user" data-id="'.$people['user_id'].'">
				<a class="profile_Link  text-link" href="'.$people['username'].'">'.$people['user_fullname'].'</a>
				</span>';
			}
		}elseif($CountPeoples == 2){
			$text_return .= " with "; 
			foreach ( $peoples as $key => $people){ if($key == 1){ $text_return .= ' '.$wo['lang']['plugin_combo_and'].' ';} 
				$text_return .= '<span class="user-popover" data-type="user" data-id="'.$people['user_id'].'">
				<a class="profile_Link text-link" href="'.$people['username'].'">'.$people['user_fullname'].'</a>
				</span>';
			}
		}elseif($CountPeoples > 2){ 
			$text_return .= " with "; 
			foreach ( $peoples as $key => $people){
				if($key == 0){ 
					$text_return .= '<span class="user-popover" data-type="user" data-id="'.$people['user_id'].'">
					<a class="profile_Link text-link" href="'.$people['username'].'">'.$people['user_fullname'].'</a>
					</span> '.$wo['lang']['plugin_combo_and'].' <a data-hover="tooltip" aria-label=" ';
				} 
				if($key != 0){ 
					$text_return .= ' '.$people['user_fullname'];
				}
			} 
			$text_return .= '" data-tooltip-position="below" data-toggle="modal" data-url="'.$wo['config']['site_url'].'/plugin_requests.php?f=combo&s=who_tag&post_id='.$post_id.'">'.$counts_P.' people more</a> ';
		}
		//echo json_encode($text_return);
		return $text_return;
	}	
}


	
function who_likes($post_id, $offset = 0) {
	global $sqlConnect;
	$rows = array();
	$offset *= 15;
	$get_posts = $sqlConnect->query("SELECT L.*, U.* FROM Wo_Likes AS L INNER JOIN Wo_Users AS U ON L.user_id=U.user_id WHERE L.post_id='{$post_id}'");
	if($get_posts->num_rows > 0) {
		while($row = $get_posts->fetch_assoc()) {
				$resource = $sqlConnect->query("SELECT reaction_filetype FROM reactions WHERE reaction_key='{$row['reaction']}' LIMIT 1");
				if($resource->num_rows > 0){ $get_info = $resource->fetch_assoc(); $row['reaction_filetype'] = $get_info['reaction_filetype']; } else { $row['reaction'] = 'like'; $row['reaction_filetype'] = 'png'; }
				if($row['avatar']!= ''){ $row['user_picture'] =  $row['avatar']; } else { $row['user_picture'] = 'upload/photos/d-avatar.jpg'; }
				if($row['first_name']!= ''){ $row['user_fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['user_fullname'] = $row['username']; }
				$rows[] = $row;
		}
	}
	return $rows;
}
?>