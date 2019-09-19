<?php
/* @author Pepe Galvan  - LdrMx */
$wo['plugin_page'] = 'ads';
if ($wo['loggedin'] == false) { header("Location: " . Wo_SeoLink('index.php?tab1=welcome')); exit(); }

$by = ( isset($_POST['by']) ? $_POST['by'] : ( isset($_GET['by']) ? $_GET['by'] : 'all' ) );

// valid inputs
$valid['by'] = array('', 'all', 'website', 'page', 'post');
if(!in_array($by, $valid['by'])) {
	$response['message'] = 'option invalid';
	header("Content-type: application/json");
    echo json_encode($response);
    exit(); 
 }
 
$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'ads create';
$wo['title']       = $wo['lang']['plugin_ads_ads_list'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('plugins/ads/ads');
?>