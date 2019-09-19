<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2018 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
$response_data = array(
    'api_status' => 400
);

$required_fields =  array(
                        'create'
                    );

if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {

    if ($_POST['type'] == 'create') {
        if (!empty($_POST['album_name']) && !empty($_FILES['postPhotos']['name'])) {

            if (isset($_FILES['postPhotos']['name'])) {
                $allowed = array(
                    'gif',
                    'png',
                    'jpg',
                    'jpeg'
                );
                for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
                    $new_string = pathinfo($_FILES['postPhotos']['name'][$i]);
                    if (!in_array(strtolower($new_string['extension']), $allowed)) {
                        $error_code    = 6;
                        $error_message = 'Image format is not supported, (jpg, png, gif, jpeg) are supported';
                    }
                }
            }
            if (empty($error_code)) {
                $post_data = array(
                    'user_id' => Wo_Secure($wo['user']['user_id']),
                    'album_name' => Wo_Secure($_POST['album_name']),
                    'postPrivacy' => Wo_Secure(0),
                    'time' => time()
                );
                $id = Wo_RegisterPost($post_data);
                if (count($_FILES['postPhotos']['name']) > 0) {
                    for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
                        $fileInfo = array(
                            'file' => $_FILES["postPhotos"]["tmp_name"][$i],
                            'name' => $_FILES['postPhotos']['name'][$i],
                            'size' => $_FILES["postPhotos"]["size"][$i],
                            'type' => $_FILES["postPhotos"]["type"][$i],
                            'types' => 'jpg,png,jpeg,gif'
                        );
                        $file     = Wo_ShareFile($fileInfo, 1);
                        if (!empty($file)) {
                            $media_album = Wo_RegisterAlbumMedia($id, $file['filename']);
                        }
                    }
                }

                $new_post = Wo_PostData($id);
                if (!empty($new_post['publisher'])) {
                    foreach ($non_allowed as $key4 => $value4) {
                      unset($new_post['publisher'][$value4]);
                    }
                }

                $response_data = array(
                                    'api_status' => 200,
                                    'data' => $new_post
                                );
            }
        }
        else{
            $error_code    = 5;
            $error_message = 'album_name and postPhotos can not be empty';
        }
    }
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}