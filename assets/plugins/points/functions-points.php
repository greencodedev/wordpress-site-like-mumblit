<?php
class soft {
  /* DATABASE */
 public static function db_query($query) {
      global $sqlConnect;
      return $sqlConnect->query($query);
  }
  
 public static function db_query_array($query) {
      global $sqlConnect;     
      $result = $sqlConnect->query($query);
      return $result ? $result->fetch_array(MYSQLI_ASSOC) : false;
  }

 public static function db_query_array_all($query) {
      global $sqlConnect;
      $items = array();
      $rows = $sqlConnect->query($query);
      while($row = $rows->fetch_array(MYSQLI_ASSOC)){ $items[] = $row; }
      return $rows ? $items : false;
  }
  
 public static function db_query_assoc($query) {
      global $sqlConnect; 
      $result = $sqlConnect->query($query);
      return $result ? $result->fetch_array(MYSQLI_ASSOC) : false;
  }

 public static function db_query_assoc_all($query) {
      global $sqlConnect;   
      $items = array();
      $rows = $sqlConnect->query($query);
      while($row = $rows->fetch_array(MYSQLI_ASSOC)){ $items[] = $row; }
      return $rows ? $items : false;
  }
  
 public static function db_query_count($query) {
      $sqlConnectr = soft::db_query_array($query);
      if($sqlConnectr === false){ return 0; }  
	  foreach($sqlConnectr as $k => $v){ $sqlConnectr0 = $v; }     
      return $sqlConnectr0;
  }

 public static function db_query_affected_rows($query) {
      global $sqlConnect;
      $result = $sqlConnect->query($query);
      return $result ? $sqlConnect->affected_rows : false;
  }
}
/** QUASI CONSTANTS **/							
$uptransaction_states = array(0 => "Completed", 1 => "Pending", 2 => "Cancelled");

$action_group_types = array( 0  => 'Unknown / Uncategorized', 1  => 'Group', 2  => 'Poll', 3  => 'Events', 4  => 'Classifieds', 5  => 'Blog', 6  => 'Media / Albums', 9  => 'Music', 10  => 'General',11  => 'Signup / Marketing');

/** MAIN "FINANCIAL" FUNCTIONS **/
//PUNTOS DE USUARIO
function get_points($user_id) {
  global $sqlConnect;
  $resource = $sqlConnect->query( "SELECT count FROM userpoints WHERE user_id = '{$user_id}'" );
  return $resource->num_rows;
}

function try_deduct($user_id, $amount) {
  return soft::db_query_count( "SELECT COUNT(*) FROM userpoints WHERE user_id = '{$user_id}' AND (count - $amount) >= 0" ) == 1;
}

function userpoints_deduct($user_id, $amount, $allowNegativeCredit = false) {

  if($allowNegativeCredit) {
    $success = soft::db_query_affected_rows( "UPDATE userpoints SET count = count - $amount, totalspent = totalspent + $amount WHERE user_id = '{$user_id}'" ) == 1;
  } else {
    $success = soft::db_query_affected_rows( "UPDATE userpoints SET count = count - $amount, totalspent = totalspent + $amount WHERE user_id = '{$user_id}' AND count >= '{$amount}'" ) == 1;
  }

  if($success){ 
   //update_stats( $user_id, "spend", $amount );
   } 

  return $success;
}

function userpoints_add($user_id, $amount, $update_totalearned = true) {
    global $sqlConnect;

    if($update_totalearned) {
    $sqlConnect->query("INSERT INTO userpoints (user_id, count, totalearned) VALUES ( $user_id, $amount, $amount ) ON DUPLICATE KEY UPDATE count = count + $amount, totalearned = totalearned + $amount ");
	    } else {
      $sqlConnect->query("INSERT INTO userpoints (user_id, count, totalearned) VALUES ( $user_id, $amount, $amount ) ON DUPLICATE KEY UPDATE count = count + $amount");
    }
}

function userpoints_set($user_id, $amount) {
    global $sqlConnect;
    $sqlConnect->query("INSERT INTO userpoints (user_id, count) VALUES ( $user_id, $amount ) ON DUPLICATE KEY UPDATE count = '{$amount}'");  
}

