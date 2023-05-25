<?php

include "config.php";
$data = array();
$data['status'] = false;

$phone = $_POST['phone'];

$sql = "SELECT * FROM user WHERE phone = '".$phone."' ";
$cek = mysqli_fetch_array(mysqli_query($conn, $sql));

if(!empty($cek)){
    
    $data['status'] = true;
    $data['id_user'] = $cek['id_user'];
    $selector = uniqid();
	$token = md5(date('Y-m-d H:i:s'));
	$pin_token = mt_rand(100000,999999);
	$sqls = "INSERT INTO user_token (selector,token,pin_token,action,id_user,created,expires) VALUES ('".$selector."','".$token."','".$pin_token."','phone-forgot','".$cek['id_user']."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s', strtotime('+1 hour'))."')";
	
	$insert_token_sms = mysqli_query($conn, $sqls);
	
	send_sms($phone, $pin_token);
	
    
} else {
    $data['mssg'] = "Phone Number does not exist.";
}

function send_sms($phone, $otp){
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'https://api.nusasms.com/api/v3/sendsms/plain',
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => array(
			'user' => 'ptkims_api',
			'password' => 'Kharisma2021!',
			'SMSText' => 'KODE OTP: '.$otp,
			'GSM' => $phone,
			'otp' => 'Y',
			'output' => 'json'
		)
	));
	$resp = curl_exec($curl);
	
	curl_close($curl);
}

echo json_encode($data);

