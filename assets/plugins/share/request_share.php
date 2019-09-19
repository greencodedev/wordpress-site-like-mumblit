<?php
/* request
 * @autor : Pp Galvan - LdrMx
 */
$post_id = ( isset($_POST['post_id']) ? $_POST['post_id'] : ( isset($_GET['post_id']) ? $_GET['post_id'] : '0' ) );		
$post_id = Wo_Secure($post_id);
		 
		 
if($f == 'shareit'){
                
		//post info
		$get_post = $sqlConnect->query("SELECT Wo_Posts.* FROM Wo_Posts WHERE id = '{$post_id}' LIMIT 1");

		/* return ajax - no exist */
		if($get_post->num_rows == 0) { 	        		
            return_json(array('title'=> $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_error_post_no_exists'], 'btn' => ''));
		}		 
        
		//while info
		$post = $get_post->fetch_assoc();

        if($post['postType'] == "share") {
            
			$origin = check_post($post['origin_id']);            
			/* return ajax - privacy 	
			if(!$origin || $origin['postPrivacy'] != '0') {					
				return_json(array('title'=> $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_error_post_no_exists'], 'btn' => ''));
			}*/            
			$post_id = $origin['post_id'];
            $author_id = $origin['user_id'];
			
        } else {
            
			$post_id = $post['post_id'];
            $author_id = $post['user_id'];
        
		} 	
		
		//get original post   
		$wo['story'] = Wo_PostData($post_id, '', 'not_limited');

		/* return ajax - no exi */	
		if(empty($wo['story']) || Wo_PostExists($wo['story']['post_id']) === false) { 		   		
		   return_json(array('title'=> $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_error_post_no_exists'], 'btn' => ''));
		}
	
		$html =  Wo_LoadPage('plugins/share/share_post_sharer');
		/*$html = str_replace("\t",'',$html);
		$html = str_replace("\r",'',$html); 
		$html = str_replace("\n",'',$html);	*/			
	
		// return & exit	
		$result = array('title'=> '<i class="fa fa-share-square-o"></i> '.$wo['lang']['plugin_share_share_post'], 'content' => $html, 'btn' => '');
	
		/* return ajax */		
		return_json($result);	
}

if ($f == 'share_new_post') {

        $media         = '';
        $mediaFilename = '';
        $mediaName     = '';
        $html          = '';
        $recipient_id  = 0;
        $page_id       = 0;
        $group_id      = 0;
		$event_id      = 0;
        $image_array   = array();
		$data = array('result' => 0);

		// valid inputs &&  return ajax 	
		if(!isset($post_id) ||  $post_id == 0) { 			
			return_json(array('title'=> $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_error_post_no_exists'], 'btn' => ''));
		}

		/* check if the viewer can share the post */
		$get_post = $sqlConnect->query("SELECT Wo_Posts.* FROM Wo_Posts WHERE id = '{$post_id}' LIMIT 1");
        if($get_post->num_rows == 0) { 
	       $result = array('title'=> $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_error_post_no_exists'], 'btn' => '');
	       /* return ajax */		
           return_json($result);
	    }

        $post = $get_post->fetch_assoc();

        /* share the origin post */
        if($post['postType'] == "share") {
            $origin = check_post($post['origin_id']);
            if(!$origin || $origin['postPrivacy'] != '0') {
			   $result = array('title'=> $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_error_post_no_exists'], 'btn' => ''); 
			   /* return ajax */		
               return_json($result);
			}
            $post_id = $origin['post_id'];
            $author_id = $origin['user_id'];
        } else {
            $post_id = $post['post_id'];
            $author_id = $post['user_id'];
        } 
	    
        if (!empty($_POST['share_option_type']) && $_POST['share_option_type'] == 'friend') {
		    $recipient_id = Wo_Secure($_POST['share_option_id']);
		} else if (!empty($_POST['share_option_type']) && $_POST['share_option_type'] == 'page') {
            $page_id = Wo_Secure($_POST['share_option_id']);
		} else if (!empty($_POST['share_option_type']) && $_POST['share_option_type'] == 'event') {
            $event_id = Wo_Secure($_POST['share_option_id']);	
        } else if (!empty($_POST['share_option_type']) && $_POST['share_option_type'] == 'group') {
            $group_id = Wo_Secure($_POST['share_option_id']);
            $group    = Wo_GroupData($group_id);
            if (!empty($group['id'])) {
                if ($group['privacy'] == 1) {
                    $_POST['postPrivacy'] = 0;
                } else if ($group['privacy'] == 2) {
                    $_POST['postPrivacy'] = 2;
                }
            }
        }
        
        if (empty($_POST['postPrivacy'])) {
			$_POST['postPrivacy'] = 0;
		}
        $post_privacy  = 0;
        $privacy_array = array('0', '1', '2', '3');
		
        if (isset($_POST['postPrivacy'])) {
            if (in_array($_POST['postPrivacy'], $privacy_array)) {
				$post_privacy = $_POST['postPrivacy'];
			}
        }
        $post_text = '';
        if (!empty($_POST['postText'])) {
            $post_text = $_POST['postText'];
        }
        
		$import_url_image = '';
        $url_link         = '';
        $url_content      = '';
        $url_title        = '';
        $post_map  = '';
        $album_name = '';
		$multi     = 0;
        $traveling = '';
        $watching  = '';
        $playing   = '';
        $listening = '';
        $feeling   = '';

        if (empty($errors)) {
            $post_data = array(
                'user_id' => Wo_Secure($wo['user']['user_id']),
                'page_id' => Wo_Secure($page_id),
                'group_id' => Wo_Secure($group_id),
				'event_id' => Wo_Secure($event_id),
                'postText' => Wo_Secure($post_text),
                'recipient_id' => Wo_Secure($recipient_id),
                'postPrivacy' => Wo_Secure($post_privacy),
				'time' => time(),
				'origin_id' => Wo_Secure($post_id)
            );
                
				if (Wo_CanSenEmails()) {
                    $data['can_send'] = 1;
                }
            
			$new_post_id = Register_Post($post_data);
			
			//echo  json_encode($new_post_id);
            if ($new_post_id) {
               
			    /* update the origin post shares counter */
                $sqlConnect->query("UPDATE Wo_Posts SET shares = shares+1 WHERE post_id = '{$post_id}' LIMIT 1");  
			   
		        /* post notification */
		        $sqlConnect->query("INSERT INTO Wo_Posts SET shares = shares+1 WHERE post_id = '{$post_id}' LIMIT 1"); 
		   
		        if($author_id != $wo['user']['user_id']){
			       $sqlConnect->query("INSERT INTO Wo_Notifications(notifier_id,recipient_id,post_id,type,url,time) 
			       VALUES (".$wo['user']['user_id'].",'{$author_id}','{$post_id}','share','index.php?link1=post&id={$new_post_id}',".time().")");
                }
		   		
				/* plugin points
				if(!empty($wo['plugin_list']['plugin_actived']) && in_array('Points', $wo['plugin_list']['plugin_actived']) && !empty($wo['system']['userpoints_enable']) && $wo['system']['userpoints_enable']){ update_points( $wo['user']['user_id'], 'share' ); }	 */
				
			    /*$wo['story'] = Wo_PostData($new_post_id);
                $html = Wo_LoadPage('story/content');
                $data = array('status' => 200, 'html' => $html, 'post_id' => $post_id );*/
				$data = array('status' => 200, 'result' => 1, 'post_id' => $post_id );
            }
        }
        /* return ajax */		
        return_json($data);
    }
	
	
	if($f == 'share_who_share'){
	   
	   // get shares
	   $users = who_shares($post_id);
	   $result = array('result' => 1, 'html' => '', 'title' => $wo['lang']['plugin_share_error'], 'content' => $wo['lang']['plugin_share_no_shared'], 'btn' =>'');
	   
	   if(count($users) > 0 ){ 
	      ob_start();
          foreach($users as $_user){ 
	        	include './themes/' . $wo['config']['theme'] . '/layout/plugins/share/__feed_user.phtml';
          }
	      $html = ob_get_contents();
          ob_end_clean(); 
	      $result = array('result' => 1, 'html' => '', 'title' => $wo['lang']['plugin_share_who_shares'], 'content' => $html, 'btn' =>'');
       }			
       
	   /* return ajax */		
       return_json($result);

}
?>