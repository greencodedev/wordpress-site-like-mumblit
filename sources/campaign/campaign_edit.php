<?php
/* @author Pp Galvan - LdrMx */
$type = ( isset($_POST['type']) ? $_POST['type'] : ( isset($_GET['type']) ? $_GET['type'] : 'website' ) );

if ($wo['loggedin'] == false){
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
	exit();
}

// valid inputs
$valid['view'] = array('', 'website', 'page', 'post');
if(!in_array($type, $valid['view'])) { 
	//echo return_ajax(array('message' => 'This windows no exist'));
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
	exit();
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'ads edit';
$wo['title']       = 'Edit Campaign | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('plugins/ads/ads_edit'); 
?>