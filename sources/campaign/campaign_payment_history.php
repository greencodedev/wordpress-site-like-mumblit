<?php
/* @author Pepe Galvan  - LdrMx */
$plugin_page = 'ads_payment_history';
if (Wo_IsLogged() === false) { header("Location: " . Wo_SeoLink('index.php?tab1=welcome')); exit(); }

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'ads create';
$wo['title']       = $wo['lang']['plugin_ads_ads_list'] . ' | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('plugins/ads/ads_payment_history');
?>