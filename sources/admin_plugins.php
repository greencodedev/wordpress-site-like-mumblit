<?php
/* @author Pp Galvan - LdrMx */

$wo['plugin_page'] = 'admin_plugins';

$is_admin = Wo_IsAdmin();
$is_moderoter = Wo_IsModerator();
if ($is_admin === false && $is_moderoter === false) {
    header("Location: " . $wo['config']['site_url']);
    exit();
}

$view = ( isset($_POST['view']) ? $_POST['view'] : ( isset($_GET['view']) ? $_GET['view'] : '' ) );
$sub_view = ( isset($_POST['sub_view']) ? $_POST['sub_view'] : ( isset($_GET['sub_view']) ? $_GET['sub_view'] : '' ) );
$id = ( isset($_POST['id']) ? $_POST['id'] : ( isset($_GET['id']) ? $_GET['id'] : '0' ) );

$wo['description'] = $wo['config']['siteDesc'];
$wo['keywords']    = $wo['config']['siteKeywords'];
$wo['page']        = 'admin';
$wo['decode_android_v']  = $wo['config']['footer_background'];
$wo['decode_android_value']  = base64_decode('I2FhYQ==');

$wo['decode_android_n_v']  = $wo['config']['footer_background_n'];
$wo['decode_android_n_value']  = base64_decode('I2FhYQ==');

$wo['decode_ios_v']  = $wo['config']['footer_background_2'];
$wo['decode_ios_value']  = base64_decode('I2FhYQ==');

$wo['decode_windwos_v']  = $wo['config']['footer_text_color'];
$wo['decode_windwos_value']  = base64_decode('I2RkZA==');

$wo['title']       = $wo['lang']['admin_area'] . '/ plugins | ' . $wo['config']['siteTitle'];
$wo['content']     = Wo_LoadPage('plugins/admin/content');
?>