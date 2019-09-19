<?php 
/* @autor Pp Galvan - LdrMx
*/	
function import_image_from_url($media, $custom_name = '_url_image', $directory = 'photos') {
    if (empty($media)) {
        return false;
    }
    if (!file_exists('uploads/'.$directory.'/' . date('Y'))) {
        mkdir('uploads/'.$directory.'/' . date('Y'), 0777, true);
    }
    if (!file_exists('uploads/'.$directory.'/' . date('Y') . '/' . date('m'))) {
        mkdir('uploads/'.$directory.'/' . date('Y') . '/' . date('m'), 0777, true);
    }
    //$size      = getimagesize($media);
    $extension = 0; //image_type_to_extension($size[2]);
    if (empty($extension)) {
        $extension = '.jpg';
    }
    $dir               = 'uploads/'.$directory.'/' . date('Y') . '/' . date('m');
    $file_dir          = $dir . '/'.$custom_name.$extension;
    $arr_context_options = array("ssl" => array("verify_peer" => false, "verify_peer_name" => false));
    $fileget           = file_get_contents(str_replace(' ', '%20', $media), false, stream_context_create($arr_context_options));
    if (!empty($fileget)) {
        $importImage = @file_put_contents($file_dir, $fileget);
    }
    if (file_exists($file_dir)) {
        return $file_dir;
    } else {
        return false;
    }
}

function check_post($id, $privacy_check = true) {
	global $wo, $sqlConnect;
	$id = Wo_Secure($id);
	
	/* get post */
	$get_post = $sqlConnect->query("SELECT Wo_Posts.* FROM Wo_Posts WHERE post_id = '{$id}' LIMIT 1");
	if($get_post->num_rows == 0) { return false; }
	$post = $get_post->fetch_assoc();
	
	/* get the author */
	$post['author_id'] = $post['user_id'];
	
	/* check privacy */
	if(!$privacy_check || ($privacy_check && check_privacy($post['postPrivacy'], $post['user_id']))) { return $post; }		
	return false;
}


function check_privacy($privacy, $author_id) {
	global $wo;
	if($privacy == '3') { return false; }
	if($privacy != '3') { return true; }
	if($wo['loggedin'] == true) {		
		/* check if the viewer is the target */
		if($author_id == $wo['user']['user_id']) { return true; }
	}
	return false;
}


function truncate($text, $length = "20", $points = true) {
	if( strlen($text) <= $length ) { return $text; }
	$add = '';
	if($points == true){ $add = '..'; }
	
	return preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length)).$add;	
}


function send_email($email, $subject, $body) {
	global $system;
	$header  = "MIME-Version: 1.0\r\n";
	$header .= "Mailer: ".$system['system_title']."\r\n";
	$header .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
	$header .= "Content-Transfer-Encoding: 7bit\r\n";
	if(@mail($email, $subject, $body, $header)) {
		return true;
	}else {
		return false;
	}
}


function return_json($response = '') {
	header("Expires: Mon, 14 Jun 2005 05:00:00 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Pragma: no-cache"); // HTTP/1.0
	header("Content-Type: application/json");
	exit(json_encode($response));
}


function who_shares($post_id, $offset = 0) {
	global $sqlConnect;
	$rows = array();
	$offset *= 15;
	$get_posts = $sqlConnect->query("SELECT P.post_id, U.* FROM Wo_Posts AS P INNER JOIN Wo_Users AS U ON P.user_id=U.user_id WHERE P.postType='share' AND P.origin_id='{$post_id}'");
	if($get_posts->num_rows > 0) {
		while($row = $get_posts->fetch_assoc()) {
				if($row['avatar']!= ''){ $row['user_picture'] =  $row['avatar']; } else { $row['user_picture'] = 'upload/photos/d-avatar.jpg'; }
				if($row['first_name']!= ''){ $row['user_fullname'] =  $row['first_name'].' '.$row['last_name']; } else { $row['user_fullname'] = $row['username']; }
				$rows[] = $row;
		}
	}
	return $rows;
}


require_once('functions_cloned.php');
?>