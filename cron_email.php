<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('upload_max_filesize', '1000M');
ini_set('post_max_size', '1000M');
ini_set('max_input_time', '90000000000');
ini_set('max_execution_time', '90000000000');
require_once('assets/init.php');
require 'PHPMailerAutoload.php';
//Create a new PHPMailer instance
$mail = new PHPMailer;
$mail->CharSet = "utf-8";
$mail->isSMTP();
$mail->SMTPDebug =0;
$mail->Debugoutput = 'html';
$mail->Host = "mail.mumblit.com";
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "notify@mumblit.com";
$mail->Password = "!Ilovemee1";

$subject='You Have New Notifications';

$mail->Subject = $subject;

$query = mysqli_query($sqlConnect, "SELECT username,user_id ,email,avatar FROM Wo_Users where type='user'");
while ($fetched_data = mysqli_fetch_assoc($query)) {
$countrows = mysqli_num_rows(mysqli_query($sqlConnect, "SELECT * FROM Wo_Notifications where recipient_id='".$fetched_data['user_id']."' and seen_pop>0"));
if($countrows>0)
	{
				$message = file_get_contents( 'email.html' );
				$message=str_replace("{fname}",$fetched_data['username'],$message);
				$message=str_replace("{notification}",$countrows,$message);
				$message=str_replace("{imgsrc}",'https://www.mumblit.com/'.$fetched_data['avatar'],$message);
				
			
				$mail->addAddress($fetched_data['email']);
				$mail->setFrom('notify@mumblit.com','mumblit.com');
				$mail->addReplyTo('notify@mumblit.com');
				$mail->msgHTML($message);
				
				$mail->send();
				$mail->ClearAllRecipients(); 
				$mail->ClearAttachments();
	}
}
?>