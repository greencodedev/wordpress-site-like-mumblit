<?php 
/* class Ads
 * @author LdrMx - Pp Galvan
 */ 
 
class Ads{	
 
	public static function get_ads($ad_id){
		global $sqlConnect;  
		$info = array();
		$sql = $sqlConnect->query("SELECT * FROM ads_ldrsoft WHERE id='{$ad_id}'"); 
		$ad_count = $sql->num_rows;
		if($ad_count == 1) { 
			$info = $sql->fetch_assoc();
		}	 
		return array('count' => $ad_count,'info' => $info);
	}
	
	
	public static function new_ads($ad_id){
		global $sqlConnect;	 
		$resource = $sqlConnect->query("SHOW TABLE STATUS LIKE '{$ad_id}'");
		while($row = $resource->fetch_assoc()) {
			$ad_id = $row['Auto_increment'];
		}  
		return $ad_id;
	}

  
	public static function get_payment_lists($limit = "", $last_id = false, $first_id = false, $sort_by = " id DESC ", $where = ""){
		global $sqlConnect;
		$payment_array = array();
		$sql = " SELECT * FROM `ads_purchased` WHERE `id` != '0'";
		if($where){ $sql .= " AND {$where}"; } 
		$sql .= ( $last_id ) ? " AND `id` < " . $last_id : '';
		$sql .= ( $first_id ) ? " AND `id` > " . $first_id : '';
		$sql .= " ORDER BY {$sort_by} ";
		if($limit!= ""){ 
			$sql .= " LIMIT {$limit} ";
		}
		// get plans
		$get_payment = $sqlConnect->query($sql);
		while($row = $get_payment->fetch_assoc()) { 
			$payment_array[] = $row;
		}
		return $payment_array;
	}

	
	public static function get_ads_lists($limit = "", $last_id = false, $first_id = false, $sort_by = " A.id DESC ", $where = "", $depth = ""){
		global $sqlConnect;	  
		$ad_sql = " SELECT `A`.*, `P`.`name`, `P`.`price` FROM `ads_ldrsoft` AS A LEFT JOIN `ads_plans` AS P ON `A`.`plan_id` = `P`.`id` WHERE `A`.`user_id` !=' 0' ";
		
		// ADD WHERE CLAUSE, IF NECESSARY
		if( !empty($where) ){
			$ad_sql .= " AND {$where} ";
		}
		$ad_sql .= ( $last_id ) ? " AND `A`.`id` < " . $last_id : '';
		$ad_sql .= ( $first_id ) ? " AND `A`.`id` > " . $first_id : '';
		$ad_sql .= " ORDER BY {$sort_by} ";
		if($limit!= ""){
			$ad_sql .= " LIMIT {$limit} ";
		}			
		$ad_query = $sqlConnect->query($ad_sql);
		$Count = $ad_query->num_rows;
		
		$ad_array = array();
		if($Count>0){
			while( $ad_info = $ad_query->fetch_assoc() ){ 		  
				if($ad_info['views'] == 0){
					$ad_info['views'] = 1;
				}
				// CTR
				if($ad_info['clicks'] == 0) {
					$ad_info['ctr'] = "0.00%";
				} elseif($ad_info['clicks'] > 0) {
					$ad_info['ctr'] = round($ad_info['clicks'] / $ad_info['views'], 4) * 100;
					if(strlen($ad_info['ctr']) == 1 || strlen($ad_info['ctr']) == 2) { $ad_info['ctr'] .= ".00"; }
					$ad_info['ctr'] .= "%";
				}
				$ad_array[] = $ad_info;			
			}
		}		
		return array('count' => $Count,'ads' => $ad_array);
	} 
	