function try_deduct_bytype($user_id, $type) {
  return soft::db_query_count( "SELECT COUNT(*) FROM userpoints WHERE user_id = '{$user_id}' AND (count - (SELECT cost FROM userpointspender  WHERE type = $type LIMIT 1)) >= 0" ) == 1;
}

// VER GASTOS DE PUNTOS DE USERPOINTS  Y USERPOINTS SPENDER COST
function try_deduct_byid($user_id, $id) {
  return soft::db_query_count( "SELECT COUNT(*) FROM userpoints WHERE user_id = '{$user_id}' AND (count - (SELECT cost FROM userpointspender  WHERE id = $id LIMIT 1)) >= 0" ) == 1;
}

function deduct_bytype($user_id, $type, $allowNegativeCredit = false) {
  $amount = soft::db_query_count("SELECT cost FROM userpointspender WHERE type = $type LIMIT 1");
  return deduct( $user_id, $amount, $allowNegativeCredit );
}


/**** CUSTOM FUNCTIONS **/
function userpoints_get_rank($user_id) {
  return soft::db_query_count( "SELECT COUNT(*)+1 AS rank FROM userpoints WHERE totalearned > (SELECT totalearned FROM userpoints WHERE user_id='{$user_id}')" );
}

/**** MAIN REWARDING FUNCTION **/

