<?php

/* CLASS uptransaction */

class uptransaction {
    var $uptransaction_exists = 0;
    var $uptransaction_info;
    
    function __construct( $transaction_id ) {
        $this->uptransaction_info = soft::db_query_assoc("SELECT * FROM uptransactions WHERE uptransaction_id = $transaction_id");
        if($this->uptransaction_info) {
            $this->uptransaction_exists = 1;
        }
    }
    
    function is_completed(){ return $this->uptransaction_info['uptransaction_state'] == 0; }
    
    function complete(){
        global $sqlConnect;
        
        if(($this->uptransaction_exists == 0) || $this->is_completed() )
            return false;
        
        $sqlConnect->query("UPDATE uptransactions SET uptransaction_state = 0 WHERE uptransaction_id = {$this->uptransaction_info['uptransaction_id']}");
        
        // FINISH TRANSACTION - REWARD USER IF "EARNER", DO NOTHING IF "SPENDER"
        // TBD: if user was deleted - junk row

        if($this->uptransaction_info['uptransaction_cat'] == 1)  {
            userpoints_add( $this->uptransaction_info['uptransaction_user_id'], $this->uptransaction_info['uptransaction_amount'] );
        }
        
        $this->uptransaction_info['uptransaction_state'] = 0; 
        return true;
    }
    
    function cancel() {
        global $sqlConnect;
        if(($this->uptransaction_exists == 0) || $this->is_completed() )
            return false;

        $sqlConnect->query("UPDATE uptransactions SET uptransaction_state = 2 WHERE uptransaction_id = {$this->uptransaction_info['uptransaction_id']}");

        // REFUND POINTS IF "SPENDER", DO NOTHING IF "EARNER"
        if($this->uptransaction_info['uptransaction_cat'] == 2)  {
            userpoints_add( $this->uptransaction_info['uptransaction_user_id'],
                            abs($this->uptransaction_info['uptransaction_amount']),
                            false // do not update "total earned"
                            );
        }

        $this->uptransaction_info['uptransaction_state'] = 2;
        return true;
    }    
}


/* CLASS upearner */


class upearner {    
    var $upearner_exists = 0;
    var $upearner_info;
    var $err_msg;
    var $transaction_message;

    function __construct( $upearner_id, $onlyenabled = true ) {
	     
        if($onlyenabled){ 
           $this->upearner_info = soft::db_query_assoc("SELECT * FROM userpointearner WHERE enabled != 0 AND id = '{$upearner_id}' LIMIT 1");
        }else{
           $this->upearner_info = soft::db_query_assoc("SELECT * FROM userpointearner WHERE id = '{$upearner_id}' LIMIT 1");
        } 
        if($this->upearner_info) {
            $this->upearner_exists = 1;
            // CONVERT HTML CHARACTERS BACK
            $this->upearner_info['body'] = str_replace("\r\n", "", html_entity_decode( $this->upearner_info['body'] ));
            if(empty($this->upearner_info['photo'])) {
              $this->upearner_info['photo'] = 'assets/plugins/img/no_photo.jpg';
            }            
        }
    }
           
    function delete(){
        global $sqlConnect;
        if($this->upearner_exists == 0){ return; }
		$photo_file = "upload/points/earn/".$this->upearner_info['photo'].'.'.$this->upearner_info['ext'];
        if(@file_exists($photo_file)) { unlink($photo_file); }
        $sqlConnect->query("DELETE FROM userpointearner WHERE id = " . $this->upearner_info['id'] );
		$sqlConnect->query("DELETE FROM userpointearner_users WHERE offer = " . $this->upearner_info['id'] );
    }
    
    function enable($enabled=true) {
        global $sqlConnect;
        
        if($this->upearner_exists == 0){ return; }
        // bool -> int
        $enabled = intval($enabled);
        $sqlConnect->query("UPDATE userpointearner SET enabled = $enabled WHERE id = " . $this->upearner_info['id'] );
    }

    function total_items( $onlyenabled = true, $where = '' ) {
        if($onlyenabled)
            return soft::db_query_count( "SELECT COUNT(*) FROM userpointearner WHERE type >= 100 AND enabled != 0" );
        else
            return soft::db_query_count( "SELECT COUNT(*) FROM userpointearner WHERE type >= 100" );
    }

