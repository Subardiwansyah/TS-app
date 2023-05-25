<?php
// POST values
$token= 'cdLeYoQ6w1GDrHPIS99p02:APA91bEUL4GWueP7b2YTivThaG3g4v9IDP7Rj4f6RUfY89iXS_j7r1z1oGcjQHc7e7V-wEvW1gZZtosODzpsKror2GynSfpIno6OAHiFNKMDRxbOK1rZDYmAD0xjaqHZw1Y-2Gyz0jCI';
$title= 'title';
$message= 'message';
$postlink= 'https://emka.web.id/tutorial/cara-mengirim-notifikasi-push-seluler-dengan-php-dan-firebase/';

$token = htmlspecialchars($token,ENT_COMPAT);
$title = htmlspecialchars($title,ENT_COMPAT);
$message = htmlspecialchars($message,ENT_COMPAT);
$postlink = htmlspecialchars($postlink,ENT_COMPAT);

// Push Data's
$data = array(
"to" => "$token",
"notification" => array( 
"title" => "$title", 
"body" => "$message", 
"icon" => "https://avatars2.githubusercontent.com/u/52190236?s=460&u=b5599a497d334f1edf4c2be8df4bd4d8f2a44e54&v=4", // Replace https://example.com/icon.png with your PUSH ICON URL
"click_action" => "$postlink")
);

// Print Output in JSON Format
$data_string = json_encode($data); 
     
// FCM API Token URL
$url = "https://fcm.googleapis.com/fcm/send";

//Curl Headers
$headers = array
(
     'Authorization: key=AAAAQwEdP-Y:APA91bGvHrvW6LOrXpedmSFHznPRc4unbt4119JWH8CzswUK8tfExokD4z4HNdyrQwLBfQrUAaZSOtUQ_IGVnxDI3_OR8OFZeiTJfQDSYQkXmLv2cDLx5Ix17fttcc8q5zAXF2jhtjlq', 
     'Content-Type: application/json'
);  

$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, $url);                                                                 
curl_setopt($ch, CURLOPT_POST, 1);  
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string);                                                                  
                                                                                                                     
// Variable for Print the Result
$result = curl_exec($ch);
echo $result;
curl_close ($ch);
?>