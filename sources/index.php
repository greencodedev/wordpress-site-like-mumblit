<?php
/* @author Pp Galvan  - LdrMx */
if (Wo_IsLogged() === false) {
	header("Location: " . Wo_SeoLink('index.php?tab1=welcome')); exit();
}

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];

if(empty($wo['page'])){
	$wo['page']        = 'My socicialnetwork';
}
if(empty($wo['title'])){
	$wo['title']       = 'Welcome | ' . $wo['config']['siteTitle'];
}

$wo['content']     = Wo_LoadPage('plugins/index');
?>