// THIS FUNCTION REWARDS USER FOR SPECIFIC ACTION, WITH LIMITS PER ACTION
function update_points($user_id, $type, $amount = 1, $text = ""){  
	global $sqlConnect;
   $now = time();
   $resource = $sqlConnect->query( "SELECT 
		P.action_id, 
		P.action_name,
		P.action_points, 
		P.action_pointsmax, 
		P.action_rolloverperiod,
		C.lastrollover,
		C.amount
                                     FROM actionpoints P
                                     JOIN userpointcounters C
                                       ON P.action_id = C.action_id
                                    WHERE P.action_type = '{$type}'
                                      AND C.user_id = '{$user_id}'" );
    if($resource->num_rows > 0){ $action_data = $resource->fetch_assoc(); }
	
    // NO POINTS AWARDED
    if(!empty($action_data) && ($action_data['action_points'] == 0)){ return; }
    
    // THIS USER HAS NO RECORD OF THIS ACTIVITY, FETCH ACTIVITY DATA
	if(empty($action_data)){  
				$resource = $sqlConnect->query("SELECT 
				P.action_id, 
				P.action_points,
				P.action_name, 
				P.action_pointsmax, 
				P.action_rolloverperiod,
				0 AS lastrollover, 
				0 AS amount
                                           FROM actionpoints P
                                           WHERE P.action_type = '$type' " );
				$action_data = $resource->fetch_assoc();								   
	}


// NO POINTS AWARDED
    if((!$action_data) || ($action_data['action_points'] == 0)){ return; }
     
//DEFINIMOS VALOR DE PUNTOS DE MONTO IGUAL A 1 MULTIPLICADO POR EL VALOR DE LA ACCION
    $points_to_add = $amount * $action_data['action_points'];

// EDITAR AQUI REGISTRAR ACCION
    // check if not reached max points / rollover date
    // if action_pointsmax is 0 
    // otherwise if empty lastrollover or lastrollover + rollover_period >= current_time  ==> rollover and assign amount
    // otherwise if amount + amount > action_pointsmax ==> STOP

    if($action_data && ($action_data['action_pointsmax'] == 0)){ 	
      $sql = "INSERT INTO userpointcounters (
                user_id,
                action_id,
                amount, 
                cumulative )
              VALUES (
                '{$user_id}',
                '{$action_data['action_id']}',
                '{$points_to_add}',
                '{$points_to_add}' )
              ON DUPLICATE KEY UPDATE
                amount = amount + '{$points_to_add}',
                cumulative = cumulative + '{$points_to_add}'";
} elseif($action_data && ($action_data['action_pointsmax'] != 0)){

        $action_data2['action_rolloverperiod'] = 86400;  // one day		
        // TIME FOR ROLLOVER OR NEVER HAD POINTS FOR THIS ACTION
		//verifica cuando se realizo el ultimo movimiento y verifica el periodo, verifica ahora con el ultimo moviento verifica periodo
		if(empty($action_data['lastrollover']) || ( ($action_data2['action_rolloverperiod'] != 0) && ($now - intval($action_data['lastrollover']) >= intval($action_data2['action_rolloverperiod']) )) ){
   
	      // CUT IF ADDING MORE THAN MAX
   if($points_to_add > $action_data['action_pointsmax'] ){ $points_to_add = $action_data['action_pointsmax']; }
          $sql = "INSERT INTO userpointcounters (
                    user_id,
                    action_id,
                    lastrollover,
                    amount, 
                    cumulative )
                  VALUES (
                    '{$user_id}',
                    '{$action_data['action_id']}',
                    '{$now}',
                    '{$points_to_add}',
                    '{$points_to_add}' )
                  ON DUPLICATE KEY UPDATE
                    lastrollover = '{$now}',
                    amount = '{$points_to_add}',
                    cumulative = cumulative + '{$points_to_add}'";
} else {
          // ROLLOVER DATE NOT REACHED, SEE IF HIT MAX
          if($action_data['amount'] + $points_to_add <= $action_data['action_pointsmax']){       
		  // DIDN'T HIT MAX, OK
          // this one checks if can add at least one "whole" action-points
          // } elseif (($amount > 1) && ($action_data['amount'] + $action_data['action_points'] <= $action_data['action_pointsmax'])) {
          // this one adds partial amount
}		
		   elseif (($amount > 1) && ($action_data['action_pointsmax'] - $action_data['amount'] > 0)) 
{
           // HIT MAX, ADD PARTIAL (? CHECK IF AMOUNT > 1 AND WE CAN STILL SQUEEZE SOME)
            $points_to_add = $action_data['action_pointsmax'] - $action_data['amount'];
}
		   else 
{
            $points_to_add = 0;
}

          if($points_to_add != 0){
            $sql = "UPDATE userpointcounters
                    SET amount = amount + $points_to_add, cumulative = cumulative + '{$points_to_add}'
                    WHERE user_id = '{$user_id}' AND action_id = '{$action_data['action_id']}'";
					}
}

}

    // negative is OK
	// CUT IF ADDING MORE THAN MAX - REGISTRO DE PUNTOS
    if( $points_to_add != 0 ){
        !empty($sql) ? $sqlConnect->query( $sql ) : 0;
        userpoints_add( $user_id, $points_to_add );
		//text number
		if($text == ''){ $text = $action_data['action_name'];}
		$sqlConnect->query("INSERT INTO uptransactions (
                    user_id,
                    type,
                    cat, 
		            state,
                    text,
                    date,
                    amount )
                  VALUES (
                    '{$user_id}',
                    '{$action_data['action_id']}',
                    '1',
		            '0',
		            '{$text}',
		            '{$now}',
                    '{$points_to_add}' )");
		}
		$newinsert_id = $sqlConnect->insert_id;
	    return $newinsert_id; 
}



/**** OBTIENE PUNTOS Y MAS **/

function userpoints_get_all($user_id) {
  return soft::db_query_assoc( "SELECT * FROM userpoints WHERE user_id = '{$user_id}'" );
}




