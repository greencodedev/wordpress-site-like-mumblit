<?php
function check_($check) {
    $siteurl           = urlencode(getBaseUrl());
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $file              = file_get_contents('http://www.wowonder.com/purchase.php?code=' . $check . '&url=' . $siteurl, false, stream_context_create($arrContextOptions));
    if ($file) {
        $check             = json_decode($file, true);
    } else {
        $check             = array('status' => 'SUCCESS', 'url' => $siteurl, 'code' => $check);
    }
    return $check;
}
function check_success($check) {
    $siteurl           = urlencode(getBaseUrl());
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $file              = file_get_contents('http://www.wowonder.com/purchase.php?code=' . $check . '&success=true&url=' . $siteurl, false, stream_context_create($arrContextOptions));
    if ($file) {
        $check             = json_decode($file, true);
    } else {
        $check             = array('status' => 'SUCCESS', 'url' => $siteurl, 'code' => $check);
    }
    return $check;
} 
?>