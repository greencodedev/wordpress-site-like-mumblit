<?php
/* @author Pp Galvan  - LdrMx */
$wo['plugin_page'] = 'ads_activate';
if ($wo['loggedin'] == false){
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
	exit();
}

// valid inputs
$ad_id = ( isset($_POST['ad_id']) ? $_POST['ad_id'] : ( isset($_GET['ad_id']) ? $_GET['ad_id'] : 0 ) );
$action = ( isset($_POST['action']) ? $_POST['action'] : ( isset($_GET['action']) ? $_GET['action'] : "" ) );
$type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : "" ) );

//default  
$ad_content = array();  

/*get ad*/
$ad = Ads::get_ad($ad_id, "");

//continue when user exist
if($ad['user_id'] != $wo['user']['user_id']) { 
$response['message'] = 'This no is your ads';
header("Content-type: application/json");
echo json_encode($response);
exit(); 
}   

//get action & template
$ad_content = Ads::activate($ad_id, $action, $type);

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'ads activate';
$wo['title']       = 'Activate Campaign | ' . $wo['config']['siteTitle'];

// page footer
if($ad_content['template']!= ''){ 
$wo['content']     = Wo_LoadPage('plugins/ads/ads_activate'); 
} else { 
$wo['content']     = Wo_LoadPage('plugins/ads/ads_no_content'); 
}
?>