    function transact($transaction_params = array() ) {
        global $sqlConnect, $wo;
                     
        /** BEFORE TRANSACTION **/      
        $metadata = !empty( $this->upearner_info['metadata'] ) ? unserialize($this->upearner_info['metadata']) : array();
		$params = array( $this, $metadata, $transaction_params );		
		$type = $this->upearner_info['type'];

        /** TRANSACTION **/ 
        // Instantly completed
        if( $this->upearner_info['transact_state'] == 0) {            		
		
		$get_act_data = $sqlConnect->query( "SELECT 
                            P.action_id, 
                            P.action_points,
                            P.action_name, 
                            P.action_pointsmax, 
                            P.action_rolloverperiod,
                            0 AS lastrollover, 
                            0 AS amount
                                           FROM actionpoints P WHERE P.action_type = '{$type}' " );										   
		$action_data = $get_act_data->fetch_assoc();							   
										   
		$action_data['action_rolloverperiod'] = 86400;  // one day		
		
		// add mount
		if(empty($action_data['lastrollover']) || ( ($action_data['rolloverperiod'] != 0) && ($now - intval($action_data['lastrollover']) >= intval($action_data['rolloverperiod']) )) ){ userpoints_add( $wo['user']['user_id'], $this->upearner_info['cost'] ); }   
			
        }
        // add transaction
		$transaction_id = 0;
        if($this->upearner_info['appear_in_transactions'] == 1){
           $transaction_id = userpoints_new_transaction( $wo['user']['user_id'],
                                                         $this->upearner_info['type'],
                                                         1,
                                                         $this->upearner_info['transact_state'],
                                                         'Offert '.$this->upearner_info['id'].' - '.$this->upearner_info['title'],
                                                         $this->upearner_info['cost']
                                                        );
		}
		
		/** subir limites de clicks **/
		$now = time();

        $sqlConnect->query( "INSERT INTO userpointcounters (
                      user_id,
                      action_id,
                      lastrollover,
                      amount,
					  cumulative)
                      VALUES (
					  '{$wo['user']['user_id']}',
					  '400',
					  '$now',
					  '{$this->upearner_info['cost']}',
					  '{$this->upearner_info['cost']}'
					  )
					  ON DUPLICATE KEY UPDATE
                      lastrollover = '$now',
                      amount = '{$this->upearner_info['cost']}',
                      cumulative = cumulative + '{$this->upearner_info['cost']}'					  
					  ");
		
		/** UPDATE CANT COUNTER **/
        $sqlConnect->query( "UPDATE userpointearner SET cant = cant - 1 WHERE id = " . $this->upearner_info['id'] );

        /** UPDATE ENGAGEMENTS COUNTER **/
        $sqlConnect->query( "UPDATE userpointearner SET engagements = engagements + 1 WHERE id = " . $this->upearner_info['id'] );
        
		$sqlConnect->query( "INSERT INTO userpointearner_users (
                      user_id,
                      offer,
					  stamp,
					  status,
					  transaction_id
                      )
                      VALUES (
					  '{$wo['user']['user_id']}',
					  '{$this->upearner_info['id']}',
					  '".time()."',
					  '{$this->upearner_info['transact_state']}',
					  '{$transaction_id}'
					  )					  
					  ");
					  
        return array('result' => '1');
    }
	
	
	

	function dir($item_id = 0) {

        if($item_id == 0 & $this->upearner_exists) {
          $item_id = $this->upearner_info['id'];
        }

	  //$subdir = $item_id+999-(($item_id-1)%1000);
	  //$itemdir = "../points/$subdir/$item_id/";
      $itemdir = "../points/";
	  return $itemdir;

	}

	function photo($nophoto_image = "") {
	    $item_photo = 'upload/earn/'.$this->upearner_info['photo'].'.'.$this->upearner_info['ext'];
	  if(!file_exists($item_photo) | $this->upearner_info['photo'] == "") {
        $item_photo = $nophoto_image;
        }
	  return $item_photo;
	  
	}

	function public_dir($item_id = 0) {
      $itemdir = "points/";
	  return $itemdir;
	}
    
	function public_photo($nophoto_image = "", $depth = "./upload/") {
      $item_photo = $this->public_dir() .'earn/'. $this->upearner_info['photo'].'.'.$this->upearner_info['ext'];
	  if(!file_exists($depth.$item_photo) || $this->upearner_info['photo'] == "") { $item_photo = $nophoto_image; }
	  return $item_photo;	  
	}

	function photo_delete() {
	  global $sqlConnect;
	  $item_photo = $this->photo();
	  if($item_photo != "") {
	    unlink($item_photo);
	    $sqlConnect->query("UPDATE userpointearner SET photo='' WHERE id='".$this->upearner_info['id']."'");
	    $this->upearner_info['photo'] = "";
	  }
	}

	function photo_upload($photo_name) {
	  global $sqlConnect, $url;

	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower( "jpg,jpeg,gif,png" )));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = 200;
	  $file_maxheight = 200;
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $this->dir() . $photo_newname;
	  $new_photo = new upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);

	  // UPLOAD AND RESIZE PHOTO IF NO ERROR
	  if($new_photo->is_error == 0) {

	    // DELETE OLD AVATAR IF EXISTS
	    $this->photo_delete();

	    // CHECK IF IMAGE RESIZING IS AVAILABLE, OTHERWISE MOVE UPLOADED IMAGE
	    if($new_photo->is_image == 1) {
	      $new_photo->upload_photo($file_dest);
	    } else {
	      $new_photo->upload_file($file_dest);
	    }

	    // UPDATE INFO WITH IMAGE IF STILL NO ERROR
	    if($new_photo->is_error == 0) {
	      $sqlConnect->query("UPDATE userpointearner SET photo='$photo_newname' WHERE id='".$this->upearner_info['id']."'");
	      $this->upearner_info['photo'] = $photo_newname;
	    }
	  }
	
	  $this->is_error = $new_photo->is_error;
	  $this->error_message = $new_photo->error_message;
	}
}


