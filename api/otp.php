<?php 
include "config.php";
$data = array();
$data['status'] = false;
$id_user 		= $_POST['id_user'];
$pin 		= $_POST['pin'];

$sql = "SELECT * FROM user_token WHERE pin_token = '".$pin."' and id_user = '".$id_user."' and action = 'register' ";
$cek_token = mysqli_fetch_array(mysqli_query($conn, $sql));

if(!empty($cek_token)){
	$data['status'] = true;
	$cek 		= "SELECT * FROM user WHERE id_user = '".$id_user."' ";
	$result = mysqli_fetch_array(mysqli_query($conn, $cek));
    
    //$update = mysqli_query($conn,"UPDATE user SET verified = '1' WHERE id_user = '".$id_user."' ");
    
	$selector = uniqid();
	$token = md5(date('Y-m-d H:i:s'));
	$pin_token = mt_rand(100000,999999);
	$sqls = "INSERT INTO user_token (selector,token,pin_token,action,id_user,created,expires) VALUES ('".$selector."','".$token."','".$pin_token."','phone','".$id_user."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s', strtotime('+1 hour'))."')";
	
	$insert_token_sms = mysqli_query($conn, $sqls);
	
	send_sms($result['phone'], $pin_token);
	
	$data['mssg']	= "Berhasil";
	$data['id']		= $result['id_user'];
	$data['nama']	= $result['nama'];
	$data['phone']	= $result['phone'];
	$data['email']	= $result['email'];
	$data['address']	= $result['address'];
	
} else {
	$data['mssg'] = "OTP Code not valid.";
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