/*********************** THE MIGHTY TRANSFERRING FUNCTION *********************/


	// THIS FUNCTION TRANSFERS POINTS FROM ONE USER TO ANOTHER
	function userpoints_transfer_points(&$sender, $receiver_id, $amount) {
		global $sqlConnect, $url, $functions_userpoints, $wo;
		
		$is_error = 0;
		$message = '';
		
		$receiver_id = intval($receiver_id);
		$amount = intval($amount);
		
		for(;;) {
	
		// if allowed to transfer points
		if(($wo['system']['level_allow'] == 0) || ($wo['system']['level_allow_transfer'] == 0) || ($wo['user']['user_allowed'] == 0)) {
			$is_error = 1;
			$message = "You are not allowed to transfer points.";//100016061;
			break;
		}
	
		// check points
		if(!($amount > 0)) {
			$is_error = 1;
			$message = "Recipient doesn't exist or can't accept points.";//100016059;
			break;
		}
	
		// check receiver exists
		$ruser = $sqlConnect->query("SELECT * FROM Wo_Users WHERE user_id = '{$receiver_id}' LIMIT 1");
		if($ruser->num_rows == 0) {
			$is_error = 1;
			$message = "Recipient doesn't exist or can't accept points.";//100016059;
			break;
		}
		$r_user = $ruser->fetch_assoc();
	
		// check points quota / limitations
		if($wo['system']['level_allow_transfer'] != 0) {
			// TBD: refactor userpoints_update_points
			$action_data = soft::db_query_assoc( "SELECT 
			P.action_id, 
			P.action_points, 
			P.action_pointsmax, 
			P.action_rolloverperiod,
			C.lastrollover, 
			C.amount
												  FROM  actionpoints P
												  JOIN  userpointcounters C
													ON  P.action_id = C.action_id
												  WHERE P.action_type = 'transferpoints'
													AND C.user_id = {$sender}" );
	
			// THIS USER HAS NO RECORD OF THIS ACTIVITY, FETCH ACTIVITY DATA
		if($action_data == false) {
			$action_data = soft::db_query_assoc( "SELECT 
			P.action_id, 
			P.action_points, 
			P.action_pointsmax, 
			P.action_rolloverperiod,
			0 AS lastrollover, 
			0 AS amount
													FROM actionpoints P
													WHERE P.action_type = 'transferpoints' " );
		}
	
		  // check if not reached max points / rollover date
		  // if action_pointsmax is 0 - ignore
		  // otherwise if empty lastrollover or lastrollover + rollover_period >= current_time  ==> rollover and assign amount
		  // otherwise if amount + amount > action_pointsmax ==> STOP
	
		if($action_data) {
	
			$action_data['action_rolloverperiod'] = 86400;  // one day
			$action_data['action_pointsmax'] = $wo['system']['level_max_transfer'];		
			$now = time();
	
			// TIME FOR ROLLOVER OR NEVER HAD POINTS FOR THIS ACTION
			if(empty($action_data['lastrollover']) || ( ($action_data['action_rolloverperiod'] != 0) && ($now - intval($action_data['lastrollover']) >= intval($action_data['action_rolloverperiod']) )) ) {
	
				// IF ADDING MORE THAN MAX  - NUMERO MAXIMO DE PUNTOS POR DIA ANUNCIO LIMITE
				if($amount > $action_data['action_pointsmax'] ) {
					$is_error = 1;
					$message = "You can only transfer ".$action_data['action_pointsmax']." points."; //100016062
					break;
				}
	
				$sql = "INSERT INTO userpointcounters (
						  user_id,
						  action_id,
						  lastrollover,
						  amount )
						VALUES (
						  {$sender},
						  {$action_data['action_id']},
						  {$now},
						  {$amount} )
						ON DUPLICATE KEY UPDATE
						  lastrollover = {$now},
						  amount = {$amount}";
			  } else {
				// ROLLOVER DATE NOT REACHED, SEE IF HIT MAX
				if($action_data['amount'] + $amount > $action_data['action_pointsmax'] ) {
					// HIT MAX, SEE IF SOME LEFT
					$amount_left = $action_data['action_pointsmax'] - $action_data['amount'];
					if($amount_left == 0) {
						$message = "You have used up your transfer quota.";//100016063;
					} else {
						$message = "You can only transfer ".$amount_left." points."; //100016062
					}				
					$is_error = 1;
					break;
				}
				$sql = "UPDATE userpointcounters
						SET amount = amount + {$amount}
						WHERE user_id = {$sender} AND action_id = {$action_data['action_id']}";
			}
		}
	}


	/*** TRY TRANSFERRING POINTS ***/
	
		// check points left - USTED NO TIENE MAS PUNTOS
		if(!userpoints_deduct( $sender, $amount ) ) {
			$is_error = 1;
			$message = "You don't have enough points.";//100016057;
			break;
		}
		
		userpoints_add(
			$receiver_id,
			$amount//,
			//false // do not update "total points earned"
		);
		
		userpoints_add(
			$sender,
			-$amount//,
			//false // do not update "total points earned"
		);
	
		// Transaction - Sender
		$transaction_id = userpoints_new_transaction( $sender,
			0,//$this->upspender_info['userpointspender_type'],
			2,  // "spender" ?
			0,  // state - completed
			//"Points transfer to <a href=\"".$wo['config']['site_url'].'/'.$r_user['username']."\">{$r_user['username']}</a>",//100016064
			"Points transfer to ".$r_user['username'],
			-$amount
		);

		// Transaction - Receiver
		$transaction_id = userpoints_new_transaction( $receiver_id,
			0,//$this->upspender_info['userpointspender_type'],
			1,    // "earner" ?
			0,    // state - completed
			//"Points transfer from <a href=\"". $wo['config']['site_url'].'/'.$wo['user']['username'] ."\">{$wo['user']['username']}</a>",//100016065
			"Points transfer from ".$wo['user']['username'],
			$amount
		);
	
		// update quotas, if needed
		!empty($sql) ? $sqlConnect->query( $sql ) : 0;
		$message = "Points were transferred successfully.";//100016060;
		break;
	}
	
	//if(is_numeric($message))$message = soft::get_language_text( $message );
	
	return array( 'is_error' => $is_error, 'message' => $message );
}


// TRANSPERENCIA POST
function userpoints_new_transaction( $user_id, $type, $cat, $state, $text, $amount ) {
	global $sqlConnect;
	$now = time(); 
	$sqlConnect->query("INSERT INTO uptransactions (
                    user_id,
                    type,
                    cat, 
		            state,
                    text,
                    date,
                    amount )
                  VALUES (
                    '{$user_id}',
                    '{$type}',
                    '{$cat}',
		            '{$state}',
		            '{$text}',
		            '{$now}',
                    '{$amount}' )");
	
	$transaction_id = $sqlConnect->insert_id;
	return $transaction_id;
}








/**** HOOKS **/

function action( $arguments = array() ) {
  /* ASSIGN ACTIVITY POINTS */
  $user = $arguments[0];
  $actiontype_name = $arguments[1];
  $replace = $arguments[2];

  // TBD - special treatments,
  // "media" - updated in footer, because need to count amount of uploaded files
   $excluded_actions = array( "media" );

   if(!in_array($actiontype_name, $excluded_actions))
      update_points( $user->_data['user_id'], $actiontype_name );
}


/* SYSTEM FUNCTIONS */
function deleteuser_userpoints( $user_id ) {
  global $sqlConnect;
  // Remove counters
  $sqlConnect->query("DELETE FROM userpointcounters WHERE user_id = '{$user_id}'");
  // Remove transactions
  $sqlConnect->query("DELETE FROM uptransactions WHERE user_id = '{$user_id}'");
  // Remove user points
  $sqlConnect->query("DELETE FROM userpoints WHERE user_id = '{$user_id}'");
}

function userpointsrank($userpoints) {
  global $sqlConnect;

  $rows = $sqlConnect->query("SELECT amount, text FROM userpointranks ORDER BY amount");
  $userpoint_ranks = array();
    while($row = $rows->fetch_assoc()) {
	      //$userpoint_ranks[] = array( '\''.$row['amount'].'\'' => '\''.$row['text'].'\'');
		  $userpoint_ranks[] = $row;
    }
 
$prev_step = 0;
$prev_step_text = '';
$userpoints_cntr = 1;

foreach($userpoint_ranks as $rank) {

  if(($userpoints >= $prev_step) && ($userpoints < $rank['amount'])) {
    $user_rank_string = $prev_step_text;
	break;
  }
 /**/ 
  if( $userpoints_cntr++ >= count($userpoint_ranks)) {
    $user_rank_string = $rank['text'];
	break;
  }
  
  $prev_step = $rank['amount'];
  $prev_step_text = $rank['text'];
}

return $user_rank_string;
}
?>