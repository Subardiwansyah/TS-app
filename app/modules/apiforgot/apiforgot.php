<?php

helper('registrasi');
$setting_register = get_setting_registrasi();
$data = array();
$data['status'] = false;

if(empty($_POST['action'])){
	$emailAddress = $_POST['emailaddress'];
	//$emailAddress = 'subardi.wansyah@gmail.com';

	$data['email'] = $emailAddress;

	if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
		$data['mssg'] = "Invalid email format";
	} else {
		$cek = $db->query('SELECT * FROM user WHERE email = "'.$emailAddress.'" ')->getRowArray();
		if(empty($cek)){
			$data['mssg'] = "Email does not exist";
		} else {
			$data['status'] = true;
			$data['id_user'] = $cek['id_user'];
			$id_user = $cek['id_user'];
			$send_email = send_confirm_email($id_user, $emailAddress, 'recovery');
			
		}
	}
} else if($_POST['action'] == 'RESEND'){
	$emailAddress 	= $_POST['emailaddress'];
	$id_user		= $_POST['id_user'];
	$data['status'] = true;
	$send_email = send_confirm_email($id_user, $emailAddress, 'recovery');
} else if($_POST["action"] == "CHANGE_EMAIL"){
	$email 			= $_POST['email'];
	$id_user		= $_POST['id_user'];

	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$data['mssg'] = "Invalid email format";
	} else {
		$cek = $db->query('SELECT * FROM user WHERE email = "'.$email.'" ')->getRowArray();
		if(!empty($cek)){
			$data['mssg'] = "Email already exist.";
		} else {
			$data['status'] = true;
			$send_email = send_confirm_email($id_user, $email, 'emailchange');
		}
	}

}

echo json_encode($data);

function send_confirm_email($id_user, $emailAddress, $action){
	global $app_auth, $db, $config;
	$token = $app_auth->generateDbToken();
	$pin_token = mt_rand(100000,999999);
	$data_db = [];
	$data_db['selector'] = $token['selector'];
	$data_db['token'] = $token['db'];
	$data_db['pin_token'] = $pin_token;
	$data_db['action'] 	= $action;
	$data_db['id_user'] = $id_user;
	$data_db['created'] = date('Y-m-d H:i:s');
	$data_db['expires'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
	
	$insert_token = $db->insert('user_token', $data_db);
	
	helper('email');
	helper('registrasi');
	$setting_web = get_setting_web();

	$url_token = $token['selector'] . ':' . $token['external'];
	$url = $config['base_url'].'register/confirm?token='.$url_token;
	$email_content = str_replace('{{NAME}}'
								, ""
								, email_pin_content()
							);
	
	$email_content = str_replace('{{url}}', $url, $email_content);
	$email_content = str_replace('{{PIN}}', $pin_token, $email_content);
	$email_content = str_replace('{{JUDUL_WEB}}', $setting_web['judul_web'], $email_content);
	$email_content = str_replace('{{EMAIL_SUPPORT}}', $setting_web['email_support'], $email_content);
	$email_content = str_replace('{{COMPANY}}', $setting_web['company'], $email_content);
	
	require_once 'app/config/email.php';
	$email_config = new EmailConfig;
	$email_data = array('from_email' => $config['email_support']
					, 'from_title' => $setting_web['company']
					, 'to_email' => $emailAddress
					, 'to_name' => 'Bardi'
					, 'email_subject' => 'Link Activation Forgot Account'
					, 'email_content' => $email_content
					, 'images' => ['logo_text' => BASEPATH . 'public/images/'.$setting_web['logo_login']]
	);
	
	require_once('app/libraries/SendEmail.php');

	$emaillib = new \App\Libraries\SendEmail;
	$emaillib->init();
	$emaillib->setProvider($email_config->provider);
	$send_email =  $emaillib->send($email_data);
	
	return $send_email;
}