<?php
$url = "https://o4anews.com/china-weakness-prevent-military-aggression/";

// Setting the HTTP Request Headers
$User_Agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';

$request_headers = array();
$request_headers[] = 'User-Agent: '. $User_Agent;
$request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

 $ch = curl_init();

    // Check if initialization had gone wrong*    
    if ($ch === false) {
        throw new Exception('failed to initialize');
    }

    curl_setopt($ch, CURLOPT_URL, $url);
// Performing the HTTP request
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
 curl_exec($ch);
 $response_body = ob_get_clean();
 
 $response_body;
 echo $response_body;
 ?>
 