/******************  CLASS upspender  ******************/

class upspender {
    
    var $upspender_exists = 0;
    var $upspender_info;
    var $err_msg;
    var $transaction_message;
    function __construct( $upspender_id, $onlyenabled = true ) {

        if($onlyenabled) {
            $this->upspender_info = soft::db_query_assoc("SELECT * FROM userpointspender WHERE enabled != 0 AND id = $upspender_id");
        } else {
            $this->upspender_info = soft::db_query_assoc("SELECT * FROM userpointspender WHERE id = $upspender_id");
        }
		
        if($this->upspender_info) {
            $this->upspender_exists = 1;
            // CONVERT HTML CHARACTERS BACK
            $this->upspender_info['body'] = str_replace("\r\n", "", html_entity_decode( $this->upspender_info['body'] ));
            if(empty($this->upspender_info['photo'])) {
              $this->upspender_info['photo'] = 'assets/plugins/img/no_photo.jpg';
            }
        }
    }
        
    function factory($item_id) {
        
    }

    function delete() {
        global $sqlConnect;
        if($this->upspender_exists == 0){ return; }         
		$photo_file = "upload/points/spend/".$this->upspender_info['photo'].'.'.$this->upspender_info['ext'];
        if(@file_exists($photo_file)) { unlink($photo_file); }	   
        $sqlConnect->query("DELETE FROM userpointspender WHERE id = " . $this->upspender_info['id'] );
		$sqlConnect->query("DELETE FROM userpointspender_users WHERE offer = " . $this->upspender_info['id'] );
    }
      
    function enable($enabled=true) {
        global $sqlConnect;
        if($this->upspender_exists == 0)
            return;
        
        // bool -> int
        $enabled = intval($enabled);
        $sqlConnect->query("UPDATE userpointspender SET enabled = $enabled WHERE id = " . $this->upspender_info['id'] );
    }
      
    function total_items( $onlyenabled = true, $where = '' ) {
        if($onlyenabled)
            return soft::db_query_count( "SELECT COUNT(*) FROM userpointspender WHERE type >= 100 AND enabled != 0" );
        else
            return soft::db_query_count( "SELECT COUNT(*) FROM userpointspender WHERE type >= 100" );
    }