	public static function get_plans($limit = "", $last_id = false, $first_id = false, $sort_by = " id DESC ", $where = ""){
		global $sqlConnect;
		$plan_array = array();
		$sql = "";
		$sql .= " SELECT * FROM `ads_plans` WHERE `id` != '0'";
		if($where){
			$sql .= " AND {$where}";
		} 
		$sql .= ( $last_id ) ? " AND `id` < " . $last_id : '';
		$sql .= ( $first_id ) ? " AND `id` > " . $first_id : '';
		$sql .= " ORDER BY {$sort_by} ";
		if($limit!= ""){
			$sql .= " LIMIT {$limit} ";
		}
		// get plans
		$get_plans = $sqlConnect->query($sql);    
		while($row = $get_plans->fetch_assoc()) {
			$plan_array[] = $row;
		}		
		return $plan_array;
	} 

 
	public static function get_ad($ad_id = 0, $where = ""){
	global $sqlConnect;
	  $ad_sql = " SELECT `A`.*, `P`.`name` , `P`.`price` FROM `ads_ldrsoft` AS A LEFT JOIN `ads_plans` AS P ON `A`.`plan_id` = `P`.`id` WHERE `A`.`id`='{$ad_id}' ";
	  // ADD WHERE CLAUSE, IF NECESSARY	
	  if( !empty($where) ){ $ad_sql .= " AND {$where} "; }
	  $ad_sql .= " LIMIT 1 ";
	
	  // DETERMINE WHICH ADS SHOULD BE SHOWN
	  $ad_query = $sqlConnect->query($ad_sql);
	  $ad_info = $ad_query->fetch_assoc();    
	return $ad_info;
	} 

	public static function get_plan($plan_id = 0, $where = ""){
	 global $sqlConnect;
	  $plan_sql = " SELECT * FROM `ads_plans` WHERE `id`='{$plan_id}' ";
	  if( !empty($where) ){ $plan_sql .= " AND {$where} "; }
	  $plan_sql .= " LIMIT 1 ";		
	  $plan_query = $sqlConnect->query($plan_sql);
	  $plan_info = $plan_query->fetch_assoc();		
	return $plan_info;
	} 
 
