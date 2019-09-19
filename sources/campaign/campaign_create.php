<?php
/* @author Pp Galvan  - LdrMx */
if ($wo['loggedin'] == false){
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome'));
	exit();
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'ads create';
$wo['title']       = $wo['lang']['plugin_ads_create_campaign'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('plugins/ads/ads_create');
?>