    function transact() {
        global $sqlConnect, $wo;
        
        /** BEFORE TRANSACTION **/              
        $metadata = !empty( $this->upspender_info['metadata'] ) ? unserialize($this->upspender_info['metadata']) : array();
        $params = array( $this, $metadata );
        
		/* available*/ 
        if( $this->upspender_info['cant'] <= 0 ) {           
            return array('error' => true, 'result' => '0', 'message' => __("This item no is more available, zero items.") );
        }
				 
        /** TRANSACTION **/         
        if( !userpoints_deduct( $wo['user']['user_id'], $this->upspender_info['cost'] ) ) {           
            return array('error' => true, 'result' => '0', 'message' => $wo['lang']['plugin_point_you_no_points']);
        }
       
        $transaction_id = 0;
        if( $this->upspender_info['appear_in_transactions'] == 1) {

                $transaction_id = userpoints_new_transaction($wo['user']['user_id'],
                                                             $this->upspender_info['type'],
                                                             2,
                                                             $this->upspender_info['transact_state'],
                                                             'Buy of '.$this->upspender_info['title'],
                                                             -$this->upspender_info['cost']
                                                             );
        }

        /** UPDATE NUMB COUNTER **/
        $sqlConnect->query( "UPDATE userpointspender SET cant = cant - 1 WHERE id = " . $this->upspender_info['id'] );

        /** UPDATE ENGAGEMENTS COUNTER **/
        $sqlConnect->query( "UPDATE userpointspender SET engagements = engagements + 1 WHERE id = " . $this->upspender_info['id'] );
		
		$sqlConnect->query( "INSERT INTO userpointspender_users (
                      user_id,
                      offer,
					  stamp,
					  status,
					  transaction_id
                      )
                      VALUES (
					  '{$wo['user']['user_id']}',
					  '{$this->upspender_info['id']}',
					  '".time()."',
					  '{$this->upspender_info['transact_state']}',
					  '{$transaction_id}'
					  )					  
					  ");
		$new_shop_id = $sqlConnect->insert_id;
					  
		//notification to admin of need action for complete
		if($this->upspender_info['transact_state'] != 0){
		  //send notify to all moderator and admins
		  $get_all_admins = $sqlConnect->query("SELECT user_id, first_name, email FROM Wo_Users WHERE admin='1'");
		  /* prepare email */
          $subject = "New aprovation of point shop from ".$wo['config']['siteTitle'];
          $body  = ucwords($wo['user']['name']).", need of you aprovation for verify his purchase";
          $body .= "\r\n\r\n To complete the process, please follow this link:";
          $body .= "\r\n\r\n".$wo['config']['site_url']."/index.php?link1=admin-plugins&view=points_shop_users&sub_view=".$new_shop_id;
          while($rows =  $get_all_admins->fetch_assoc()){
		  $email =  $rows['email'];
		  /* send using mail() */
          send_email($email, $subject, $body);
		  }	
		}					  
					  
        return array('result' => '1');
    }
   
	function dir($item_id = 0) {
      if($item_id == 0 & $this->upspender_exists) { $item_id = $this->upspender_info['id']; }
	  //$subdir = $item_id+999-(($item_id-1)%1000);
	  //$itemdir = "../points/$subdir/$item_id/";
      $itemdir = "../points/";
	  return $itemdir;
	}

	function photo($nophoto_image = "") {  
	  $item_photo = 'upload/spend/'.$this->upspender_info['photo'].'.'.$this->upspender_info['ext'];
	  if(!file_exists($item_photo) | $this->upspender_info['photo'] == "") {
        $item_photo = $nophoto_image;
      }
	  return $item_photo;
	  
	}

	function public_dir($item_id = 0) {
      $itemdir = "points/";
	  return $itemdir;
	}
     
	function public_photo($nophoto_image = "", $depth = "./upload/") {
      $item_photo = $this->public_dir() .'spend/'. $this->upspender_info['photo'].'.'.$this->upspender_info['ext'];

	  if(!file_exists($depth.$item_photo) || $this->upspender_info['photo'] == "") {
        $item_photo = $nophoto_image;
        }
	  return $item_photo;
	  
	}
 
	function photo_delete() {
	  global $sqlConnect;
	  $item_photo = $this->photo();
	  if($item_photo != "") {
	    unlink($item_photo);
	    $sqlConnect->query("UPDATE userpointspender SET photo='' WHERE id='".$this->upspender_info['id']."'");
	    $this->upspender_info['photo'] = "";
	  }
	}