	public static function add($val){
	  global $sqlConnect, $wo;
		 $expected = array(
		 'campaign_name' => '', 
		 'title' => '', 
		 'type' => '', 
		 'description' => '', 
		 'image' => '', 
		 'plan_type' => '', 
		 'plan_id' => '', 
		 'all_location' => 0, 
		 'gender', 
		 'id' => '', 
		 'page_id' => '',
		 'post_id' => '', 
		 'link' => '', 
		 'status' => 0, 
		 'display_link' => ''
		);
					   
		extract(array_merge($expected, $val));
		
		$location = ( isset($_POST['location']) ? $_POST['location'] : ( isset($_GET['location']) ? $_GET['location'] : '' ) );
		
		if ($all_location or empty($location)) { $location = 'all'; } else { $location = @json_encode($location); }
			
		$campaign_name = Wo_Secure($campaign_name);
		$title = Wo_Secure($title);
		$description = Wo_Secure($description);
		$plan_id = Wo_Secure($plan_id);
		$user_id = Wo_Secure($wo['user']['user_id']);
		$plan_type = Wo_Secure($plan_type);
		$type = Wo_Secure($type);
		$page_id = Wo_Secure($page_id);
		$post_id = Wo_Secure($post_id);
		$gender = Wo_Secure($gender);
		$image = Wo_Secure($image);
		$link = Wo_Secure($link);
		$display_link = Wo_Secure($display_link);
		$slug = Wo_Secure(hash('crc32', $campaign_name.time()));
		$status = Wo_Secure($status);
		$id = Wo_Secure($id);
				
		//UPDATE
		if($id != ""){		
			$sqlConnect->query("UPDATE `ads_ldrsoft` SET 
			`campaign_name` = '{$campaign_name}', 
			`title` = '{$title}', 
			`description` = '{$description}', 
			`plan_id` = '{$plan_id}', 
			`plan_type` = '{$plan_type}', 
			`type` = '{$type}', 
			`page_id` = '{$page_id}', 
			`post_id` = '{$post_id}', 
			`target_location` = '{$location}', 
			`target_gender` = '{$gender}', 
			`images` = '{$image}', 
			`link` = '{$link}', 
			`display_link` = '{$display_link}', 
			`status` = '{$status}', 
			`updated` = ".time()."
			 WHERE `id`= '{$id}' AND `user_id` = '{$wo['user']['user_id']}'");
			//update
			$get_query = $sqlConnect->query("SELECT * FROM `ads_ldrsoft` WHERE `id`='{$id}' AND `user_id` ='{$wo['user']['user_id']}' LIMIT 1");	
			$ad = $get_query->fetch_assoc();
		
		} else if($id == ""){//NEW ADS	
			
			$sqlConnect->query("INSERT INTO `ads_ldrsoft` (
			`campaign_name`, 
			`title`, 
			`description`, 
			`plan_id`, 
			`user_id`, 
			`plan_type`, 
			`type`, 
			`page_id`, 
			`post_id`, 
			`target_location`, 
			`target_gender`, 
			`images`, 
			`link`, 
			`display_link`, 
			`slug`, 
			`status`, 
			`created`, 
			`updated`
			)
			VALUES ( 
			'{$campaign_name}', 
			'{$title}', 
			'{$description}', 
			'{$plan_id}', 
			'{$wo['user']['user_id']}', 
			'{$plan_type}', 
			'{$type}', 
			'{$page_id}', 
			'{$post_id}', 
			'{$location}', 
			'{$gender}', 
			'{$image}', 
			'{$link}', 
			'{$display_link}', 
			'{$slug}', 
			'{$status}', 
			".time().", 
			".time()."
			 )");	
			$adId = $sqlConnect->insert_id;
	
			$get_query = $sqlConnect->query("SELECT * FROM `ads_ldrsoft` WHERE `id`='{$adId}' AND `user_id` ='{$wo['user']['user_id']}' LIMIT 1");	
			$ad = $get_query->fetch_assoc();
		}    
		return $ad;
	}
	
  
	public static function Name_Exists($name){  
		global $sqlConnect, $wo;
		$post_query = $sqlConnect->query("SELECT id FROM ads_ldrsoft WHERE campaign_name='{$name}' AND user_id ='{$wo['user']['user_id']}' LIMIT 1");	
		return $post_query->num_rows;
	}
	
	public static function plan_add($plan_id = "", $name = "", $description = "", $type = "", $price = 0, $quantity = "", $enable, $payment_currency){ 
	 global $sqlConnect;
	 		   
		if($plan_id != ""){
			$sqlConnect->query("UPDATE `ads_plans` SET `name`='{$name}', `description`='{$description}', `type`='{$type}', `price`='{$price}', `quantity`='{$quantity}', `enable`='{$enable}', `money_type`='{$payment_currency}' WHERE `id`= '{$plan_id}'");		    
			$get_query = $sqlConnect->query("SELECT * FROM `ads_plans` WHERE `id`='{$plan_id}' LIMIT 1");	
			$plan = $get_query->fetch_assoc();
		}else if ($plan_id == ""){
			$sqlConnect->query("INSERT INTO `ads_plans` (`name`,`description`,`type`,`price`,`quantity`, `enable`, `money_type`) VALUES ('{$name}','{$description}','{$type}','{$price}','{$quantity}','{$enable}','{$payment_currency}')");	
			$planId = $sqlConnect->insert_id;		
			$get_query = $sqlConnect->query("SELECT * FROM `ads_plans` WHERE `id`='{$planId}' LIMIT 1");	
			$plan = $get_query->fetch_assoc();
		}      
		return $plan;
	} 
	
	/*ACTIVATE*/
  public static	function activate($id = 0, $action = "", $type = ""){       
    global $wo, $sqlConnect;
       if ($id == 0){
		   return header('Location: '.$wo['config']['site_url'].'/campaign');
	   }
       $ad = Ads::get_ad($id);	
       $title = 'Activate Ad';		 
       if (!$ad && ($ad['paid'] == 1 && $ad['quantity'] != 0)){ 
		   return header('Location: '.$wo['config']['site_url'].'/campaign');
	   }        
       $content = null;
        switch($action) {
		    case 'stripe':
			switch($type) {
			 case 'thank_you':
	 
	 if (empty($_POST['stripeToken'])) { return array('title' => $title, 'template' => 'ads_activate_voguepay_fail.phtml'); exit(); }
	  
	 if(!empty($_POST['stripeToken'])){
	 
	 $plan_query = $sqlConnect->query("SELECT `A`.`plan_id`, `P`.* FROM `ads_plans` AS P LEFT JOIN `ads_ldrsoft` AS A ON `A`.`plan_id`=`P`.`id` WHERE `A`.`id`='{$id}' LIMIT 1");
	 $plan_info = $plan_query->fetch_assoc();
	 
	 $sqlConnect->query("INSERT INTO `ads_purchased` (
	 `user_id`, 
	 `payment_type`, 
	 `item_name`, 
	 `item_number`, 
	 `payment_status`, 
	 `payment_amount`, 
	 `payment_currency`, 
	 `quantity`, 
	 `txn_id`, 
	 `receiver_email`, 
	 `payer_email`, 
	 `custom`, 
	 `created`, 
	 `updated`
	 )
		VALUES ( 
	 '{$wo['user']['user_id']}', 
	 'stripe - ".$_POST['stripeTokenType']."', 
	 '{$plan_info['name']}', 
	 '{$id}', 
	 'Complete', 
	 '{$plan_info['price']}', 
	 '{$plan_info['money_type']}', 
	 '1', 
	 '{$_POST['stripeToken']}', 
	 '{$_POST['stripeEmail']}', 
	 '{$wo['user']['email']}', 
	 '', '".time()."', '".time()."' 
	 )");		
			  			
	$sqlConnect->query("UPDATE `ads_ldrsoft` SET `paid`='1', `status`='1', `quantity`= '{$plan_info['quantity']}' WHERE `id`='{$id}' LIMIT 1");
  }
		
		$title = 'Thank you for your order';
	    return array('title' => $title, 'template' => 'ads_activate_payment_success.phtml', 'return_post_paypal' => $data);	 
			 break;
			}
			break;
		
		   /*voguepay*/
		    case 'voguepay':
			switch($type) {
                    case 'thank_you':
					//echo json_encode($_POST);			
					
 					 if(!empty($_POST['transaction_id'])){
						//get the full transaction details as an json from voguepay
						if($wo['system']['voguePayMerchantId'] == ''){ 
						$json = file_get_contents('https://voguepay.com/?v_transaction_id='.$_POST['transaction_id'].'&type=json&demo=true'); 
						}
						else { $json = file_get_contents('https://voguepay.com/?v_transaction_id='.$_POST['transaction_id'].'&type=json&demo=true'); }
						$transaction = json_decode($json, true);
						Ads::save_voguepay_data($transaction);
  					}
                        $title = 'Thank you for your order';
	                    return array('title' => $title, 'template' => 'ads_activate_payment_success.phtml', 'return_post_paypal' => $data);
					break;
					
					case 'notification':
					break;
					
					case 'failed':
						return array('title' => $title, 'template' => 'ads_activate_voguepay_fail.phtml');
                        break;
					
					default:
						return array('title' => $title, 'template' => 'ads_activate_voguepay_continue.phtml');
                        break;
					}
			break;
			
			/*paypal*/
            case 'paypal':
			require_once('assets/plugins/ads/payment_class.php');
            $paypal = new paypal_class();
                switch($type) {
                    case 'cancel':
						return array('title' => $title, 'template' => 'ads_activate_payment_cancel.phtml');
                        break;
                    case 'success':
					
					// Response from paypal $_POST
                    $data['item_name'] = $_POST['item_name1'];
                    $data['item_number'] = $_POST['item_number1'];
                    $data['payment_status'] = $_POST['payment_status'];
                    $data['payment_amount'] = $_POST['mc_gross']; //cost
                    $data['payment_currency'] = $_POST['mc_currency']; //USD
					$data['quantity'] = $_POST['quantity1'];
                    $data['txn_id'] = $_POST['txn_id'];
                    $data['receiver_email'] = $_POST['receiver_email'];
                    $data['payer_email'] = $_POST['payer_email'];
                    $data['custom'] = $_POST['invoice'];
					
                    Ads::save_paypal_data($data);
					
	                    $title = 'Thank you for your order';
	                    return array('title' => $title, 'template' => 'ads_activate_payment_success.phtml', 'return_post_paypal' => $data);
                        break;
												
					 case 'ipn':          
				        // Paypal is calling page for IPN validation...     
                        if ($paypal->validate_ipn()) {
				        $subject = 'Instant Payment Notification - Recieved Payment';
				        $paypal->send_report ( $subject );
                        } else {
				        $subject = 'Instant Payment Notification - Payment Fail';
			 	        $paypal->send_report ( $subject );
    	                }
		             break;
                      
					 default:
                        Ads::payPalProcess($ad);
						return array('title' => $title, 'template' => 'ads/');
                        break;
                   }
                break;
				
              default:
				$title = 'Select Payment Method';
				break;
        }

		return array('title' => $title, 'template' => 'ads_activate_method.phtml');
    }


   public static function save_voguepay_data($p){
     global $sqlConnect, $wo;
     if(!$p){ return false; } 
	 		   $user_id = Wo_Secure($wo['user']['user_id']);
			   $item_name = Wo_Secure($p['merchant_ref']); 
			   $item_number = Wo_Secure($p['merchant_ref']); 
			   $payment_status = Wo_Secure($p['status']);
			   $payment_amount = Wo_Secure($p['total']);
			   $payment_currency = 'USD';
			   $quantity = 1;
			   $txn_id = Wo_Secure($p['transaction_id']);
			   $receiver_email = Wo_Secure($p['email']);
			   $payer_email = Wo_Secure($p['email']);
			   $custom = Wo_Secure($p['cur']);
			    		
		$sqlConnect->query("INSERT INTO `ads_purchased` (`user_id`, `payment_type`, `item_name`, `item_number`, `payment_status`, `payment_amount`, `payment_currency`, `quantity`, `txn_id`, `receiver_email`, `payer_email`, `custom`, `created`, `updated`)
		VALUES ( '{$user_id}', 'voguepay', '{$item_name}', '{$item_number}', '{$payment_status}', '{$payment_amount}', '{$payment_currency}', '{$quantity}', '{$txn_id}', '{$receiver_email}', '{$payer_email}', '{$custom}', ".time().", ".time().")");			
		//$payId = $sqlConnect->insert_id;	
			
		if($p['status'] == 'Approved'){
			$plan_query = $sqlConnect->query("SELECT `A`.`plan_id`, `P`.`quantity`, `P`.`type`, `P`.`money_type` FROM `ads_plans` AS P LEFT JOIN `ads_ldrsoft` AS A ON `A`.`plan_id`=`P`.`id` WHERE `A`.`id`='{$p['merchant_ref']}' LIMIT 1");
			$plan_info = $plan_query->fetch_assoc();	
			if($plan_info['type'] !='period'){  			
				$sqlConnect->query("UPDATE `ads_ldrsoft` SET `paid`='1', `status`='1', `quantity`= '{$plan_info['quantity']}' WHERE `id`='{$p['merchant_ref']}' LIMIT 1");
			} else {
			    $date_start = time();
				$date_end = time()+(60*60*24*$plan_info['quantity']);
				$sqlConnect->query("UPDATE `ads_ldrsoft` SET `paid`='1', `status`='1', `quantity`= '{$plan_info['quantity']}', `date_start`= '{$date_start}', `date_end`= '{$date_end}'  WHERE `id`='{$p['merchant_ref']}' LIMIT 1");
			}
		}			
 }
 	
	/*PAYPAL*/
   public static function payPalProcess($ad){       
	 global $wo, $sqlConnect;
        
		require_once('assets/plugins/ads/payment_class.php');
		
	    $plan_query = $sqlConnect->query("SELECT A.plan_id, P.price, P.quantity, P.money_type FROM ads_plans AS P LEFT JOIN ads_ldrsoft AS A ON A.plan_id=P.id WHERE A.id='{$ad['id']}' LIMIT 1");
        $plan_info = $plan_query->fetch_assoc();
		
        $paypal = new paypal_class();
        $paypal->admin_mail = $wo['system']['paypal_email_notification'];
        $paypal->add_field('business', $wo['system']['corporate_paypal_email']);
        $paypal->add_field('cmd', '_cart');
        $paypal->add_field('return', $wo['config']['site_url'].'/index.php?link1=campaign_activate&ad_id='.$ad['id'].'&action=paypal&type=success');
        $paypal->add_field('cancel_return', $wo['config']['site_url'].'/index.php?link1=campaign_activate&ad_id='.$ad['id'].'&action=paypal&type=cancel');
        $paypal->add_field('notify_url', $wo['config']['site_url'].'/index.php?link1=campaign_activate&action=ipn');
        $paypal->add_field('currency_code', $plan_info['money_type']);
        $paypal->add_field('invoice', $ad['id'].'_'.time());
        $paypal->add_field('upload', 1);
        $paypal->add_field('item_name_1', $ad['campaign_name']);
        $paypal->add_field('item_number_1', $ad['id']);
        $paypal->add_field('quantity', $plan_info['quantity']);
        $paypal->add_field('amount_1', $plan_info['price']);
        $paypal->add_field('email', $wo['user']['email']);
        $paypal->add_field('first_name', $wo['user']['username']);
        $paypal->add_field('last_name', $wo['user']['first_name'].' '.$wo['user']['last_name']);
        $paypal->add_field('address1', $wo['user']['address']);
        $paypal->add_field('city', $wo['user']['country_id']);
        $paypal->add_field('state', $wo['user']['country_id']);
        $paypal->add_field('country', $wo['user']['country_id']);
        $paypal->add_field('zip', 'null');
		$paypal->add_field('rm', '2');	// Return method = POST
        $paypal->submit_paypal_post();
		//$paypal->dump_fields();
    }

   public static function save_paypal_data($p){
       global $sqlConnect, $wo;
       if(!$p){ return false; } 
       
	    $user_id = Wo_Secure($wo['user']['user_id']);
		$item_name = Wo_Secure($p['item_name']); 
		$item_number = Wo_Secure($p['item_number']); 
		$payment_status = Wo_Secure($p['payment_status']);
		$payment_amount = Wo_Secure($p['payment_amount']);
		$payment_currency = Wo_Secure($p['payment_currency']);
		$quantity = Wo_Secure($p['quantity']);
		$txn_id = Wo_Secure($p['txn_id']);
		$receiver_email = Wo_Secure($p['receiver_email']);
		$payer_email = Wo_Secure($p['payer_email']);
		$custom = Wo_Secure($p['custom']);
		
 		$sqlConnect->query("INSERT INTO `ads_purchased` (`user_id`, `item_name`, `item_number`, `payment_status`, `payment_amount`, `payment_currency`, `quantity`, `txn_id`, `receiver_email`, `payer_email`, `custom`, `created`, `updated`)
		VALUES ( '{$user_id}', '{$item_name}', '{$item_number}', '{$payment_status}', '{$payment_amount}', '{$payment_currency}', '{$quantity}', '{$txn_id}', '{$receiver_email}', '{$payer_email}', '{$custom}', ".time().", ".time().")");
			
		//$payId = $sqlConnect->insert_id;	
		if($p['payment_status'] == 'Completed'){
			$plan_query = $sqlConnect->query("SELECT `A`.`plan_id`, `P`.`quantity`, `P`.`type`, `P`.`money_type` FROM `ads_plans` AS P LEFT JOIN `ads_ldrsoft` AS A ON `A`.`plan_id`=`P`.`id` WHERE `A`.`id`='{$p['item_number']}' LIMIT 1");    
			$plan_info = $plan_query->fetch_assoc();	  			
			if($plan_info['type'] !='period'){  
				$sqlConnect->query("UPDATE `ads_ldrsoft` SET `paid`='1', `status`='1', `quantity`= '{$plan_info['quantity']}' WHERE `id`='{$p['item_number']}' LIMIT 1");
			} else {
				$date_start = time();
				$date_end = time()+(60*60*24*$plan_info['quantity']);
				$sqlConnect->query("UPDATE `ads_ldrsoft` SET `paid`='1', `status`='1', `quantity`= '{$plan_info['quantity']}', `date_start`= '{$date_start}', `date_end`= '{$date_end}' WHERE `id`='{$p['item_number']}' LIMIT 1");
			}
		}			
 }
   
	/*CLICK*/
	public static function processClick($ad){
		global $sqlConnect;
		if(!$ad){ return false; }
		/*sum clicks*/
		$clicks = ($ad['clicks'] + 1);        
		$clicks_stats = ($ad['clicks_stats'] + 1);	
		/*rest clicks*/
		$quantity = ($ad['quantity'] - 1);        
		/*update*/
		$sqlConnect->query("UPDATE ads_ldrsoft SET clicks='{$clicks}', clicks_stats='{$clicks_stats}', quantity = '{$quantity}' WHERE id='{$ad['id']}' LIMIT 1");		
	}

	
	/*UPDATE WHEN GET ADS*/	
	public static function updateAd($ad){       
		global $wo, $sqlConnect;
		if(!$ad){ return false; }
		/*sum views*/
		$views = ($ad['views'] + 1);
		$impression_stats = ($ad['impression_stats'] + 1);
		/*updates*/
		if ($ad['plan_type'] == 'impression') {	
		/*rest views*/
		$quantity =  ($ad['quantity'] - 1);
			$sqlConnect->query("UPDATE ads_ldrsoft SET views='{$views}', impression_stats='{$impression_stats}', quantity = '{$quantity}' WHERE id='{$ad['id']}' LIMIT 1");		
		} else {
			$sqlConnect->query("UPDATE ads_ldrsoft SET views='{$views}', impression_stats='{$impression_stats}' WHERE id='{$ad['id']}' LIMIT 1");
		}
	}
	  
	
	
  public static function get_page($page_id){
	global $sqlConnect,$wo;
	if(!$page_id){ return false; }
		// get page info
		$get_page = $sqlConnect->query("
		SELECT * 
		FROM Wo_Pages 
		WHERE page_id = '{$page_id}' 
		LIMIT 1
		");
		$get_info_page = $get_page->fetch_assoc();
		$get_info_page['photo'] = $wo['config']['site_url'].'/'.$get_info_page['avatar'];		
		return $get_info_page;
	}

	
	public static function processSuccess($ad){ 
		global $sqlConnect;
		if($ad['id']){  
			$plan_query = $sqlConnect->query("
			SELECT A.plan_id, P.quantity 
			FROM ads_plans AS P LEFT JOIN ads_ldrsoft AS A ON A.plan_id=P.id 
			WHERE A.id='{$ad['id']}' LIMIT 1
			");
			$plan_info = $plan_query->fetch_assoc();
			
			$paid = 1;
			$quantity = $plan_info['quantity'];
			$status = 1;	
			$id = Wo_Secure($ad['id']);	 				
			$sqlConnect->query("
			UPDATE ads_ldrsoft 
			SET paid = '{$paid}', quantity = '{$quantity}', status = '{$status}' 
			WHERE id= '{$id}'
			");
		}	
	}
	
}
?>