	function photo_upload($photo_name) {
	  global $sqlConnect, $url;

	  // SET KEY VARIABLES
	  $file_maxsize = "4194304";
	  $file_exts = explode(",", str_replace(" ", "", strtolower( "jpg,jpeg,gif,png" )));
	  $file_types = explode(",", str_replace(" ", "", strtolower("image/jpeg, image/jpg, image/jpe, image/pjpeg, image/pjpg, image/x-jpeg, x-jpg, image/gif, image/x-gif, image/png, image/x-png")));
	  $file_maxwidth = 200;
	  $file_maxheight = 200;
	  $photo_newname = "0_".rand(1000, 9999).".jpg";
	  $file_dest = $this->dir() . $photo_newname;

	  $new_photo = new upload();
	  $new_photo->new_upload($photo_name, $file_maxsize, $file_exts, $file_types, $file_maxwidth, $file_maxheight);

	    // DELETE OLD AVATAR IF EXISTS
	    $this->photo_delete();
	    // UPDATE INFO WITH IMAGE IF STILL NO ERROR
	    if($new_photo->is_error == 0) {
	      $sqlConnect->query("UPDATE userpointspender SET photo='$photo_newname' WHERE id='".$this->upspender_info['id']."'");
	      $this->upspender_info['photo'] = $photo_newname;
	    }
		
	}
	
}



/*
 * Advanced Activity Points
 * Remember if you truly like it, support the maker and buy it!
 ****************************************************************/

class tagcloud {
  
  var $tags = array();
  
  function __construct( $tags = null) {
    if($tags) {
      if(!is_array($tags))
        $tags = explode(',', $tags);
      foreach($tags as $tag) {
        $this->add_tag( $tag );
      }
    }
  }
  
  function add_tag( $tag, $weight = 1) {
    $tag = trim(strtolower( $tag ));
    if(empty($tag))
      return;
    if(array_key_exists($tag, $this->tags)) 
      $this->tags[$tag] += $weight;
    else
      $this->tags[$tag] = $weight;
  }
  
  function shuffle() {
    $keys = array_keys($this->tags);
     
    shuffle($keys);
     
    if (count($keys) && is_array($keys))
    {
        $tmp = $this->tags;
        $this->tags = array();
        foreach ($keys as $key => $value)
            $this->tags[$value] = $tmp[$value];
    }
  }
  
  function cloud_size() {
    return array_sum( $this->tags );
  }
  
  function get_class_from_percent($percent)
  {
      if ($percent >= 99)
          $class = 1;
      else if ($percent >= 70)
          $class = 2;
      else if ($percent >= 60)
          $class = 3;
      else if ($percent >= 50)
          $class = 4;
      else if ($percent >= 40)
          $class = 5;
      else if ($percent >= 30)
          $class = 6;
      else if ($percent >= 20)
          $class = 7;
      else if ($percent >= 10)
          $class = 8;
      else if ($percent >= 5)
          $class = 9;
      else
          $class = 0;
       
      return $class;
  }
  
  function compile($result_type = 'html', $html_template = '') {
    $this->shuffle();
    if (is_array($this->tags) && !empty($this->tags)) {
    $this->max = max($this->tags);
        $result = ($result_type == "html" ? "" : ($result_type == "array" ? array() : ""));
        foreach ($this->tags as $tag => $weight) {
            $size_range = $this->get_class_from_percent(($weight / $this->max) * 100);
            if ($result_type == "array") {
                $result[$tag]['tag'] = $tag;
                $result[$tag]['size_range'] = $size_range;
            }
            else if ($result_type == "html")
            {
                $result .= str_replace( '[tag]' , $tag, $html_template );
                
            }
        }
        return $result;
    }
  }

  function to_html($link) {
    $this->shuffle();
    if (is_array($this->tags) && !empty($this->tags)) {
    $this->max = max($this->tags);
        $result = '';
        foreach ($this->tags as $tag => $weight) {
            $size_range = $this->get_class_from_percent(($weight / $this->max) * 100);
            $result .= "<span class='tag size{$size_range}'>&nbsp; <a href='$link?tag=$tag'>{$tag}</a>&nbsp;</span>";
        }
        return $result;
    }
  }
}
?>